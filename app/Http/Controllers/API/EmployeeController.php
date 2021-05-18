<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use Dotenv\Util\Str;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class EmployeeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $employee = Employee::with('role')
            ->paginate(5)
            ->onEachSide(2)
            ->setPath('');

        if (count($employee) > 0)
            return $this->sendResponse($employee, 'Employee retrieved successfully');

        return $this->sendError('Employee empty');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');
        $employees = Employee::with('role')
            ->orderBy('name')
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->orWhere('phone', 'like', '%' . $query . '%')
            ->paginate(10);
        $employees->onEachSide(2);
        $employees->setPath('');

        return $this->sendResponse($employees, 'OK');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email:rfc,dns|unique:employees',
            'phone' => 'required|digits_between:10,15',
            'date_join' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $requestData['password'] = bcrypt($requestData['password']);

        $requestData['image_path'] = $this->getImagePath(
            $requestData['image_path'],
            $requestData['properties'],
            $requestData['image_delete']
        );


        $employee = Employee::create($requestData);

        return $this->sendResponse($employee, 'Employee created success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $employee = Employee::with('role')->find($id);

        if (is_null($employee))
            return $this->sendError('Employee not found');

        return $this->sendResponse($employee, 'Employee retrieved successfully.');
    }

    public function deactivate(int $id): JsonResponse
    {
        $employee = Employee::with('role')->find($id);

        if (is_null($employee))
            return $this->sendError('Employee not found');

        $employee->is_disabled = true;
        $employee->save();

        return $this->sendResponse($employee, 'Pegawai berhasil dinonaktifkan.');
    }

    public function activate(int $id): JsonResponse
    {
        $employee = Employee::with('role')->find($id);

        if (is_null($employee))
            return $this->sendError('Employee not found');

        $employee->is_disabled = false;
        $employee->save();

        return $this->sendResponse($employee, 'Pegawai berhasil diaktifkan kembali.');
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
        $employee = Employee::find($id);

        if (is_null($employee))
            return $this->sendError('Employee not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email:rfc,dns|unique:employees,email,' . $employee->id,
            'phone' => 'required|digits_between:10,15',
            'date_join' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        $employee->name = $requestData['name'];
        $employee->email = $requestData['email'];
        $employee->phone = $requestData['phone'];
        $employee->date_join = $requestData['date_join'];
        $employee->gender = $requestData['gender'];
        $employee->role_id = $requestData['role_id'];

        if ($requestData['image_change']) {
            $employee->image_path = $this->getImagePath(
                $requestData['image_path'],
                $requestData['properties'],
                $requestData['image_delete']
            );
        }

        $employee->save();

        return $this->sendResponse($employee, 'Employee updated success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (is_null($employee))
            return $this->sendError('Employee not found');

        $employee->delete();

        return $this->sendResponse(null, 'Employee deleted successfully.');
    }

    /**
     * Update password for logged in user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::user();

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|different:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        if (Hash::check($requestData['password'], Auth::user()->getAuthPassword())) {
            $user->password = bcrypt($requestData['new_password']);
            $user->save();

            return $this->sendResponse(null, 'Employee updated successfully.');
        }
    }

    public function getImagePath($image_path, $properties, $image_delete, $scope = 'avatar'): string
    {
        if ($image_delete) {
            return '';
        } else {
            if ($image_path) {
                $disk = Storage::disk('s3');
                $temporary = $disk->get('temporary/' . $image_path);
                $image = Image::make($temporary)
                    ->crop(
                        $properties['width'],
                        $properties['height'],
                        $properties['x'],
                        $properties['y'])
                    ->resize(500, 500);
                $disk->put($scope . '/' . $image_path, $image->stream(), 'public');
                return $disk->url($scope . '/' . $image_path);
            }
        }
        return '';
    }

}

