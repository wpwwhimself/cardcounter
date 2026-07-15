@extends("layouts.shipyard.admin")
@section("title", "FreeCell")
@section("subtitle", "Gry")

@section("content")

<div id="playmat" class="flex down">
    <x-deck />

    <div class="grid but-mobile-down" style="grid-template-columns: 1fr auto 1fr;">
        <x-shipyard.app.card class="holder"
            inner-class="flex right center middle nowrap"
        >
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray compact" data-index="{{ $i }}" ondrop="dropCardToHolder(event);" ondragover="event.preventDefault();"></div>
            @endfor
        </x-shipyard.app.card>

        <x-shipyard.app.card class="buttons" inner-class="flex down but-mobile-right center middle no-gap">
            <x-game-stats.timer />

            <x-shipyard.ui.button
                icon="restart"
                pop="Od nowa"
                :action="route('games.freecell')"
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
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray compact" data-index="{{ $i }}" ondrop="dropCardToFinalHolder(event);" ondragover="event.preventDefault();"></div>
            @endfor
        </x-shipyard.app.card>
    </div>

    <x-shipyard.app.card class="table"
        inner-class="flex right center middle nowrap"
    >
        @for ($i = 1; $i <= 8; $i++)
        <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToTable(event);" ondragover="event.preventDefault();"></div>
        @endfor
    </x-shipyard.app.card>
</div>

@endsection

@section("prepends")
<script>
function init() {
    toggleBigLoader();
    fetchWithUser(`/api/game-stats/start`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            game: "freecell",
        }),
    })
        .then(res => res.json())
        .finally(() => {
            toggleBigLoader();

            // put cards on the table
            getAllCards().forEach((card, i) => {
                const tray = document.querySelector(`#playmat .table .card-tray[data-index="${i % 8 + 1}"]`);
                moveCard(card, tray);
            });

            window.gameHistory = [];
            startTimer();
        });
}

//? 🦺 validators 🦺 ?//
function cardsCanBeStackedOnTable(upper_card, lower_card) {
    const upper_data = getCardValue(upper_card);
    const lower_data = getCardValue(lower_card);

    if (!lower_card || !upper_card) return true;

    return upper_data.color % 2 != lower_data.color % 2
        && upper_data.rank == lower_data.rank - 1;
}

function cardsCanBeStackedOnFinalHolder(upper_card, lower_card) {
    const upper_data = getCardValue(upper_card);
    const lower_data = getCardValue(lower_card);

    if (!upper_card) return false;
    if (!lower_card) return (upper_data.rank == 1);

    return upper_data.color == lower_data.color
        && upper_data.rank == lower_data.rank + 1;
}

function tooManyCardsInStack(cards, target_tray) {
    const cards_to_move = cards.length;

    const free_holders = Array.from(document.querySelectorAll(`#playmat .holder .card-tray`))
        .filter(tray => tray.children.length == 0)
        .length;
    const free_trays = Array.from(document.querySelectorAll(`#playmat .table .card-tray`))
        .filter(tray => tray.children.length == 0)
        .length
        - (target_tray.children.length === 0); // exclude one tray if it is the target
    const max_stack_size = Math.pow(2, free_trays) * (free_holders + 1);

    return cards_to_move > max_stack_size;
}
//? 🦺 validators 🦺 ?//

//? 🥪 stacks 🥪 ?//
function dropCardToTable(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#${ev.dataTransfer.getData("card")}`);
    const tray = ev.target.closest(".card-tray");

    moveCardStackToTable(card, tray);
}

function moveCard(card, tray) {
    const original_tray = card.closest(".card-tray");

    tray.appendChild(card);
    cleanUpStack(tray);
    cleanUpStack(original_tray);

    // check win condition
    const completed_final_holders =
        Array.from(document.querySelectorAll(`#playmat .final-holder .card-tray`))
            .reduce((completed, holder) => completed + (holder.children.length == 13), 0);
    if (completed_final_holders == 4) {
        finish("freecell");
    }
}

function moveCardStackToTable(card, tray) {
    let cards_to_move = [];
    let card_cursor = card;
    while (card_cursor) {
        if (!cardsCanBeStackedOnTable(card_cursor.nextSibling, card_cursor)) {
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

    if (tooManyCardsInStack(cards_to_move, tray)) {
        popToast("error", "Próbujesz przenieść zbyt wiele kart.");
        return;
    }

    log(card, tray);
    cards_to_move.forEach(c => {
        moveCard(c, tray);
    });
}
//? 🥪 stacks 🥪 ?//

//? ⚓ holders ⚓ ?//
function dropCardToHolder(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#${ev.dataTransfer.getData("card")}`);
    const holder = ev.target.closest(".card-tray");

    if (holder.children.length > 0) {
        popToast("error", "Pole jest zajęte.");
        return;
    }

    if (card.nextSibling) {
        popToast("error", "Nie możesz przenieść tej karty z tego poziomu.");
        return;
    }

    log(card, holder);
    moveCard(card, holder);
}

function dropCardToFinalHolder(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#${ev.dataTransfer.getData("card")}`);
    const holder = ev.target.closest(".card-tray");

    if (!cardsCanBeStackedOnFinalHolder(card, getTopCardFromStack(holder))) {
        popToast("error", "Ta karta tu nie pasuje.");
        return;
    }

    if (card.nextSibling) {
        popToast("error", "Nie możesz przenieść tej karty z tego poziomu.");
        return;
    }

    log(card, holder);
    moveCard(card, holder);
}
//? ⚓ holders ⚓ ?//

//? 🛟 helpers 🛟 ?//
function collectAll() {
    let none_collected = true;

    while(collectOne()) {
        none_collected = false;
    }

    if (none_collected) {
        popToast("info", "Nie ma kart do przeniesienia.");
    }
}

function collectOne() {
    let collected = false;

    Array.from(document.querySelectorAll(`#playmat .table .card-tray, #playmat .holder .card-tray`)).forEach(tray => {
        const top_card = getTopCardFromStack(tray);
        Array.from(document.querySelectorAll(`#playmat .final-holder .card-tray`)).forEach(holder => {
            if (!cardsCanBeStackedOnFinalHolder(top_card, getTopCardFromStack(holder))) return;
            if (top_card.closest(".card").classList.contains("final-holder")) return;

            log(top_card, holder);
            moveCard(top_card, holder);
            collected = true;
        });
    });

    return collected;
}

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

getAllCards().forEach(card => {
    card.classList.toggle("reversed");
});

document.addEventListener("contextmenu", (ev) => {
    ev.preventDefault();
    collectAll();
});
</script>
@endsection
