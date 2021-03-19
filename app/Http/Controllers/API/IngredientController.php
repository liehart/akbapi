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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $ingredient = Ingredient::all();

        if (count($ingredient) > 0)
            return $this->sendResponse($ingredient, 'Ingredient retrieved successfully');

        return $this->sendError('Ingredient empty');
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
            'serving_size' => 'required|numeric',
            'menu_id' => 'required|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
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
            'serving_size' => 'required|numeric',
            'menu_id' => 'required|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $ingredient->unit = $store_data['unit'];
        $ingredient->serving_size = $store_data['serving_size'];
        $ingredient->menu_id = $store_data['menu_id'];
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

        $ingredient->delete();

        return $this->sendResponse(null, 'Ingredient deleted successfully.');
    }
}
