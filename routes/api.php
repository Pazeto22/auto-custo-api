<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BudgetController;

// Oficinas
Route::apiResource('workshops', WorkshopController::class);
// Usuários
Route::apiResource('workshops.users', UserController::class);
// Clientes
Route::apiResource('workshops.clients', ClientController::class);
// Veículos
Route::apiResource('clients.vehicles', VehicleController::class);
// Produtos
Route::apiResource('products', ProductController::class);
// Orçamentos
Route::apiResource('budgets', BudgetController::class);
// Itens do orçamento
Route::apiResource('budgets.items', \App\Http\Controllers\BudgetItemController::class);
