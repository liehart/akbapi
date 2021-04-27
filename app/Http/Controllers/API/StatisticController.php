<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'reservation' => $reservation
        );

        return $this->sendResponse($resp, 'OK');
    }
}
