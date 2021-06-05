<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\IncomingStockController;
use App\Http\Controllers\API\OutgoingStockController;
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
use App\Http\Controllers\API\TransactionController;
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
Route::resource('transaction', TransactionController::class);
Route::resource('role', RoleController::class);
Route::resource('employee', EmployeeController::class);
Route::resource('history/incoming', IncomingStockController::class);
Route::resource('history/outgoing', OutgoingStockController::class);
Route::resource('order', OrderController::class);
Route::resource('order/{order_id}/detail', OrderDetailController::class);
Route::resource('order/{order_id}/cart', CartController::class);

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

Route::post('menu/get', [MenuController::class, 'indexPost']);


Route::post('file/avatar', [FileController::class, 'avatar']);
Route::post('file/menu', [FileController::class, 'menu']);

Route::post('menu/{id}/enable', [MenuController::class, 'enable']);
Route::post('menu/{id}/refresh', [MenuController::class, 'refresh']);

Route::get('statistic', [StatisticController::class, 'index']);

Route::post('search/reservation', [ReservationController::class, 'search']);

Route::get('test', function () {
        event(new App\Events\CustomerCreated('Someone', 'mbk5ez'));
    return "Event has been sent!";
});

Route::get('select/role', [RoleController::class, 'select']);
Route::get('select/permission', [RoleController::class, 'permission']);
Route::get('select/reservation', [ReservationController::class, 'select']);

Route::get('search/customer', [CustomerController::class, 'search']);
Route::get('search/role', [RoleController::class, 'search']);
Route::get('search/employee', [EmployeeController::class, 'search']);
Route::get('search/table', [TableController::class, 'search']);

Route::put('order/{order_id}/cart', [CartController::class, 'update']);
Route::delete('order/{order_id}/cart', [CartController::class, 'destroy']);
Route::post('order/{order_id}/createOrder', [CartController::class, 'createOrder']);
Route::post('order/{order_id}/finish', [OrderController::class, 'done']);
Route::post('order/{order_id}/check', [OrderController::class, 'cek']);

Route::post('order_detail/{id}/ready', [OrderController::class, 'ready']);
Route::post('order_detail/{id}/serve', [OrderController::class, 'served']);
