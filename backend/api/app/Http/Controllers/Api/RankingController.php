<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        $ranking = QuizAttempt::select('user_id', DB::raw('MAX(score) as best_score'), DB::raw('MIN(time_seconds) as best_time'))
            ->whereNotNull('completed_at')
            ->groupBy('user_id')
            ->orderBy('best_score', 'desc')
            ->orderBy('best_time', 'asc')
            ->with('user:id,name')
            ->limit(10)
            ->get()
            ->map(function ($attempt, $index) {
                return [
                    'position' => $index + 1,
                    'user' => ['name' => $attempt->user->name ?? '—'],
                    'score' => $attempt->best_score,
                    'time_seconds' => $attempt->best_time,
                ];
            });

        return response()->json($ranking);
    }
}
