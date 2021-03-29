<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderDetailController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param int $order_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $order_id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($order_id);

        if (is_null($order))
            return $this->sendError('Order not found');

        return $this->sendResponse($order, 'Order retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
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
}
