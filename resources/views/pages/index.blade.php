@extends("layouts.shipyard.admin")
@section("title", "Gry")

@section("content")

<div class="grid but-mobile-down" style="--col-count: 3;">
    <x-shipyard.app.card title="FreeCell">
        <x-slot:actions>
            <x-shipyard.ui.button
                icon="arrow-right"
                label="Graj"
                :action="route('games.freecell')"
                class="primary"
            />
        </x-slot:actions>
    </x-shipyard.app.card>
</div>

@endsection
