@props([
    "user",
])

<x-shipyard::app.section title="Statystyki gier"
    icon="abacus"
    inner-class="grid but-mobile-down"
    inner-style="--col-count: 3;"
>
    @foreach ($user->game_stats as $game => $stats)
    @continue (empty($game))
    @php
    $meta = \App\Http\Controllers\GameController::GAME_META[$game];
    @endphp
    <x-shipyard::app.card :title="$meta['name']"
        :icon="$meta['icon']"
    >
        <table>
            <thead>
                <tr>
                    <th>Rozpoczęte</th>
                    <th>Ukończone</th>
                    <th>Najlepszy czas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $stats["started"] }}</td>
                    <td>{{ $stats["finished"] }}</td>
                    <td>{{ $stats["top_time"] ?? "—" }}</td>
                </tr>
            </tbody>
        </table>
    </x-shipyard::app.card>
    @endforeach
</x-shipyard::app.section>
