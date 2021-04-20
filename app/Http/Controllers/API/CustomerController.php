<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $customer = Customer::paginate(5)->onEachSide(2);

        if (count($customer) > 0)
            return $this->sendResponse($customer, 'Customers retrieved successfully');

        return $this->sendResponse($customer, 'Customers empty');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'nullable|email:rfc,dns',
            'phone' => 'nullable|digits_between:10,13'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $customer = Customer::create($requestData);

        return $this->sendResponse($customer, 'Customer create success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (is_null($customer))
            return $this->sendError('Customer not found');

        return $this->sendResponse($customer, 'Customer retrieved successfully.');
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
        $customer = Customer::find($id);

        if (is_null($customer))
            return $this->sendError('Customer not found');

        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'nullable|email:rfc,dns',
            'phone' => 'nullable|digits_between:10,15'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors());
        }

        $customer->name = $requestData['name'];
        if (isset($requestData['email'])) {
            $customer->email = $requestData['email'];
        }
        if (isset($requestData['phone'])) {
            $customer->phone = $requestData['phone'];
        }

        $customer->save();

        return $this->sendResponse($customer, 'Customer update success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (is_null($customer))
            return $this->sendError('Customer not found');

        $customer->delete();

        return $this->sendResponse(null, 'Customer deleted successfully.');
    }
}
