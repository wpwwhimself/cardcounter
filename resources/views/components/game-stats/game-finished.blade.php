@props([
    "user",
    "game",
])

<div class="flex down">
    <p>Wygrałeś grę <strong class="accent primary">{{ $game }}</strong>.</p>
    
    @if ($user)
    <p>Twoje statystyki:</p>
    <ul>
        <li>Rozpoczęte partie: {{ $user->game_stats[$game]["started"] }}</li>
        <li>Ukończone partie: {{ $user->game_stats[$game]["finished"] }}</li>
        <li>Najlepszy czas: {{ $user->game_stats[$game]["top_time"] }}</li>
    </ul>
    @endif

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