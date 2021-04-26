<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 * @package App\Http\Controllers\API
 */
class AuthController extends BaseController
{
    /**
     * Retrieve current logged in user by bearer token
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $success['status'] = "OK";
        $success['message'] = "Detail pengguna berhasil didapatkan";
        $success['user'] = $user;
        $success['scope'] = $user->role->permission->pluck('name');
        return response()->json($success);
    }

    /**
     * Login Controller
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('V_ERR', $validator->errors());
        }

        if (Auth::attempt([
            'email' => $requestData['email'],
            'password' => $requestData['password']
        ])) {
            $user = Employee::with('role')->find(Auth::id());
            if ($user->is_disabled) {
                return $this->sendError('Maaf akun anda telah di nonaktifkan', null, 401);
            }
            $success['token'] = $user->createToken('Personal Access Token')->accessToken;
            return $this->sendResponse($success, 'User login success');
        }

        return $this->sendError('Tidak dapat masuk, alamat email atau password salah.', null, 401);
    }

    /**
     * Logout Controller
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->token()->revoke();
        return $this->sendResponse(null, 'Berhasil keluar dari aplikasi');
    }
}
