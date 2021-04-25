<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('permission')->paginate(10);

        foreach($roles as $role)
        {
            $count = Employee::orderBy('name')->where('role_id', $role->id)->count();
            $employee = Employee::where('role_id', $role->id)
                ->select(['name', 'image_path'])
                ->limit(5)
                ->get();
            $role->employees = $employee;
            $role->count = $count - 5;
        }

        $roles->onEachSide(2);
        $roles->setPath('');

        return $this->sendResponse($roles, 'Role retrieved successfully');
    }

    public function select(): JsonResponse
    {
        $role = Role::all('name', 'id as value');

        if (count($role) > 0)
            return $this->sendResponse($role, 'Role retrieved successfully');

        return $this->sendError('Role empty');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');
        $roles = Role::orderBy('name')->where('name', 'like', '%' . $query . '%')
            ->paginate(10);

        foreach($roles as $role)
        {
            $count = Employee::orderBy('name')->where('role_id', $role->id)->count();
            $employee = Employee::where('role_id', $role->id)
                ->select(['name', 'image_path'])
                ->limit(5)
                ->get();
            $role->employees = $employee;
            $role->count = $count - 5;
        }

        $roles->onEachSide(2);
        $roles->setPath('');

        return $this->sendResponse($roles, 'OK');
    }

    public function permission(int $id, Request $request): JsonResponse
    {
        $role = Role::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|exists:permissions',
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $permission = Permission::where('name', '=', $requestData['name'])->first();

        $role->permission()->syncWithoutDetaching($permission);

        return $this->sendResponse($role, 'OK');
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
            'slug' => 'required|max:100|unique:employee_roles'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $role = Role::create($requestData);

        return $this->sendResponse($role, 'Role create success');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::with('permission')->find($id);

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
        $role = Role::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|unique:employee_roles,name,' . $role->id,
            'slug' => 'required|max:100|unique:employee_roles,slug,' . $role->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $role->name = $requestData['name'];
        $role->slug = $requestData['slug'];
        $role->save();

        return $this->sendResponse($role, 'Role update success');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $role = Role::find($id);

        if (is_null($role))
            return $this->sendError('Role not found');

        $role->delete();

        return $this->sendResponse(null, 'Role deleted successfully.');
    }
}
