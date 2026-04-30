<?php

use App\Http\Controllers\LegacyBridgeController;
use Illuminate\Support\Facades\Route;

Route::any('/{any?}', [LegacyBridgeController::class, 'handle'])
    ->where('any', '.*');
