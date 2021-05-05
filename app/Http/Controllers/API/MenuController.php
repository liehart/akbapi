<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $menus = Menu::orderBy('name')
            ->paginate(10)
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
            'name' => 'required|max:50',
            'description' => 'required',
            'unit' => 'required|alpha',
            'price' => 'required|numeric',
            'menu_type' => 'required|in:side_dish,drink,main'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $menu = Menu::create($store_data);

        return $this->sendResponse($menu, 'Menu created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        return $this->sendResponse($menu, 'Menu retrieved successfully');
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
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'name' => 'required|max:50',
            'description' => 'required',
            'unit' => 'required|alpha',
            'price' => 'required|numeric',
            'menu_type' => 'required|in:side_dish,drink,main'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $menu->name = $store_data['name'];
        $menu->description = $store_data['description'];
        $menu->unit = $store_data['unit'];
        $menu->price = $store_data['price'];
        $menu->menu_type = $store_data['menu_type'];

        $menu->save();

        return $this->sendResponse($menu, 'Menu updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        $menu->delete();

        return $this->sendResponse(null, 'Menu deleted successfully.');
    }
}
