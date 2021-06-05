<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Class BaseController
 * @package App\Http\Controllers\API
 */
class BaseController extends Controller
{
    /**
     * Send response is a constructor for sending successful JSON response
     *
     * @param $result
     * @param $message
     * @param int $resp_code
     * @return JsonResponse
     */
    public function sendResponse($result, $message, int $resp_code = 200, bool $success = true): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $result,
        ];
        return response()->json($response, $resp_code);
    }

    /**
     * Send response in general but allow to handle more error detail
     *
     * @param $error
     * @param array $errorMessage
     * @param int $errorCode
     * @return JsonResponse
     */
    public function sendError($error, $errorMessage = [], int $errorCode = 404): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $error,
        ];
        if (!empty($errorMessage))
            $response['data'] = $errorMessage;
        return response()->json($response, $errorCode);
    }
}
