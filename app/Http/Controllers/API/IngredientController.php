<?php

namespace App\Http\Controllers\API;

use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IngredientController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $v = json_decode($request->query('v'));
        $query = $v->query ?? '';
        $sort = $v->sort ?? 'name';
        $asc = $v->asc ?? 'true';
        $show = $v->show ?? 10;
        $page = $v->page ?? 1;
        $select = $v->select ?? 'no';
        $select_id = $v->select_id ?? null;

        $menus = Ingredient::when($sort, function ($q) use ($sort, $asc) {
            $q->orderBy($sort, $asc == 'true' ? 'asc' : 'desc');
        })
            ->when($select == 'yes', function ($q) use ($select, $select_id) {
                $q->doesntHave('menu')->when($select_id, function ($qq) use ($select_id) {
                    $qq->orWhereHas('menu', function($qqq) use ($select_id) {
                        $qqq->where('ingredient_id', '=', $select_id);
                    });
                });
            })
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->paginate($show, ['*'], 'page', $page)
            ->onEachSide(2)
            ->setPath('');

        return $this->sendResponse($menus, 'Ingredients retrieved successfully');
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
            'unit' => 'required|alpha',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $ingredient = Ingredient::create($store_data);

        return $this->sendResponse($ingredient, 'Ingredient created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $ingredient = Ingredient::find($id);

        if (is_null($ingredient))
            return $this->sendError('Ingredient not found');

        return $this->sendResponse($ingredient, 'Ingredient retrieved successfully');

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
        $ingredient = Ingredient::find($id);

        if (is_null($ingredient))
            return $this->sendError('Ingredient not found');

        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'unit' => 'required|alpha',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $ingredient->unit = $store_data['unit'];
        $ingredient->name = $store_data['name'];
        $ingredient->save();

        return $this->sendResponse($ingredient, 'Ingredient updated successfull');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $ingredient = Ingredient::find($id);

        if (is_null($ingredient))
            return $this->sendError('Ingredient not found');

        if ($ingredient->menu) {
            return $this->sendError('Silahkan hapus terlebih dahulu menu yang memakai bahan ' . $ingredient->name . ' (' . $ingredient->menu->name . ')');
        } else {
            $ingredient->delete();
            return $this->sendResponse(null, 'Ingredient deleted successfully.');
        }

    }
}
