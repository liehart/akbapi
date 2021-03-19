<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TransactionCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

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
            'cardholder_number' => 'required|digits_between:13,16|unique:transaction_cards',
            'cardholder_exp_month' => 'required|between:1,12',
            'cardholder_exp_year' => 'required|digits:4',
            'cardholder_ccv' => 'required|digits_between:3,4'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $cardValidator = Validator::make($requestData, [
            'cardholder_number' => ['required', new CardNumber()],
            'cardholder_exp_month' => ['required', new CardExpirationMonth($requestData['cardholder_exp_year'])],
            'cardholder_exp_year' => ['required', new CardExpirationYear($requestData['cardholder_exp_month'])],
            'cardholder_ccv' => ['required', new CardCvc($requestData['cardholder_number'])]
        ]);

        if ($cardValidator->fails()) {
            return $this->sendError('Validation error', $cardValidator->errors());
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $card = TransactionCard::find($id);

        if (is_null($card))
            return $this->sendError('Transaction card not found');

        $card->delete();

        return $this->sendResponse($card, 'Transaction card deleted successfully.');
    }
}
