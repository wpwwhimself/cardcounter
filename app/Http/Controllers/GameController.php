<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    #region game stats
    private function updateGameStats($user, $game, $mode)
    {
        $stats = $user->game_stats ?? [];
        $stats[$game] ??= [
            "started" => 0,
            "finished" => 0,
        ];
        $stats[$game][$mode]++;

        $user->update(["game_stats" => $stats]);
    }

    public function gameStatStart(Request $rq): JsonResponse
    {
        $res = [
            "status" => 1,
            "details" => "Gamer is anonymous.",
        ];

        if (Auth::guest()) return response()->json($res);

        $this->updateGameStats(Auth::user(), $rq->game, "started");

        $res["status"] = 0;
        $res["details"] = Auth::user();

        return response()->json($res);
    }

    public function gameStatFinish(Request $rq): JsonResponse
    {
        if (!Auth::guest()) {
            $this->updateGameStats(Auth::user(), $rq->game, "finished");
        }

        $res = [
            "status" => 0,
            "details" => Auth::user(),
            "modal" => view("components.game-stats.game-finished", [
                "user" => Auth::user(),
                "game" => $rq->game,
            ])->render(),
        ];

        return response()->json($res);
    }
    #endregion
}
