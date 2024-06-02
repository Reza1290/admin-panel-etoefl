<?php

use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PacketController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\ForYouController;
use App\Http\Controllers\Api\GameAnswerController;
use App\Http\Controllers\Api\GameClaimController;
use App\Http\Controllers\Api\GameController;
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
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    Route::resource('/randomword', RandomWordController::class);
    Route::resource('/quizs',QuizController::class);
    Route::resource('/quiztypes',QuizTypeController::class);
    Route::resource('/games',GameController::class);
    Route::resource('/gameclaims',GameClaimController::class);
    Route::resource('/quizclaims',QuizEnrollController::class);
    Route::resource('/leaderboard',QuizGameScoreController::class);
    Route::resource('/gameanswer',GameAnswerController::class);
    Route::resource('/quizanswer',QuizAnswerController::class);
    Route::resource('/quizgameresult',QuizResultController::class);
    Route::resource('/randomword',RandomWordController::class);
    Route::resource('/scrambledword',ScrambledWordController::class);
    Route::resource('/pairingclaims',PairingClaimController::class);
    Route::resource('/foryou', ForYouController::class);
});
