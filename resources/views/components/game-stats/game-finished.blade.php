@props([
    "user",
    "game",
])

@php
$meta = \App\Http\Controllers\GameController::GAME_META[$game];
@endphp

<div class="flex down">
    <p>Wygrałeś grę <strong class="accent primary">{{ $meta["name"] }}</strong>.</p>
    
    @if ($user)
    <p>Twoje statystyki:</p>
    <ul>
        <li>Rozpoczęte partie: {{ $user->game_stats[$game]["started"] }}</li>
        <li>Ukończone partie: {{ $user->game_stats[$game]["finished"] }}</li>
        <li>Najlepszy czas: {{ $user->game_stats[$game]["top_time"] }}</li>
    </ul>
    @endif

    <div class="flex right spread and-cover">
        <x-shipyard.ui.button
            icon="restart"
            label="Zagraj ponownie"
            action="none"
            onclick="window.location.reload();"
            class="primary"
        />
        <x-shipyard.ui.button
            icon="arrow-left"
            label="Wróć"
            :action="route('home')"
        />
    </div>
</div>