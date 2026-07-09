@extends("layouts.shipyard.admin")
@section("title", "FreeCell")
@section("subtitle", "Gry")

@section("content")

<div id="playmat" class="flex down">
    <x-deck />

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-shipyard.app.card class="holder"
            inner-class="flex right center middle no-wrap"
        >
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToHolder(event);" ondragover="event.preventDefault();"></div>
            @endfor
        </x-shipyard.app.card>

        <x-shipyard.app.card class="final-holder"
            inner-class="flex right center middle no-wrap"
        >
            @for ($i = 1; $i <= 4; $i++)
            <div class="card-tray" data-index="{{ $i }}" ondrop="dropCardToFinalHolder(event);" ondragover="event.preventDefault();"></div>
            @endfor
        </x-shipyard.app.card>
    </div>

    <x-shipyard.app.card class="table"
        inner-class="flex right center middle no-wrap"
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
        cards_to_move.push(card_cursor);
        card_cursor = card_cursor.nextSibling;
    }
    cards_to_move.forEach(c => {
        moveCardToTable(c, tray);
    });
}
//? 🥪 stacks 🥪 ?//

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