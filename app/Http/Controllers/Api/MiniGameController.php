<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameHistory;
use App\Models\ScrambledClaim;
use App\Models\SpeakingClaim;
use App\Models\SynonymClaim;
use App\Models\TenseClaim;
use App\Models\User;
use App\Models\Word;
use Exception;
use Illuminate\Http\Request;

class MiniGameController extends Controller
{
    public function storePairingGames(Request $request){
            try{
                $request->validate([
                    'synonym_words' => 'array|required',
                    'score' => 'required',
                ]);
    
                $user = auth()->user();
                $synonymClaim = new SynonymClaim();
    
    
                $synonymClaim->synonym_words = $request->synonym_words;
                $synonymClaim->score = $request->score;
                $synonymClaim->user_id = $user->_id;
    
                $synonymClaim->save();
                app(LeaderboardController::class)->updateUserScores();
                GameHistory::create([
                    'user_id' => $user->id,
                    'game_name' => 'Synonym Pairing Game',
                'game_type' => 'Challenge',

                    'score' => $request->is_true ? 1 : 0,
                ]);
                return response()->json([
                    'success'=> true,
                    'data' => true
                ]);
            }catch(Exception $e){
                return response()->json([
                    'success' => false,
                    'data'=> false,
                    'message' => $e->getMessage()
                ]);
            }
        
    }

    public function storeScrambledWordGames(Request $request){
        try{
            $request->validate([
                'word_id' => 'required',
                'is_true' => 'required',
            ]);

            $user = auth()->user();
            $scrambledClaim = new ScrambledClaim();


            $scrambledClaim->word_id = $request->word_id;
            $scrambledClaim->is_true = $request->is_true;
            $scrambledClaim->user_id = $user->_id;

            $scrambledClaim->save();
            app(LeaderboardController::class)->updateUserScores();
            GameHistory::create([
                'user_id' => $user->id,
                'game_name' => 'Scrambled Word',
                'game_type' => 'Challenge',
                'score' => $request->is_true ? 1 : 0,
            ]);
            return response()->json([
                'success'=> true,
                'data' => true
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'data'=> false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function storeScrambledTenseGames(Request $request){
        try{
            $request->validate([
                'tense_id' => 'required',
                'is_true' => 'required',
            ]);

            $user = auth()->user();
            $scrambledTense = new TenseClaim();


            $scrambledTense->tense_id = $request->tense_id;
            $scrambledTense->is_true = $request->is_true;
            $scrambledTense->user_id = $user->_id;

            $scrambledTense->save();
            app(LeaderboardController::class)->updateUserScores();

            GameHistory::create([
                'user_id' => $user->id,
                'game_name' => 'Scrambled Sentence',
                'game_type' => 'Challenge',
                'score' => $request->is_true? 1 : 0,
            ]);

            return response()->json([
                'success'=> true,
                'data' => true
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'data'=> false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function storeSpeakingGames(Request $request){
        try{
            $request->validate([
                'tense_id' => 'required',
                'score' => 'required',
            ]);

            $user = auth()->user();
            $speakingGame = new SpeakingClaim();

            $speakingGame->tense_id = $request->tense_id;
            $speakingGame->score = $request->score;
            $speakingGame->user_id = $user->_id;

            $speakingGame->save();
            app(LeaderboardController::class)->updateUserScores();
            GameHistory::create([
                'user_id' => $user->id,
                'game_name' => 'Speaking Game',
                'game_type' => 'Challenge',
                'score' => $request->score,
            ]);
    
            return response()->json([
                'success'=> true,
                'data' => true
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'data'=> false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUserHistory(Request $request, $userId = null)
{
    try {
        $user = $userId ? User::findOrFail($userId) : auth()->user();
        
        $history = GameHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')->take(10)
            ->get();

        return response()->json([   
            'success' => true,
            'data' => $history
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'data' => false
        ]);
    }
}

public function getLoggedInUserHistory()
{
    return $this->getUserHistory(request());
}

    
}
