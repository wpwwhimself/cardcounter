@extends("layouts.shipyard.admin")
@section("title", "FreeCell")
@section("subtitle", "Gry")

@section("content")

<div id="playmat" class="flex down">
    <x-deck />

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-shipyard.app.card class="holder"
            inner-class="flex right center middle nowrap"
        >
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToHolder(event);" ondragover="event.preventDefault();"></div>
            @endfor
        </x-shipyard.app.card>

        <x-shipyard.app.card class="final-holder"
            inner-class="flex right center middle nowrap"
        >
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToFinalHolder(event);" ondragover="event.preventDefault();"></div>
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
    // put cards on the table
    getAllCards().forEach((card, i) => {
        const tray = document.querySelector(`#playmat .table .card-tray[data-index="${i % 8 + 1}"]`);
        moveCardToTable(card, tray);
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

    if (!lower_card || !upper_card) return true;

    return upper_data.color == lower_data.color
        && upper_data.rank == lower_data.rank + 1;
}

function tooManyCardsInStack(cards) {
    const cards_to_move = cards.length;
    const free_holders = Array.from(document.querySelectorAll(`#playmat .holder .card-tray`))
        .filter(tray => tray.children.length == 0)
        .length;

    return cards_to_move > free_holders;
}
//? 🦺 validators 🦺 ?//

//? 🥪 stacks 🥪 ?//
function dropCardToTable(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#playmat .playing-card[data-value="${ev.dataTransfer.getData("card")}"]`);
    const tray = ev.target.closest(".card-tray");

    moveCardStackToTable(card, tray);
}

function moveCardToTable(card, tray) {
    const original_tray = card.closest(".card-tray");

    tray.appendChild(card);
    spreadStack(tray);
    spreadStack(original_tray);
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

    if (tooManyCardsInStack(cards_to_move)) {
        popToast("error", "Próbujesz przenieść zbyt wiele kart.");
        return;
    }

    cards_to_move.forEach(c => {
        moveCardToTable(c, tray);
    });
}
//? 🥪 stacks 🥪 ?//

//? ⚓ holders ⚓ ?//
function dropCardToHolder(ev) {
    ev.preventDefault();
    const card = document.querySelector(`#playmat .playing-card[data-value="${ev.dataTransfer.getData("card")}"]`);
    const tray = ev.target.closest(".card-tray");

    moveCardToHolder(card, tray);
}

function moveCardToHolder(card, holder) {
    const original_tray = card.closest(".card-tray");

    if (holder.children.length > 0) {
        popToast("error", "Pole jest zajęte.");
        return;
    }

    holder.appendChild(card);
    spreadStack(holder);
    spreadStack(original_tray);
}
//? ⚓ holders ⚓ ?//
</script>
@endsection

@section("appends")
<script defer>
init();

getAllCards().forEach(card => {
    card.classList.toggle("reversed");
});
</script>
@endsection
