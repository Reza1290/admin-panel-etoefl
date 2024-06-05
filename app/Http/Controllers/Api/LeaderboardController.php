<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameAnswer;
use App\Models\Leaderboard;
use App\Models\pairingClaim;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\ScrambledClaim;
use App\Models\SpeakingClaim;
use App\Models\SynonymClaim;
use App\Models\TenseClaim;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class LeaderboardController extends Controller
{
    public function index()
    {
        try {
            $scores = Leaderboard::with('user')->orderBy('total_score', 'desc')->take(10)->get();
            $me = auth()->user();
            $myScore = Leaderboard::where('user_id', $me->id)->first();

            return response()->json([
                'data' => [
                    'user' => $me,
                    'my_score' => $myScore,
                    'top_scores' => $scores
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving scores. Please try again later.'], 500);
        }
    }

    public function updateUserScores()
    {
        $user = auth()->user();
        $currentYear = now()->year;
        $currentMonth = now()->month;
            $game_score = GameAnswer::whereHas('claim', function ($query) {
                $query->where('is_completed', true);
            })->where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('score');

            $quiz_score = QuizAnswer::whereHas('claim', function ($query) {
                $query->where('is_completed', true);
            })->where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('score');

            $synonym_score = SynonymClaim::where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('score');

            $scrambled_score = ScrambledClaim::where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('is_true', true)
                ->count();

            $tense_score = TenseClaim::where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('is_true', true)
                ->count();

            $speaking_score = SpeakingClaim::where('user_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('score');

            $total_score = $game_score + $quiz_score + $synonym_score + $scrambled_score + $tense_score + $speaking_score;

            Leaderboard::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_score' => $total_score,
                    'game_score' => $game_score,
                    'quiz_score' => $quiz_score,
                    'synonym_score' => $synonym_score,
                    'scrambled_score' => $scrambled_score,
                    'tense_score' => $tense_score,
                    'speaking_score' => $speaking_score,
                ]
            );
        }
    
}
