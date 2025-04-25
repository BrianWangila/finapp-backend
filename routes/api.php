<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\Api\CurrencyExchangeController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Cards
    Route::get('/cards', [CardController::class, 'index']);
    Route::post('/cards', [CardController::class, 'store']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    // Route::post('/transactions/send', [TransactionController::class, 'send']);
    // Route::post('/transactions/withdraw', [TransactionController::class, 'withdraw']);
    // Route::post('/transactions/exchange', [TransactionController::class, 'exchange']);

    // Currency Exchange
    Route::get('/currency-exchanges', [CurrencyExchangeController::class, 'index']);
    Route::post('/currency-exchanges', [CurrencyExchangeController::class, 'store']);
});
