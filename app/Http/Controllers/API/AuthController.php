<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\RoleHasPermissionSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function index(): JsonResponse
    {
        $userid = Auth::guard('api')->id();
        if ($userid) {
            $user = Employee::with('role')->find($userid);
            $data = [];
                        $data['user'] = $user;
            $data['scope'] = $user->role->permission->pluck('name');
            return response()->json($data, 201);
        }

        return $this->sendError('ERROR');
    }

    public function login(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        if (Auth::attempt([
            'email' => $requestData['email'],
            'password' => $requestData['password']
        ])) {
            $user = Employee::with('role.acls:role_id,object,operation')->find(Auth::id());
            if ($user->is_disabled) {
                return $this->sendError('Maaf akun anda telah di nonaktifkan');
            }
            $success['token'] = $user->createToken('melcafe')->accessToken;
            //$success['user'] = $user;
            return $this->sendResponse($success, 'User login success');
        }

        return $this->sendError('Tidak dapat masuk, alamat email atau password salah.', null, 401);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $user->token()->revoke();
            return $this->sendResponse(null, 'Logout success');
        }

        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);

    }
}
