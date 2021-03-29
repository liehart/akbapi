<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $order = Order::all();

        if (count($order) > 0)
            return $this->sendResponse($order, 'Order retrieved successfully');

        return $this->sendError('Order empty');
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
            'reservation_id' => 'required|integer|exists:reservations,id',
            'waiter_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $order = Order::create($requestData);

        return $this->sendResponse($order, 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::find($id);

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

        $order->delete();

        return $this->sendResponse(null, 'Order deleted successfully.');
    }
}
