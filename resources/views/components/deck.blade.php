@props([
    "mode" => "standard",
])

@php
$decks = [
    "standard" => collect(["A", "B", "C", "D"])
        ->crossJoin(["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "12", "13", "14"])
        ->map(fn ($d) => implode('-', $d)),
];
@endphp

@foreach ($decks[$mode]->shuffle() as $card)
<x-card :id="$card" />
@endforeach
