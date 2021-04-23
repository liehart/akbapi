<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Faker\Provider\Base;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $reservation = Reservation::with('customer:id,name,deleted_at')->orderBy('reservation_date')->paginate(10);
        $reservation->onEachSide(2);
        $reservation->setPath('');

        if (count($reservation) > 0)
            return $this->sendResponse($reservation, 'Reservation retrieved successfully');

        return $this->sendResponse($reservation,'Reservation empty');
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
            'reservation_date' => 'required|date',
            'reservation_session' => 'required|in:lunch,dinner,other',
            'table_table_number' => 'required|exists:tables,table_number',
            'customer_id' => 'required|exists:customers,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $reservation = Reservation::create($store_data);

        return $this->sendResponse($reservation, 'Reservation created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (is_null($reservation))
            return $this->sendError('Reservation not found');

        return $this->sendResponse($reservation, 'Reservation retrieved successfully.');
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
        $reservation = Reservation::find($id);

        if (is_null($reservation))
            return $this->sendError('Reservation not found');

        $store_data = $request->all();
        $validator = Validator::make($store_data, [
            'reservation_date' => 'required|date',
            'reservation_session' => 'required|in:lunch,dinner,other',
            'table_table_number' => 'required|exists:tables,table_number',
            'customer_id' => 'required|exists:customers,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $reservation->reservation_date = $store_data['reservation_date'];
        $reservation->reservation_session = $store_data['reservation_session'];
        $reservation->table_table_number = $store_data['table_table_number'];
        $reservation->customer_id = $store_data['customer_id'];

        $reservation->save();

        return $this->sendResponse($reservation, 'Reservation updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (is_null($reservation))
            return $this->sendError('Reservation not found');

        $reservation->delete();

        return $this->sendResponse(null, 'Reservation deleted successfully.');
    }
}
