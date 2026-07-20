<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::controller(GameController::class)->group(function () {
    foreach (GameController::GAME_META as $game => $meta) {
        Route::view($game, "pages.games.$game")->name("games.$game");
    }

    Route::view("", "pages.index")->name("home");
});
