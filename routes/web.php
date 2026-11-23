<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('transactions', TransactionController::class)->except('show');

Route::get('/chart', [ChartController::class, 'index'])->name('chart');

Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');

Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
