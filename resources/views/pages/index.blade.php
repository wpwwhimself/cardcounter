@extends("layouts.shipyard.admin")
@section("title", "Gry")

@section("content")

<div class="grid but-mobile-down" style="--col-count: 3;">
    <x-shipyard.app.card title="FreeCell"
        icon="crop-free"
        inner-class="flex right spread and-cover"
    >
        <x-shipyard.ui.button
            icon="arrow-right"
            label="Graj"
            :action="route('games.freecell')"
            class="primary"
        />
    </x-shipyard.app.card>

    <x-shipyard.app.card title="Pasjans Pająk"
        icon="spider"
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
