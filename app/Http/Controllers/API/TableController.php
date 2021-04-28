<?php

namespace App\Http\Controllers\API;

use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->query('query');
        $date = $request->query('date');
        $session = $request->query('session');
        $edit = $request->query('edit');

        $tables = Table::with(['reservation' => function ($qq) use ($request, $date, $session, $edit) {
            $qq->with('customer')
                ->when($request->filled('date'), function ($q) use ($date) {
                $q->where('date', $date);
            })
                ->when($request->filled('session'), function ($q) use ($session) {
                $q->where('session', '=', $session);
            })
                ->when($request->filled('edit'), function ($q) use ($edit) {
                $q->where('id', '!=', $edit);
            })
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'done');
        }])
            ->when($request->filled('query'), function ($q) use ($query) {
                $q->where('table_number', 'like', '%' . $query . '%');
            })
            ->orderBy('table_number')
            ->paginate(15);

        $tables->onEachSide(2);
        $tables->setPath('');

        return $this->sendResponse($tables, 'OK');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'table_number' => 'required|integer|unique:tables',
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $table = Table::create($requestData);

        return $this->sendResponse($table, ' create success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $table = Table::with('reservation')
            ->find($id);

        if (is_null($table))
            return $this->sendError('Table not found');

        return $this->sendResponse($table, 'Table retrieved successfully.');
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
        $table = Table::find($id);

        if (is_null($table))
            return $this->sendError('Table not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'is_empty' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $table->is_empty = $requestData['is_empty'];
        $table->save();

        return $this->sendResponse($table, 'Table update success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $table = Table::find($id);

        if (is_null($table))
            return $this->sendError('Table not found');

        if ($table->is_empty == true) {
            return $this->sendError('IN_USE');
        }

        $table->delete();

        return $this->sendResponse(null, 'Table deleted successfully.');
    }
}
