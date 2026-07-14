@extends("layouts.shipyard.admin")
@section("title", "Gry")

@section("content")

<div class="grid but-mobile-down" style="--col-count: 3;">
    @php
    $meta = \App\Http\Controllers\GameController::GAME_META["freecell"];
    @endphp
    <x-shipyard.app.card :title="$meta['name']"
        :icon="$meta['icon']"
        inner-class="flex right spread and-cover"
    >
        <x-shipyard.ui.button
            icon="arrow-right"
            label="Graj"
            :action="route('games.freecell')"
            class="primary"
        />
    </x-shipyard.app.card>

    @php
    $meta = \App\Http\Controllers\GameController::GAME_META["spider"];
    @endphp
    <x-shipyard.app.card :title="$meta['name']"
        :icon="$meta['icon']"
        inner-class="flex right spread and-cover"
    >
        @foreach ([
            ["1 kolor", 1],
            ["2 kolory", 2],
            ["4 kolory", 4],
        ] as [$diff_label, $colors])
        <x-shipyard.ui.button
            icon="arrow-right"
            :label="$diff_label"
            :action="route('games.spider', ['colors' => $colors])"
            class="primary"
        />
        @endforeach
    </x-shipyard.app.card>
</div>

@endsection
