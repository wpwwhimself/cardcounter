@extends("layouts.shipyard.admin")
@section("title", "Pasjans Pająk")
@section("subtitle", "Gry")

@section("content")

@php
$color_count = request("colors", 1);
@endphp

<div id="playmat" class="flex down">
    <x-deck :mode="'spider-' . $color_count" />

    <div class="grid but-mobile-down" style="grid-template-columns: auto auto 1fr;">
        <x-shipyard.app.card class="deck"
            inner-class="flex right center middle nowrap"
        >
            <div class="card-tray compact" onclick="dropNewLine();"></div>
        </x-shipyard.app.card>

        <x-shipyard.app.card class="buttons" inner-class="flex down but-mobile-right center middle no-gap">
            <x-game-stats.timer />

            <x-shipyard.ui.button
                icon="restart"
                pop="Od nowa"
                :action="route('games.spider')"
                class="danger"
            />
            <x-shipyard.ui.button
                icon="undo"
                pop="Cofnij"
                action="none"
                onclick="undo();"
                class="tertiary"
            />
        </x-shipyard.app.card>

        <x-shipyard.app.card class="final-holder"
            inner-class="flex right center middle nowrap"
        >
            @for ($i = 1; $i <= 8; $i++)
            <div class="card-tray compact" data-index="{{ $i }}"></div>
            @endfor
        </x-shipyard.app.card>
    </div>

    <x-shipyard.app.card class="table"
        inner-class="flex right center middle nowrap"
    >
        @for ($i = 1; $i <= 10; $i++)
        <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToTable(event);" ondragover="event.preventDefault();"></div>
        @endfor
    </x-shipyard.app.card>
</div>

@endsection

@section("prepends")
<script>
function init() {
    const modal = reuseModal();

    modal.modal.classList.remove("hidden");
    modal.loader.classList.remove("hidden");
    fetch(`/api/game-stats/start`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({
            game: "spider",
        }),
    })
        .then(res => res.json())
        .finally(() => {
            modal.modal.classList.add("hidden");
            modal.loader.classList.add("hidden");

            // put cards in the deck
            const deck = document.querySelector(`#playmat .deck .card-tray`);
            getAllCards().forEach((card, i) => {
                moveCard(card, deck);
            });

            // initial population
            getAllCards().forEach((card, i) => {
                if (i >= 54) return;

                const tray = document.querySelector(`#playmat .table .card-tray[data-index="${i % 10 + 1}"]`);
                moveCard(card, tray);
            });

            // reveal top cards
            document.querySelectorAll(`#playmat .table .card-tray`).forEach(tray => {
                checkTopCardVisible(tray);
            });

            window.gameHistory = [];
            startTimer();
        });
}

function finish() {
    const time_elapsed = stopTimer();

    const modal = reuseModal();

    modal.modal.classList.remove("hidden");
    modal.loader.classList.remove("hidden");
    fetch(`/api/game-stats/finish`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({
            game: "spider",
            time: time_elapsed,
        }),
    })
        .then(res => res.json())
        .then(res => {
            modal.loader.classList.add("hidden");
            modal.card.classList.remove("hidden");
            modal.header.textContent = "Gratulacje!";
            modal.contents.innerHTML = res.modal;
        });
}

function log(card, target) {
    window.gameHistory.push({
        card: card,
        from: card.closest(".card-tray"),
        to: target,
    });
}

//? 🦺 validators 🦺 ?//
function cardsCanBeStackedOnTable(upper_card, lower_card) {
    const upper_data = getCardValue(upper_card);
    const lower_data = getCardValue(lower_card);

    if (!lower_card || !upper_card) return true;

    return upper_data.rank == lower_data.rank - 1;
}
//? 🦺 validators 🦺 ?//

//? 🥪 stacks 🥪 ?//
function dropCardToTable(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#${ev.dataTransfer.getData("card")}`);
    const tray = ev.target.closest(".card-tray");
    const original_tray = card.closest(".card-tray");

    if (card.closest(".playing-card").classList.contains("reversed")) return;

    moveCardStackToTable(card, tray);
    checkTopCardVisible(original_tray);
    tryCollectTray(tray);
}

function moveCard(card, tray) {
    const original_tray = card.closest(".card-tray");

    tray.appendChild(card);
    cleanUpStack(tray);
    cleanUpStack(original_tray);
}

function moveCardStackToTable(card, tray) {
    let cards_to_move = [];
    let card_cursor = card;
    while (card_cursor) {
        if (!cardsCanBeStackedOnTable(card_cursor.nextSibling, card_cursor)
            || (card_cursor.nextSibling && getCardValue(card_cursor).color != getCardValue(card_cursor.nextSibling)?.color)
        ) {
            popToast("error", "Nie możesz przenieść tej karty z tego poziomu.");
            return;
        }
        cards_to_move.push(card_cursor);
        card_cursor = card_cursor.nextSibling;
    }

    if (!cardsCanBeStackedOnTable(card, getTopCardFromStack(tray))) {
        popToast("error", "Nie możesz przenieść tutaj tej karty.");
        return;
    }

    log(card, tray);
    cards_to_move.forEach(c => {
        moveCard(c, tray);
    });
}
//? 🥪 stacks 🥪 ?//

//? 🏃 basic actions 🏃 ?//
function dropNewLine() {
    const deck = document.querySelector(`#playmat .deck .card-tray`);
    if (deck.children.length == 0) return;

    document.querySelectorAll(`#playmat .table .card-tray`).forEach(tray => {
        moveCard(getTopCardFromStack(deck), tray);
        checkTopCardVisible(tray);
        tryCollectTray(tray);
    })
}

function checkTopCardVisible(tray) {
    const card = getTopCardFromStack(tray);
    card?.classList.remove("reversed");
}

function tryCollectTray(tray) {
    const top_card = getTopCardFromStack(tray);
    if (getCardValue(top_card).rank != 1) return;

    let cards_to_move = [];
    let card_cursor = top_card;
    while (card_cursor) {
        cards_to_move.push(card_cursor);
        if (getCardValue(card_cursor).color != getCardValue(card_cursor.previousSibling)?.color
            || (getCardValue(card_cursor).rank + 1) != getCardValue(card_cursor.previousSibling)?.rank
        ) break;
        card_cursor = card_cursor.previousSibling;
    }

    if (cards_to_move.length < 13) return;

    const first_free_holder = Array.from(document.querySelectorAll(`#playmat .final-holder .card-tray`)).find(holder => holder.children.length == 0);
    cards_to_move.forEach(c => {
        moveCard(c, first_free_holder);
    });

    checkTopCardVisible(tray);
    checkWinCondition();
}

function checkWinCondition() {
    const completed_final_holders =
        Array.from(document.querySelectorAll(`#playmat .final-holder .card-tray`))
            .reduce((completed, holder) => completed + (holder.children.length == 13), 0);
    if (completed_final_holders == 8) {
        finish();
    }
}
//? 🏃 basic actions 🏃 ?//

//? 🛟 helpers 🛟 ?//
function undo() {
    if (window.gameHistory.length == 0) {
        popToast("error", "To już początek.");
        return;
    }

    const action = window.gameHistory.pop();

    let cards_to_move = [];
    let card_cursor = action.card;
    while (card_cursor) {
        if (!cardsCanBeStackedOnTable(card_cursor.nextSibling, card_cursor)) {
            popToast("error", "Nie możesz przenieść tej karty z tego poziomu.");
            return;
        }
        cards_to_move.push(card_cursor);
        card_cursor = card_cursor.nextSibling;
    }

    cards_to_move.forEach(c => {
        moveCard(c, action.from);
    });
}
//? 🛟 helpers 🛟 ?//
</script>
@endsection

@section("appends")
<script defer>
document.addEventListener("DOMContentLoaded", () => {
    init();
});

document.addEventListener("contextmenu", (ev) => {
    ev.preventDefault();
});
</script>
@endsection
