@props([
    "id",
])

@php
$rank = Str::after($id, '-');
$color = Str::before($id, '-');
$colors = [
    "A" => "miecze",
    "B" => "kielichy",
    "C" => "berła",
    "D" => "monety",
    "E" => "taroty",
];

$card_path = "https://raw.githubusercontent.com/wpwwhimself/tarot-deck/master/kompletandos/pdftoimage/";
$card_path .= "c$colors[$color]/c$colors[$color]-$rank.jpg";
@endphp

<div class="playing-card interactive reversed" data-value="{{ $id }}" draggable="true" ondragstart="grabCard(event);">
    <img src="{{ asset('media/rewers.jpg') }}" alt="back" class="back">
    <img src="{{ asset($card_path) }}" alt="front" class="front">
</div>