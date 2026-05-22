<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // 1. GET ALL DATA
    public function index(): JsonResponse
    {
        $services = Service::query()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Services retrieved successfully',
            'data' => $services,
        ]);
    }

    // 2. CREATE DATA
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $data['status'] ?? true;
        $service = Service::query()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service,
        ], 201);
    }

    // 3. GET DATA BY ID
    public function show(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
                'errors' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service retrieved successfully',
            'data' => $service,
        ]);
    }

    // 4. UPDATE DATA
    public function update(Request $request, int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
                'errors' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service,
        ]);
    }

    // 5. DELETE DATA
    public function destroy(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
                'errors' => [],
            ], 404);
        }

        if ($service->subscriptions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Service cannot be deleted because it has subscriptions',
                'errors' => [],
            ], 422);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
            'data' => null,
        ]);
    }

    // 6. GET ALL DATA BY STATUS
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

        $services = Service::query()
            ->where('status', $status === 'active')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Services retrieved successfully by status',
            'data' => $services,
        ]);
    }

    // 7. CHANGE STATUS (Endpoint Tunggal Pembalik Status)
    public function changeStatus(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
                'errors' => [],
            ], 404);
        }

        $service->update([
            'status' => !$service->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service status updated successfully',
            'data' => $service,
        ]);
    }
}