<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::query()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => $customers,
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'string', 'unique:customers,customer_id'],
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email', 'unique:customers,email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $data['status'] ?? true;
        $customer = Customer::query()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'data' => $customer,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $data = $request->validate([
            'customer_id' => ['sometimes', 'string', 'unique:customers,customer_id,' . $id],
            'name' => ['sometimes', 'string'],
            'email' => ['nullable', 'email', 'unique:customers,email,' . $id],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        if ($customer->subscriptions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Customer cannot be deleted because they have active subscriptions',
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
            'data' => null,
        ]);
    }

    public function getByStatus(Request $request): JsonResponse
    {
        $status = $request->query('status');

        if ($status === null || !in_array($status, ['active', 'inactive'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'status' => ['The status parameter is required and must be active or inactive.'],
                ],
            ], 422);
        }

        $customers = Customer::query()
            ->where('status', $status === 'active')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully by status',
            'data' => $customers,
        ]);
    }

    public function changeStatus(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->update([
            'status' => !$customer->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully',
            'data' => $customer,
        ]);
    }
}