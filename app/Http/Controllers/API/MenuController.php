<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends BaseController
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
        $filter = $v->filter ?? [];

        $menus = Menu::when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->where(function($q) use ($filter){
                foreach($filter as $item){
                    $q->when($item->value != [] || $item->value, function ($qq) use ($item) {
                        $qq->whereIn($item->name, $item->value ?? ['*']);
                    });
                }
            })
            ->with('ingredient')
            ->orderBy($sort, $asc == 'true' ? 'asc' : 'desc')
                ->paginate($show, ['*'], 'page', $page)
            ->onEachSide(2)
            ->setPath('');

        return $this->sendResponse($menus, 'Menu retrieved successfully');
    }

    public function indexPost(Request $request): JsonResponse
    {
        $data = $request->all();

        $menus = Menu::orderBy('name')
            ->when($data['query'], function ($q) use ($data) {
                $q->where('name', 'like', '%' . $data['query'] . '%');
            })
            ->when($data['category'], function ($q) use ($data) {
                $q->whereIn('menu_type', $data['category']);
            })
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
            'menu_type' => 'required|in:side_dish,drink,main',
            'ingredient_id' => 'required|exists:ingredients,id|unique:menus,ingredient_id',
            'serving_size' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
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
            'menu_type' => 'required|in:side_dish,drink,main',
            'ingredient_id' => 'required|exists:ingredients,id|unique:menus,ingredient_id,' . $menu->id,
            'serving_size' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $menu->name = $store_data['name'];
        $menu->description = $store_data['description'];
        $menu->unit = $store_data['unit'];
        $menu->price = $store_data['price'];
        $menu->menu_type = $store_data['menu_type'];
        $menu->ingredient_id = $store_data['ingredient_id'];
        $menu->serving_size = $store_data['serving_size'];
        $menu->image_path = $store_data['image_path'];

        $menu->save();

        return $this->sendResponse($menu, 'Menu updated successfully');
    }

    public function enable(int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        if ($menu->ingredient->remaining_stock > 0) {
            $menu->is_available = !$menu->is_available;
            $menu->save();
            return $this->sendResponse(null, 'Menu deleted successfully.');
        } else {
            return $this->sendError('Stok bahan menu ini kosong.');
        }

    }

    public function refresh(int $id): JsonResponse
    {
        $menu = Menu::find($id);

        if (is_null($menu))
            return $this->sendError('Menu not found');

        $in = DB::table('incoming_stocks')->whereNull('deleted_at')->where('ingredient_id', '=', $menu->ingredient->id)->sum('quantity');
        $out = DB::table('outgoing_stocks')->whereNull('deleted_at')->where('ingredient_id', '=', $menu->ingredient->id)->sum('quantity');

        $menu->ingredient->remaining_stock = $in - $out;
        $menu->ingredient->save();

        return $this->sendResponse($in - $out, 'Menu deleted successfully.');
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
