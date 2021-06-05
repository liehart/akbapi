<?php

namespace App\Http\Controllers\API;

use App\Events\CustomerCreated;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OutgoingStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Torann\Hashids\Facade\Hashids;

class CartController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param $token
     * @return JsonResponse
     */
    public function index($token): JsonResponse
    {
        $id = Hashids::decode($token);

        if(isset($id[0])) {
            $carts = Cart::where('order_id', '=', $id[0])->get();
            $order = Order::where('id', '=', $id[0])->first();
            if (count($carts) > 0) {
                $price = $carts->sum(function ($cart) {
                    return $cart->quantity * $cart->menu->price;
                });
                $data = [
                    'price' => [
                        'price' => (int) $price,
                        'tax' => (int) round($price * 0.1),
                        'service' => (int) round($price * 0.05),
                        'total' => (int) round($price + ($price * 0.15)),
                    ],
                    'carts' => $carts,
                    'reservation' => [
                        'name' => $order->reservation->customer->name,
                        'table_number' => $order->reservation->table_number,
                        'session' => $order->reservation->session
                    ]
                ];
                return $this->sendResponse($data, 'CART_RETRIEVED');
            }
            $data = [
                'price' => [
                    'price' => 0,
                    'tax' => 0 * 0.1,
                    'service' => 0 * 0.05,
                    'total' => 0 + (0 * 0.15),
                ],
                'carts' => [],
                'reservation' => [
                    'name' => $order->reservation->customer->name,
                    'table_number' => $order->reservation->table_number,
                    'session' => $order->reservation->session
                ]
            ];
            return $this->sendResponse($data, 'CART_EMPTY');
        }
        return $this->sendError('CART_TOKEN_INVALID');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $token
     * @param Request $request
     * @return JsonResponse
     */
    public function store($token, Request $request): JsonResponse
    {
        $id = Hashids::decode($token);

        if(isset($id[0])) {
            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $requestData = $request->all();
                $validator = Validator::make($requestData, [
                    'menu_id' => 'required|integer|exists:menus,id',
                ]);
                if ($validator->fails()) {
                    return $this->sendResponse($validator->errors(), 'CART_VALIDATION_ERROR', success: false);
                }
                $cart = Cart::where('menu_id', $requestData['menu_id'])
                    ->where('order_id', $id[0])
                    ->first();
                $cartQuantity = (int) Cart::where('menu_id', $requestData['menu_id'])
                        ->where('order_id', $id[0])
                        ->sum('quantity') + 1;
                $menu = Menu::find($requestData['menu_id']);
                $menuQuantity = $menu->serving_size;
                $servingQuantity = $menu->ingredient->remaining_stock;
                $servingRemaining = (int) ($servingQuantity/$menuQuantity);

                if ($cartQuantity > $servingRemaining) {
                    return $this->sendError('MENU_OUT_OF_STOCK');
                }
                if(is_null($cart)) {
                    $newCart = Cart::create([
                        'menu_id' => $requestData['menu_id'],
                        'quantity' => 1,
                        'order_id' => $id[0]
                    ]);
                    return $this->sendResponse($newCart, 'CART_CREATED');
                }
                $cart->update([
                    'quantity' => $cart->quantity + 1
                ]);
                return $this->sendResponse($cart, 'CART_UPDATED');
            }
        }
        return $this->sendError($id, 'CART_TOKEN_INVALID');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $token
     * @param Request $request
     * @return JsonResponse
     */
    public function update($token, Request $request): JsonResponse
    {
        $id = Hashids::decode($token);

        if(isset($id[0])) {
            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $requestData = $request->all();
                $validator = Validator::make($requestData, [
                    'menu_id' => 'required|integer|exists:menus,id',
                    'quantity' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return $this->sendResponse($validator->errors(), 'CART_VALIDATION_ERROR', success: false);
                }

                $cart = Cart::where('menu_id', $requestData['menu_id'])
                    ->where('order_id', $id[0])
                    ->first();

                $cartQuantity = $requestData['quantity'];
                $menu = Menu::find($requestData['menu_id']);
                $menuQuantity = $menu->serving_size;
                $servingQuantity = $menu->ingredient->remaining_stock;
                $servingRemaining = (int) ($servingQuantity/$menuQuantity);

                if ($cartQuantity > $servingRemaining) {
                    return $this->sendError('MENU_OUT_OF_STOCK');
                }

                if(!is_null($cart)) {
                    if($requestData['quantity'] <= 0) {
                        $cart->delete();
                        return $this->sendResponse(null, 'CART_DELETED');
                    }
                    $cart->update([
                        'quantity' => (int) $requestData['quantity']
                    ]);
                    return $this->sendResponse($cart, 'CART_UPDATED');
                }
                return $this->sendError('CART_EMPTY');
            }
        }
        return $this->sendError('CART_TOKEN_INVALID');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $token
     * @return JsonResponse
     */
    public function destroy($token): JsonResponse
    {
        $id = Hashids::decode($token);

        if(isset($id[0])) {
            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $carts = Cart::where('order_id', $id[0]);
                if(!is_null($carts)) {
                    $carts->delete();
                    return $this->sendResponse(null, 'CART_DELETED');
                }
                return $this->sendError('CART_EMPTY');
            }
        }
        return $this->sendError('CART_TOKEN_INVALID');
    }

    public function createOrder($token): JsonResponse
    {
        $id = Hashids::decode($token);

        if(isset($id[0])) {
            $order = Order::find($id[0]);
            if(!is_null($order)) {
                $carts = Cart::where('order_id', $id[0]);
                if(count($carts->get()) > 0) {
                    foreach ($carts->get() as $cart) {
                        $rs = $cart->menu->ingredient->remaining_stock;
                        $q = $cart->menu->serving_size * $cart->quantity;
                        if (($rs - $q) >= 0) {
                            $cart->menu->ingredient->remaining_stock -= $q;
                            if($cart->menu->ingredient->remaining_stock - $q < $cart->menu->serving_size) {
                                $cart->menu->is_available = false;
                                $cart->menu->save();
                            }
                            $cart->menu->ingredient->save();
                            OutgoingStock::create([
                                'quantity' => $q,
                                'employee_id' => 1,
                                'ingredient_id' => $cart->menu->ingredient->id,
                                'category' => 'sold'
                            ]);
                        } else {
                            return $this->sendError('INGREDIENT_INSUFFICIENT');
                        }
                    }
                    foreach ($carts->select(
                        ['order_id', 'menu_id', 'quantity']
                    )->get()->toArray() as $cart) {
                        OrderDetail::create($cart);
                    }
                    $order = Order::where('token', '=', $token)->first();
                    $order->total_menu += $carts->count();
                    $order->total_item += $carts->sum('quantity');
                    $order->save();
                    $carts->delete();
                    event(new CustomerCreated($order->reservation->customer->name, $token));
                    return $this->sendResponse($carts, 'ORDER_CREATED');
                }
                return $this->sendError('CART_EMPTY');
            }
        }
        return $this->sendError('CART_TOKEN_INVALID');
    }
}
