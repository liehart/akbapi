<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OutgoingStock;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $reservation['today'] = Reservation::whereDate('date', Carbon::today())
            ->count();
        $reservation['yesterday'] = Reservation::whereDate('date', Carbon::yesterday())
            ->count();
        $diff = $reservation['today'] - $reservation['yesterday'];
        $reservation['diff'] = $diff;

        $resp = array(
            'reservation' => $reservation,
            'top_item' => $this->getTopItemThisMonth()
        );

        return $this->sendResponse($resp, 'OK');
    }

    public function getTopItemThisMonth() {
        $query = "select menus.name as name, sum(quantity) as quantity from outgoing_stocks join ingredients on ingredients.id = outgoing_stocks.ingredient_id join menus on ingredients.id = menus.ingredient_id where category = 'sold' and month(date) = month(curdate()) group by MONTH(date), name order by quantity desc limit 5";
        return DB::select($query);
    }
}
