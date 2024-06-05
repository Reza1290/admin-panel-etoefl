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
use PhpOffice\Math\Element\Row;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/forgot', 'forgot');
    Route::post('/users/verify-otp-forgot',  'verifyOtpForgot');
    Route::post('/reset', 'reset');
    Route::post('/users/verify-otp',  'verifyOtpRegister');
    Route::post('/users/new-otp',  'newOtp');
    Route::get('/users/profile',  'profile');
    Route::post('/logout',  'logout');
    Route::post('check/password', 'checkPassword');
    Route::post('change/password', 'changePassword');
    Route::post('search/friend', 'searchFriend');
    Route::get('/spesify-user/{id}', 'spesifyFriend');
    Route::post('/friend/process/add-patch/{id}', 'addFriend');
    Route::get('/get-all/friends', 'getAllFriends');
    Route::post('/edit/profile', 'editProfile');
    Route::get('/.well-known/assetlink.json', 'wellknownAssetLink');
});
