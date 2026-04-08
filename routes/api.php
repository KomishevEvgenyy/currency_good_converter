<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;
use Modules\Currency\Application\Http\Controllers\ExchangeRateController;

Route::get('/catalog/{currency}', CatalogController::class);
Route::get('/exchangeRate/{currency}', ExchangeRateController::class);
