<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrambledClaim;
use App\Models\SpeakingClaim;
use App\Models\SynonymClaim;
use App\Models\TenseClaim;
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
                'is_true' => 'required',
            ]);

            $user = auth()->user();
            $speakingGame = new SpeakingClaim();

            $speakingGame->tense_id = $request->tense_id;
            $speakingGame->score = $request->score;
            $speakingGame->user_id = $user->_id;

            $speakingGame->save();

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

    
}
