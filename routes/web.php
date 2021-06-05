<?php

use App\Http\Controllers\API\IngredientController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/receipt/{token}', [TransactionController::class, 'showReceipt']);

Route::get('/stock/{start}/{end}', [IngredientController::class, 'showStockCustsom']);
Route::get('/stockMenu/{menu}/{month}/{year}', [IngredientController::class, 'showStockMenu']);
Route::get('/pengeluaran/{year}', [TransactionController::class, 'showTransaksiPengeluaran']);
Route::get('/pengeluaranTahun/{start}/{end}', [TransactionController::class, 'showTransaksiPengeluaranYearly']);
Route::get('/pendapatan/{year}', [TransactionController::class, 'showTransaksiPendapatan']);
Route::get('/pendapatanTahun/{start}/{end}', [TransactionController::class, 'showTransaksiPendapatanYearly']);
Route::get('/penjualan/{month}/{year}', [TransactionController::class, 'showTransaksiPenjualan']);
Route::get('/penjualanTahun/{year}', [TransactionController::class, 'showTransaksiPenjualanTahun']);
