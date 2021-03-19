<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TransactionCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TransactionCardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendError('Not implemented yet.');
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
            'card_type' => 'required|in:debit,credit',
            'cardholder_name' => 'required_if:card_type,credit|max:100',
            'cardholder_number' => 'required|digits_between:15,16',
            'cardholder_exp_month' => 'required|between:1,12',
            'cardholder_exp_year' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $card = TransactionCard::create($requestData);

        return $this->sendResponse($card, 'Transaction Card create success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $card = TransactionCard::find($id);

        if (is_null($card))
            return $this->sendError('Transaction card not found');

        return $this->sendResponse($card, 'Transaction card retrieved successfully.');
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
        return $this->sendError('Not implemented yet.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return $this->sendError('Not implemented yet.');
    }
}
