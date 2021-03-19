<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmployeeRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeRoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $role = EmployeeRole::all();

        if (count($role) > 0)
            return $this->sendResponse($role, 'Role retrieved successfully');

        return $this->sendError('Role empty');
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
            'name' => 'required|max:100|unique:employee_roles',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $role = EmployeeRole::create($requestData);

        return $this->sendResponse($role, 'Role create success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $role = EmployeeRole::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        return $this->sendResponse($role, 'Role retrieved successfully.');
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
        $role = EmployeeRole::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|unique:employee_roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $role->name = $requestData['name'];
        $role->save();

        return $this->sendResponse($role, 'Role update success', 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $role = EmployeeRole::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        $role->delete();

        return $this->sendResponse(null, 'Role deleted successfully.');
    }
}
