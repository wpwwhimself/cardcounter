<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

if (file_exists(__DIR__.'/Shipyard/shipyard.php')) require __DIR__.'/Shipyard/shipyard.php';

Route::controller(GameController::class)->group(function () {
    Route::view("freecell", "pages.games.freecell")->name("games.freecell");
    Route::view("spider", "pages.games.spider")->name("games.spider");

    Route::view("", "pages.index")->name("home");

    Route::prefix("api/game-stats")->group(function () {
        Route::post("start", "gameStatStart");
        Route::post("finish", "gameStatFinish");
    });
});
