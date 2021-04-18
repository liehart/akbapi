<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\EmployeeRoleController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\IngredientController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderDetailController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\StockHistoryController;
use App\Http\Controllers\API\TableController;
use App\Http\Controllers\API\TransactionCardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('customer', CustomerController::class);
Route::resource('table', TableController::class);
Route::resource('card', TransactionCardController::class);
Route::resource('menu', MenuController::class);
Route::resource('ingredient', IngredientController::class);
Route::resource('reservation', ReservationController::class);
Route::resource('role', EmployeeRoleController::class);
Route::resource('employee', EmployeeController::class);
Route::resource('history', StockHistoryController::class);
Route::resource('order', OrderController::class);
Route::resource('file', FileController::class);
Route::resource('order/{order_id}/detail', OrderDetailController::class);

Route::prefix('auth')->group(function () {
    Route::post('/', [AuthController::class, 'index'])->middleware('auth:api');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('password', [EmployeeController::class, 'updatePassword']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('menu/image/{id}', [MenuController::class, 'updateImage']);
