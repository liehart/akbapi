<?php

namespace App\Http\Controllers\API;

use App\Events\NotifyWaiterReadyToServe;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Torann\Hashids\Facade\Hashids;
use function PHPUnit\Framework\isNull;

class OrderController extends BaseController
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
        $sort = $v->sort ?? '';
        $asc = $v->asc ?? 'true';
        $show = $v->show ?? 10;
        $page = $v->page ?? 1;

        $orders = Order::join('reservations', 'reservation_id', '=', 'reservations.id')
            ->join('customers', 'customer_id', '=', 'customers.id')
            ->join('employees', 'waiter_id', '=', 'employees.id')
            ->select(
                [
                    'customers.name',
                    'customers.email',
                    'customers.phone',
                    'table_number',
                    'order_date',
                    'session',
                    'orders.*',
                    'employees.name as w_name'
                ])
            ->when($sort, function ($q) use ($sort, $asc) {
                $q->orderBy($sort, $asc == 'true' ? 'asc' : 'desc');
            })
            ->when($query, function ($q) use ($query) {
                $q->orWhere('customers.name', 'like', '%' . $query . '%')
                    ->orWhere('table_number', 'like', '%' . $query . '%');
            })
            ->with('transaction')
            ->with('details')
            ->paginate($show, ['*'], 'page', $page)
            ->onEachSide(2)
            ->setPath('');

        return $this->sendResponse($orders, 'Orders retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|integer|exists:reservations,id|unique:orders,reservation_id',
            'waiter_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $requestData['token'] = Hashids::encode($requestData['reservation_id']);

        $order = Order::create($requestData);

        $order->reservation->table->is_empty = true;
        $order->reservation->status = 'in';
        $order->reservation->table->save();
        $order->reservation->save();

        return $this->sendResponse($order, 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(String $token): JsonResponse
    {

        $order = Order::where('token', '=', $token)->first();

        if (is_null($order)) {
            return $this->sendError('ORDER_NOT_FOUND');
        }

        $order = Order::where('token', '=', $token)->with('transaction')->with('waiter')->with('reservation.customer')->with('details')->with('details.menu')->first();
        $price = $order->details->sum(function ($cart) {
            return $cart->quantity * $cart->menu->price;
        });
        $order['price'] = [
            'price' => (int) $price,
            'tax' => (int) round($price * 0.1),
            'service' => (int) round($price * 0.05),
            'total' => (int) round($price + ($price * 0.15)),
        ];

        if (is_null($order))
            return $this->sendError('Order not found');

        return $this->sendResponse($order, 'Order retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (is_null($order))
            return $this->sendError('Order not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|integer|exists:reservations,id',
            'waiter_id' => 'required|integer|exists:employees,id',
            'finish_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $order->reservation_id = $requestData['reservation_id'];
        $order->waiter_id = $requestData['waiter_id'];
        $order->finish_at = $requestData['finish_at'];

        $order->save();

        return $this->sendResponse($order, 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (is_null($order))
            return $this->sendError('Order not found');

        $order->reservation->table->is_empty = false;
        $order->reservation->status = 'new';
        $order->reservation->table->save();
        $order->reservation->save();
        $order->delete();

        return $this->sendResponse(null, 'Order deleted successfully.');
    }

    public function done(String $token): JsonResponse
    {

        $order = Order::where('token', '=', $token)->first();

        if (is_null($order)) {
            return $this->sendError('ORDER_NOT_FOUND');
        }

        $cek = DB::table('order_details')
            ->select(['*'])
            ->where('order_id', '=', $order->id)
            ->whereNull('served_at')
            ->get();

        if(count($cek) > 0) {
            return $this->sendError('ORDER_HASNT_BEEN_FINISHED');
        }

        if (is_null($order->finish_at)) {
            $order->finish_at = Carbon::now();
            $order->save();
            $order->reservation->table->is_empty = false;
            $order->reservation->status = 'done';
            $order->reservation->table->save();
            $order->reservation->save();

            return $this->sendResponse($order, 'Order retrieved successfully.');
        }

        return $this->sendError('ORDER_HAS_BEEN_FINISHED');
    }

    public function cek(String $token): JsonResponse
    {

        $order = Order::where('token', '=', $token)->first();

        if (is_null($order)) {
            return $this->sendError('ORDER_NOT_FOUND');
        }

        if (is_null($order->finish_at)) {
            $order->finish_at = Carbon::now();

            return $this->sendResponse($order, 'ORDER_OK');
        }

        return $this->sendError('ORDER_HAS_BEEN_FINISHED');
    }

    public function ready($id): JsonResponse
    {

        $order = OrderDetail::find($id);

        if (is_null($order)) {
            return $this->sendError('ORDER_NOT_FOUND');
        }

        if (is_null($order->ready_to_serve_at)) {
            $order->ready_to_serve_at = Carbon::now();
            $order->save();
//            event(new NotifyWaiterReadyToServe($order->order->reservation->customer->name, $order->order->token));
            return $this->sendResponse($order, 'ORDER_READY');
        }

        return $this->sendError('ORDER_ALREADY_READY');
    }

    public function served($id): JsonResponse
    {

        $order = OrderDetail::find($id);

        if (is_null($order)) {
            return $this->sendError('ORDER_NOT_FOUND');
        }

        if (is_null($order->served_at)) {
            $order->served_at = Carbon::now();
            $order->save();
            return $this->sendResponse($order, 'ORDER_SERVED');
        }

        return $this->sendError('ORDER_ALREADY_SERVED');
    }
}
