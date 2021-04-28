<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\IngredientController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderDetailController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\StatisticController;
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
Route::resource('role', RoleController::class);
Route::resource('employee', EmployeeController::class);
Route::resource('history', StockHistoryController::class);
Route::resource('order', OrderController::class);
Route::resource('order/{order_id}/detail', OrderDetailController::class);

Route::prefix('auth')->group(function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/', [AuthController::class, 'index']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('password', [EmployeeController::class, 'updatePassword']);
    });
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('reservation/{id}/cancel', [ReservationController::class, 'cancel']);
Route::post('menu/image/{id}', [MenuController::class, 'updateImage']);
Route::post('employee/{id}/deactivate', [EmployeeController::class, 'deactivate']);
Route::post('employee/{id}/activate', [EmployeeController::class, 'activate']);

Route::post('file/avatar', [FileController::class, 'avatar']);

Route::get('statistic', [StatisticController::class, 'index']);

Route::post('search/reservation', [ReservationController::class, 'search']);

Route::get('test', function () {
    event(new App\Events\CustomerCreated('Someone'));
    return "Event has been sent!";
});

Route::get('select/role', [RoleController::class, 'select']);
Route::get('select/permission', [RoleController::class, 'permission']);

Route::get('search/customer', [CustomerController::class, 'search']);
Route::get('search/role', [RoleController::class, 'search']);
Route::get('search/employee', [EmployeeController::class, 'search']);
Route::get('search/table', [TableController::class, 'search']);
