<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // 1. GET ALL SERVICES (Bisa filter via query parameter ?status=active/inactive)
    public function index(Request $request): JsonResponse
    {
        $status = $request->query("status");
        $query = Service::query();

        if ($status !== null) {
            if (!in_array($status, ["active", "inactive"], true)) {
                return response()->json([
                    "success" => false,
                    "message" => "Validation failed",
                    "errors" => [
                        "status" => ["The selected status is invalid."],
                    ],
                ], 422);
            }

            $query->where("status", $status === "active");
        }

        $services = $query->latest()->get();

        return response()->json([
            "success" => true,
            "message" => "Services retrieved successfully",
            "data" => $services,
        ]);
    }

    // 2. CREATE NEW SERVICE
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string"],
            "price" => ["required", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
        ]);

        $data["status"] = $data["status"] ?? true;
        $service = Service::query()->create($data);

        return response()->json([
            "success" => true,
            "message" => "Service created successfully",
            "data" => $service,
        ], 201);
    }

    // 3. SHOW SINGLE SERVICE BY ID
    public function show(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Service not found",
                "errors" => [],
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Service retrieved successfully",
            "data" => $service,
        ]);
    }

    // 4. UPDATE SERVICE BY ID
    public function update(Request $request, int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Service not found",
                "errors" => [],
            ], 404);
        }

        $data = $request->validate([
            "name" => ["sometimes", "string"],
            "price" => ["sometimes", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
        ]);

        $service->update($data);

        return response()->json([
            "success" => true,
            "message" => "Service updated successfully",
            "data" => $service,
        ]);
    }

    // 5. DELETE SERVICE (Akan gagal jika service memiliki data subscription)
    public function destroy(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Service not found",
                "errors" => [],
            ], 404);
        }

        if ($service->subscriptions()->exists()) {
            return response()->json([
                "success" => false,
                "message" => "Service cannot be deleted because it has subscriptions",
                "errors" => [],
            ], 422);
        }

        $service->delete();

        return response()->json([
            "success" => true,
            "message" => "Service deleted successfully",
            "data" => null,
        ]);
    }

    // 6. PATCH ACTIVATE SERVICE
    public function activate(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Service not found",
                "errors" => [],
            ], 404);
        }

        $service->update(["status" => true]);

        return response()->json([
            "success" => true,
            "message" => "Service activated successfully",
            "data" => $service,
        ]);
    }

    // 7. PATCH DEACTIVATE SERVICE
    public function deactivate(int $service): JsonResponse
    {
        $service = Service::query()->find($service);

        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Service not found",
                "errors" => [],
            ], 404);
        }

        $service->update(["status" => false]);

        return response()->json([
            "success" => true,
            "message" => "Service deactivated successfully",
            "data" => $service,
        ]);
    }
}