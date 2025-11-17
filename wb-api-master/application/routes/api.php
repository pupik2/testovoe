<?php

use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware([AuthMiddleware::class])->group(function () {

    Route::get('sales', [SaleController::class, 'list']);
    Route::get('stocks', [StockController::class, 'list']);
    Route::get('orders', [OrderController::class, 'list']);
    Route::get('incomes', [IncomeController::class, 'list']);
});

Route::middleware('auth.token')->post('/test-post', function (Request $request) {
    $account = $request->attributes->get('auth_account');

    return response()->json([
        'message' => 'Аутентификация прошла успешно',
        'authenticated_account' => [
            'id' => $account->id,
            'name' => $account->name,
            'company_id' => $account->company_id,
        ],
        'received_data' => $request->all(),
    ]);

});

