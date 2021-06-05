<?php

namespace App\Http\Controllers\API;

use App\Models\Ingredient;
use App\Models\Menu;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public function showStockCustsom($start, $end){
        if(isset($start) && isset($end)) {
            if ($this->validateDate($start) && $this->validateDate($end)) {
                $stockMakanan = Ingredient::orWhereHas(
                    'in', function ($qq) use ($start, $end) {
                    $qq
                        ->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end);
                })->orWhereHas(
                    'out', function ($qq) use ($start, $end) {
                    $qq
                        ->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end);
                })->get();
                if (count($stockMakanan) > 0 ) {
                    return view('stock', compact('stockMakanan', 'start', 'end'));
                }
                else {
                    return $this->sendError('Tidak ada data untuk tanggal tersebut', 'NO_DATA');
                }
            }
        }
        return $this->sendError('DATE_INVALID');

    }

    public function showStockMenu($menu, $month, $year){
        if(isset($month) && isset($menu) && isset($year)) {
            if($month > 0 && $month <= 12 && $year > 0 && $year < 10000) {
                $menu = Menu::find($menu);
                if (!is_null($menu)) {
                    $query = "select datea as date,
    (select unit from ingredients WHERE id = " . $menu->id . ") as unit,
    CONVERT(COALESCE(sum(jumlah_masuk), 0), UNSIGNED) as jumlah_masuk,
    CONVERT((
        COALESCE(sum(jumlah_masuk), 0) - COALESCE(sum(jumlah_terjual), 0)
    ), SIGNED) as jumlah_sisa,
    CONVERT(COALESCE(sum(jumlah_keluar), 0), UNSIGNED) as jumlah_dibuang
FROM (
        SELECT DATE(date) as datea,
            CONVERT(SUM(QUANTITY), UNSIGNED) as jumlah_masuk,
            null as jumlah_keluar,
            null as jumlah_terjual
        FROM incoming_stocks
        WHERE ingredient_id = " . $menu->id . " AND MONTH(date) = " . $month . " AND YEAR(date) = " . $year . "
        GROUP BY datea
        union
        SELECT DATE(date) as datea,
            null as jumlah_masuk,
            CONVERT(
                SUM(IF(category = 'waste', quantity, 0)),
                UNSIGNED
            ) as jumlah_keluar,
            CONVERT(
                SUM(IF(category = 'sold', quantity, 0)),
                UNSIGNED
            ) as jumlah_terjual
        FROM outgoing_stocks
        WHERE ingredient_id = " . $menu->id . " AND MONTH(date) = " . $month . " AND YEAR(date) = " . $year . "
        GROUP BY datea
    ) as a
group by datea;";
                    $stockMakanan = DB::select( DB::raw($query));
                    if (count($stockMakanan) > 0 ) {
                        return view('stockMenu', compact('stockMakanan', 'month', 'year', 'menu'));
                    }
                    else {
                        return $this->sendError('Tidak ada data untuk pilihan tersebut', 'NO_DATA');
                    }
                }
                return $this->sendError('MENU_NOT_FOUND');
            }
        }
        return $this->sendError('DATE_INVALID');

    }


}
