<?php

namespace App\Http\Controllers\API;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends BaseController
{
    public function avatar(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'file' => 'required|max:10240|dimensions:min_width=100,
            min_height=100,max_width=2500,max_height=2500|image|
            mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $disk = Storage::disk('s3');
        $file = $request->file('file');
        $avatar = $file->store('temporary', 's3');
        $res['path'] = basename($disk->path($avatar));
        $res['presigned_url'] = $disk->temporaryUrl($avatar, Carbon::now()->addMinutes(60));

        return $this->sendResponse($res, 'File upload success');
    }

    public function menu(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'file' => 'required|max:10240|dimensions:min_width=100,
            min_height=100,max_width=2500,max_height=2500|image|
            mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $disk = Storage::disk('s3');
        $file = $request->file('file');
        $image = $file->storePublicly('menu', 's3');
        $res['path'] = $disk->url($image);

        return $this->sendResponse($res, 'File upload success');
    }
}
