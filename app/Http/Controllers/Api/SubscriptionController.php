<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::with(['customer', 'service'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions retrieved successfully',
            'data' => $subscriptions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'service_id' => ['required', 'exists:services,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $data['status'] ?? true;
        $subscription = Subscription::query()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription->load(['customer', 'service']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::with(['customer', 'service'])->find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription retrieved successfully',
            'data' => $subscription,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Update operation is not allowed for subscriptions.'
        ], 405);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Delete operation is not allowed for subscriptions.'
        ], 405);
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

        $subscriptions = Subscription::with(['customer', 'service'])
            ->where('status', $status === 'active')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions retrieved successfully by status',
            'data' => $subscriptions,
        ]);
    }

    public function changeStatus(int $id): JsonResponse
    {
        $subscription = Subscription::query()->find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        $subscription->update([
            'status' => !$subscription->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully',
            'data' => $subscription->load(['customer', 'service']),
        ]);
    }
}