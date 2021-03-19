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
    public function index(): JsonResponse
    {
        $menu = Menu::all();

        if (count($menu) > 0)
            return $this->sendResponse($menu, 'Menu retrieved successfully');

        return $this->sendError('Menu empty');
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

        $url = "";

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $extension = $request->image->extension();
                $name = $_SERVER['REQUEST_TIME'];
                $request->image->storeAs('/public', $name.".".$extension);
                $url = Storage::url($name.".".$extension);
            }
        }

        $store_data['image_path'] = $url;

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

    public function updateImage(Request $request, $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'image' => 'required',
        ]);

        if ($validator->fails())
            return $this->sendError('Validation error', $validator->errors());

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $extension = $request->image->extension();
                $name = $_SERVER['REQUEST_TIME'];
                $request->image->storeAs('/public', $name.".".$extension);
                $menu->image_path = Storage::url($name.".".$extension);
                $menu->save();
                return $this->sendResponse(null, 'Update image success');
            }
        }

        return $this->sendError('Update image failed');
    }
}
