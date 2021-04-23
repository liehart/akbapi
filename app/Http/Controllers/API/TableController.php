<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $table = Table::paginate(15);
        $table->onEachSide(2);
        $table->setPath('');

        if (count($table) > 0)
            return $this->sendResponse($table, 'Tables retrieved successfully');

        return $this->sendResponse($table, 'Tables empty');
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
        $table = Table::find($id);

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

        $table->delete();

        return $this->sendResponse(null, 'Table deleted successfully.');
    }
}
