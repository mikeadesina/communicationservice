<?php

use Illuminate\Support\Facades\Route;
use App\Api\V1\Controllers\TelegramController;

Route::group(['prefix' => 'V1'], function() {
    Route::get('/subscribe', [TelegramController::class, 'subscribeToBot']);
    Route::get('/subscribe-to-channel', [TelegramController::class, 'subscribeToChannel']);
    Route::get('/setwebhook', [TelegramController::class, 'setWebhook']);
    Route::get('/webhook', [TelegramController::class, 'setWebhook']);
});
