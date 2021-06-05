<?php

namespace App\Http\Controllers\API;

use App\Models\Reservation;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $from = $request->query('from');
        $to = $request->query('to');
        $hide = $request->query('hide');
        $rawStatus = $request->query('status');
        $status = json_decode($rawStatus);

        $reservation = Reservation::with('customer')
            ->whereHas(
            'customer', function ($qq) use ($request, $search) {
                $qq->when($request->filled('search'), function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('from'), function ($q) use ($from) {
                $q->where('date', '>=', $from);
            })
            ->when($request->filled('to'), function ($q) use ($to) {
                $q->where('date', '<=', $to);
            })
            ->when($request->filled('hide'), function ($q) use ($hide) {
                if ($hide == 1) {
                    $q->where('date', '>=', Carbon::today());
                }
            })
            ->when($request->filled('status '), function ($q) use ($status) {
                $q->whereIn('status', $status);
            })
            ->with('table')
            ->orderBy('date')
            ->paginate(10);
        $reservation->onEachSide(2);
        $reservation->setPath('');

        if (count($reservation) > 0)
            return $this->sendResponse($reservation, 'Reservation retrieved successfully');

        return $this->sendResponse($reservation, 'Reservation empty');
    }

    public function select(Request $request): JsonResponse
    {
        $v = json_decode($request->query('v'));

        $query = $v->query ?? '';
        $session = $v->session ?? 'lunch';

        $date = Carbon::today()->toDateString();

        $orders = Order::get()->pluck('reservation_id');

        $reservations = Reservation::with('customer')
            ->whereHas(
            'customer', function ($qq) use ($query) {
                $qq->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                });
            })
            ->when($session, function ($q) use ($session) {
                $q->where('session', '=', $session);
            })
            ->where('date', '=', $date)
            ->where('status', '=', 'new')
            ->whereNotIn('id', $orders)
            ->take(3)
            ->get();
                
        return $this->sendResponse($reservations, 'Ingredients retrieved successfully');
    }

    public function search(Request $request): JsonResponse
    {

        $data = $request->all();
        $search = $data['search'] ?? '';
        $from = $data['from'] ?? '';
        $hide = $data['hide'] ?? '';
        $to = $data['to'] ?? '';
        $status = $data['status'] ?? [];

        $reservation = Reservation::with('customer')
            ->whereHas(
                'customer', function ($qq) use ($request, $search) {
                $qq->when($request->filled('search'), function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('from'), function ($q) use ($from) {
                $q->where('date', '>=', $from);
            })
            ->when($request->filled('to'), function ($q) use ($to) {
                $q->where('date', '<=', $to);
            })
            ->when($request->filled('hide'), function ($q) use ($hide) {
                if ($hide == 1) {
                    $q->where('date', '>=', Carbon::today());
                }
            })
            ->when($request->filled('status'), function ($q) use ($status) {
                $q->whereIn('status', $status);
            })
            ->with('table')
            ->orderBy('date')
            ->paginate(10);
        $reservation->onEachSide(2);
        $reservation->setPath('');

        if (count($reservation) > 0)
            return $this->sendResponse($reservation, 'Reservation retrieved successfully');

        return $this->sendResponse($reservation, 'Reservation empty');
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
            'date' => 'required|date',
            'session' => 'required|in:lunch,dinner',
            'table_number' => 'required|exists:tables,table_number',
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
        $reservation = Reservation::with('customer')->find($id);

        if (is_null($reservation))
            return $this->sendError('Reservation not found');

        return $this->sendResponse($reservation, 'Reservation retrieved successfully.');
    }

    public function cancel(int $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (is_null($reservation))
            return $this->sendError('Reservation not found');

        $reservation->status = 'cancelled';

        $reservation->save();

        return $this->sendResponse($reservation, 'Reservation updated successfully');

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
            'date' => 'required|date',
            'session' => 'required|in:lunch,dinner',
            'table_number' => 'required|exists:tables,table_number',
            'customer_id' => 'required|exists:customers,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $reservation->date = $store_data['date'];
        $reservation->session = $store_data['session'];
        $reservation->table_number = $store_data['table_number'];
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
