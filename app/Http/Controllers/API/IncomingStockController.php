<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IncomingStock;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomingStockController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $v = json_decode($request->query('v'));
        $query = $v->query ?? '';
        $sort = $v->sort ?? 'date';
        $asc = $v->asc ?? 'true';
        $show = $v->show ?? 10;
        $page = $v->page ?? 1;
        $filter = $v->filter ?? [];

        $menus = IncomingStock::with('employee')
            ->with('ingredient')
            ->join('ingredients', 'ingredients.id', '=', 'incoming_stocks.ingredient_id')
            ->leftJoin('menus', 'menus.ingredient_id', '=', 'ingredients.id')
            ->select(['incoming_stocks.*', 'menus.menu_type', 'ingredients.unit as i_unit', 'menus.serving_size', 'menus.unit as m_unit', 'ingredients.name'])
            ->where(function($q) use ($filter){
                foreach($filter as $item){
                    if ($item->type == 'date.range') {
                        $q->when($item->value, function ($qq) use ($item) {
                            $qq->when(isset($item->value[0]), function ($qqq) use ($item) {
                                $qqq->where($item->name, '>=', $item->value[0]);
                            });
                            $qq->when(isset($item->value[1]), function ($qqq) use ($item) {
                                $qqq->where($item->name, '<=', Carbon::createFromFormat('Y-m-d', $item->value[1])->endOfDay()->toDateTimeString());
                            });
                        });
                    } else {
                        $q->when($item->value != [] || $item->value, function ($qq) use ($item) {
                            $qq->whereIn($item->name, $item->value ?? ['*']);
                        });
                    }
                }
            })
            ->when($query, function ($q) use ($query) {
                $q->where('ingredients.name', 'like', '%' . $query . '%');
            })
            ->orderBy($sort, $asc == 'true' ? 'asc' : 'desc')
            ->paginate($show, ['*'], 'page', $page)
            ->onEachSide(2)
            ->setPath('');
        return $this->sendResponse($menus, 'Menu retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'employee_id' => 'required|exists:employees,id',
            'quantity' => 'required|numeric',
            'ingredient_id' => 'required|exists:ingredients,id',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $ingredient = IncomingStock::create($store_data);
        $ingredient->ingredient->remaining_stock += $store_data['quantity'];
        if ($ingredient->ingredient->remaining_stock > 0) {
            if ($ingredient->ingredient->menu) {
                $ingredient->ingredient->menu->is_available = true;
                $ingredient->ingredient->menu->save();
            }
        }
        $ingredient->ingredient->save();

        return $this->sendResponse($ingredient, 'Ingredient created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $ingredient = IncomingStock::find($id);

        if (is_null($ingredient))
            return $this->sendError('Menu not found');

        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'quantity' => 'required|numeric',
            'ingredient_id' => 'required|exists:ingredients,id',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $tampung = $ingredient->quantity;
        $ingredient->quantity = $store_data['quantity'];
        $ingredient->ingredient_id = $store_data['ingredient_id'];
        $ingredient->price = $store_data['price'];
        $ingredient->ingredient->remaining_stock += $store_data['quantity'] - $tampung;
        if ($ingredient->ingredient->remaining_stock > 0) {
            if ($ingredient->ingredient->menu) {
                $ingredient->ingredient->menu->is_available = true;
                $ingredient->ingredient->menu->save();
            }
        }
        $ingredient->ingredient->save();
        $ingredient->save();

        return $this->sendResponse($ingredient, 'Ingredient created successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $menu = IncomingStock::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        $menu->ingredient->remaining_stock -= $menu->quantity;
        $menu->ingredient->save();
        $menu->delete();

        return $this->sendResponse(null, 'Menu deleted successfully.');
    }
}
