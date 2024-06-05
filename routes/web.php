<?php

use App\Http\Controllers\AllPacketController;
use App\Http\Controllers\Api\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PacketFullController;
use App\Http\Controllers\PacketMiniController;
use App\Http\Controllers\StoryQuestionController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/', 'login')->name('loginPost');
});

Route::controller(AuthAuthController::class)->group(function () {
    Route::get('/.well-known/assetlinks.json', 'wellknownAssetLink');
});
