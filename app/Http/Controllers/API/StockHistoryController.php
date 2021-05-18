<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StockHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StockHistoryController extends BaseController
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
        $filter = $v->filter ?? [];

        $menus = StockHistory::with('ingredient.menu')
            ->where(function($q) use ($filter){
                foreach($filter as $item){
                    $q->when($item->value != [] || $item->value, function ($qq) use ($item) {
                        $qq->whereIn($item->name, $item->value ?? ['*']);
                    });
                }
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
            'quantity' => 'required|max:50',
            'category' => 'required|in:in,sold,waste',
            'price' => 'required_if:category,in|numeric',
            'ingredient_id' => 'required|exists:ingredients,id',
            ''
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $history = StockHistory::create($store_data);

        return $this->sendResponse($history, 'Stock history created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $history = StockHistory::find($id);

        if (is_null($history))
            return $this->sendError('Stock history not found');

        return $this->sendResponse($history, 'Stock history retrieved successfully.');
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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $history = StockHistory::find($id);

        if (is_null($history))
            return $this->sendError('Stock history not found');

        $history->delete();

        return $this->sendResponse($history, 'Stock history deleted successfully.');
    }
}
