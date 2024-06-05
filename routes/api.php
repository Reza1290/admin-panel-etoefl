<?php

use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PacketController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\ForYouController;
use App\Http\Controllers\Api\GameAnswerController;
use App\Http\Controllers\Api\GameClaimController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\MiniGameController;
use App\Http\Controllers\Api\PairingClaimController;
use App\Http\Controllers\Api\QuizAnswerController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizEnrollController;
use App\Http\Controllers\Api\QuizGameScoreController;
use App\Http\Controllers\Api\QuizResultController;
use App\Http\Controllers\Api\QuizTypeController;
use App\Http\Controllers\Api\RandomWordController;
use App\Http\Controllers\Api\ScrambledWordController;
use App\Http\Controllers\Api\ValueHomeController;
use App\Models\Leaderboard;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => '/minigames'], function() {
        Route::post('/pairing-game', [MiniGameController::class, 'storePairingGames']);
        Route::post('/scrambled-word-game', [MiniGameController::class, 'storeScrambledWordGames']);
        Route::post('/scrambled-tense-game', [MiniGameController::class, 'storeScrambledTenseGames']);
        Route::post('/speaking-game', [MiniGameController::class, 'storeSpeakingGames']);
        Route::get('/user-history', [MiniGameController::class, 'getLoggedInUserHistory']);
        Route::get('/user-history/{userId}', [MiniGameController::class, 'getUserHistory']);
    });
    Route::get('/leaderboard',[LeaderboardController::class, 'index']);
    Route::get('/user-rank',[LeaderboardController::class,'me']);
    Route::get('/user-rank/{id}',[LeaderboardController::class,'show']);
    
    Route::resource('/foryou', ForYouController::class);
    
    Route::resource('/quizs',QuizController::class);
    Route::resource('/quiztypes',QuizTypeController::class);
    Route::resource('/games',GameController::class);
    Route::resource('/gameclaims',GameClaimController::class);
    Route::resource('/quizclaims',QuizEnrollController::class);
    Route::resource('/gameanswer',GameAnswerController::class);
    Route::resource('/quizanswer',QuizAnswerController::class);
    Route::resource('/quizgameresult',QuizResultController::class);    
});
