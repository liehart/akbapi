<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Torann\Hashids\Facade\Hashids;

class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $v = json_decode($request->query('v'));
        $query = $v->query ?? '';
        $sort = $v->sort ?? 'id';
        $asc = $v->asc ?? 'true';
        $show = $v->show ?? 10;
        $page = $v->page ?? 1;

        $menus = Transaction::when($sort, function ($q) use ($sort, $asc) {
            $q->orderBy($sort, $asc == 'true' ? 'asc' : 'desc');
        })
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with('cashier')
            ->with('order')
            ->paginate($show, ['*'], 'page', $page)
            ->onEachSide(2)
            ->setPath('');

        return $this->sendResponse($menus, 'TRANSACTION_RETRIEVED');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $token
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'payment_method' => 'required|in:cash,debit,credit',
            'cashier_id' => 'required|exists:employees,id',
            'token' => 'required|exists:orders,token'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $id = Hashids::decode($store_data['token']);

        if(isset($id[0])) {

            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $price = $order->details->sum(function ($d) {
                    return $d->quantity * $d->menu->price;
                });
                $transaction = Transaction::create([
                    'subtotal' => $price,
                    'tax' => $price * 0.1,
                    'service' => $price * 0.05,
                    'grand_total' => $price + ($price * 0.15),
                    'payment_method' => $store_data['payment_method'],
                    'cashier_id' => $store_data['cashier_id'],
                    'order_id' => $id[0]
                ]);
                $transaction->transaction_sn = 'AKB-' . Carbon::now()->format('Ymd') . '-' . $transaction->id;
                $transaction->save();
                return $this->sendResponse($transaction, 'TRANSACTION_CREATED');
            }
        }
        return $this->sendError('ORDER_ID_INVALID');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function showReceipt($token){
        $id = Hashids::decode($token);

        if(isset($id[0])) {

            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $customers = Customer::all();
                return view('receipt', compact('customers', 'order'));            }
        }
        return $this->sendError('ORDER_ID_INVALID');

    }

    public function showTransaksiPendapatan($year){
        if(isset($year)) {
            if($year > 2000 && $year < 10000) {
                $query = "
                select MONTH(order_details.created_at) as bulan,
                    CONVERT(SUM(IF(menu_type = 'main', quantity * price, 0)), UNSIGNED) as jumlah_makanan,
                    CONVERT(SUM(IF(menu_type = 'drink', quantity * price, 0)), UNSIGNED)  as jumlah_minuman,
                    CONVERT(SUM(IF(menu_type = 'side_dish', quantity * price, 0)), UNSIGNED)  as jumlah_side_dish,
                    CONVERT(SUM(quantity * price), UNSIGNED)  as jumlah_terjual
                from order_details
                    join menus on menus.id = order_details.menu_id
                where order_id in (
                        select order_id
                        from transactions
                    ) and year(order_details.created_at) = " . $year . " group by bulan;";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('pendapatan', compact('pengeluaran', 'year'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');
    }

    public function showTransaksiPendapatanYearly($start, $end){
        if(isset($start) && isset($end)) {
            if($start > 2000 && $start < 10000 && $end > 2000 && $end < 10000) {
                $query = "
                select YEAR(order_details.created_at) as tahun,
                    CONVERT(SUM(IF(menu_type = 'main', quantity * price, 0)), UNSIGNED) as jumlah_makanan,
                    CONVERT(SUM(IF(menu_type = 'drink', quantity * price, 0)), UNSIGNED)  as jumlah_minuman,
                    CONVERT(SUM(IF(menu_type = 'side_dish', quantity * price, 0)), UNSIGNED)  as jumlah_side_dish,
                    CONVERT(SUM(quantity * price), UNSIGNED)  as jumlah_terjual
                from order_details
                    join menus on menus.id = order_details.menu_id
                where order_id in (
                        select order_id
                        from transactions
                    ) and year(order_details.created_at) >= " . $start . " and year(order_details.created_at) <= " . $end . " group by tahun;";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('pendapatanTahun', compact('pengeluaran', 'start', 'end'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');
    }

    public function showTransaksiPengeluaran($year){
        if(isset($year)) {
            if($year > 2000 && $year < 10000) {
                $query = "
                select MONTH(incoming_stocks.created_at) as bulan,
                    CONVERT(SUM(IF(menu_type = 'main', incoming_stocks.price, 0)), UNSIGNED) as jumlah_makanan,
                    CONVERT(SUM(IF(menu_type = 'drink', incoming_stocks.price, 0)), UNSIGNED)  as jumlah_minuman,
                    CONVERT(SUM(IF(menu_type = 'side_dish', incoming_stocks.price, 0)), UNSIGNED)  as jumlah_side_dish,
                    CONVERT(SUM(incoming_stocks.price), UNSIGNED)  as jumlah_terjual
                from incoming_stocks
                    join ingredients on ingredients.id = incoming_stocks.ingredient_id
                    join menus on menus.ingredient_id = ingredients.id
                where year(incoming_stocks.created_at) = " . $year . " group by bulan";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('pengeluaran', compact('pengeluaran', 'year'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');
    }

    public function showTransaksiPengeluaranYearly($start, $end){
        if(isset($start) && isset($end)) {
            if($start > 2000 && $start < 10000 && $end > 2000 && $end < 10000) {
                $query = "
                select YEAR(incoming_stocks.date) as tahun,
                    CONVERT(SUM(IF(menu_type = 'main', incoming_stocks.price, 0)), UNSIGNED) as jumlah_makanan,
                    CONVERT(SUM(IF(menu_type = 'drink', incoming_stocks.price, 0)), UNSIGNED)  as jumlah_minuman,
                    CONVERT(SUM(IF(menu_type = 'side_dish', incoming_stocks.price, 0)), UNSIGNED)  as jumlah_side_dish,
                    CONVERT(SUM(incoming_stocks.price), UNSIGNED)  as jumlah_terjual
                from incoming_stocks
                    join ingredients on ingredients.id = incoming_stocks.ingredient_id
                    join menus on menus.ingredient_id = ingredients.id
                where year(incoming_stocks.date) >= " . $start . " and year(incoming_stocks.date) <= " . $end . " group by tahun";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('pengeluaranTahun', compact('pengeluaran', 'start', 'end'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');

    }

    public function showTransaksiPenjualan($month, $year){
        if(isset($month) && isset($year)) {
            if($month > 0 && $month <= 12 && $year > 0 && $year < 10000) {
                $query = "select name, menu_type, unit, sum(jumlah_penjualan) as jumlah_penjualan, max(jumlah_penjualan) as jumlah_penjualan_tertinggi from (
                        select menus.menu_type, DATE(order_details.created_at) as date, menus.name, menus.unit, sum(order_details.quantity) as jumlah_penjualan from order_details
                    join menus on order_details.menu_id = menus.id
                    join ingredients on menus.ingredient_id = ingredients.id
                    where order_id in (select order_id from transactions) and month(order_details.created_at) = " . $month. "
                    and year(order_details.created_at) = " . $year. "
                    group by menus.menu_type, menus.name, menus.unit, DATE(order_details.created_at)
                    ) as a group by name, unit, menu_type;";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('penjualan', compact('pengeluaran', 'month', 'year'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');

    }

    public function showTransaksiPenjualanTahun($year){
        if(isset($year)) {
            if($year > 0 && $year < 10000) {
                $query = "select name, menu_type, unit, sum(jumlah_penjualan) as jumlah_penjualan, max(jumlah_penjualan) as jumlah_penjualan_tertinggi from (
                        select menus.menu_type, DATE(order_details.created_at) as date, menus.name, menus.unit, sum(order_details.quantity) as jumlah_penjualan from order_details
                    join menus on order_details.menu_id = menus.id
                    join ingredients on menus.ingredient_id = ingredients.id
                    where order_id in (select order_id from transactions)
                    and year(order_details.created_at) = " . $year. "
                    group by menus.menu_type, menus.name, menus.unit, DATE(order_details.created_at)
                    ) as a group by name, unit, menu_type;";
                $pengeluaran = DB::select($query);
                if (count($pengeluaran) > 0 ) {
                    return view('penjualanTahun', compact('pengeluaran', 'year'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');

    }
}
