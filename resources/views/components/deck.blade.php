@props([
    "mode" => "standard",
])

@php
$all_values = $standard_values = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14"];
array_splice($standard_values, 10, 1);

$decks = [
    "standard" => collect(["A", "B", "C", "D"])
        ->crossJoin($standard_values),
    "spider-1" => collect(array_fill(0, 8, Arr::random(["A", "B", "C", "D"])))
        ->crossJoin($standard_values),
    "spider-2" => collect(array_merge(array_fill(0, 4, Arr::random(["A", "C"])), array_fill(0, 4, Arr::random(["B", "D"]))))
        ->crossJoin($standard_values),
    "spider-4" => collect(["A", "A", "B", "B", "C", "C", "D", "D"])
        ->crossJoin($standard_values),
];
$deck = $decks[$mode]
    ->map(fn ($d) => implode('-', $d))
    ->shuffle();
@endphp

@foreach ($deck as $id => $card)
<x-card :id="$id" :value="$card" :mono="in_array($mode, ['standard'])" />
@endforeach
