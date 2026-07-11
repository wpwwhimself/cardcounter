@props([
    "id",
    "value",
    "mono" => false,
])

@php
$rank = Str::after($value, '-');
$color = Str::before($value, '-');
$colors = [
    "A" => "miecze",
    "B" => "kielichy",
    "C" => "berła",
    "D" => "monety",
    "E" => "taroty",
];

$color_name = $colors[$color];
if ($mono && $color != "D") {
    $color_name .= "_mono";
}

$card_path = "https://raw.githubusercontent.com/wpwwhimself/tarot-deck/master/kompletandos/pdftoimage/";
$card_path .= "c$color_name/c$color_name-$rank.jpg";
@endphp

<div class="playing-card interactive reversed" id="pc-{{ $id }}" data-value="{{ $value }}" draggable="true" ondragstart="grabCard(event);">
    <img src="{{ asset('media/rewers.jpg') }}" alt="back" class="back">
    <img src="{{ asset($card_path) }}" alt="front" class="front">
</div>
