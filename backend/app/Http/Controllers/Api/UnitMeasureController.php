<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\UnitMeasure;
use Illuminate\Http\JsonResponse;

class UnitMeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => UnitMeasure::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:150",
            "abbreviation" => "nullable|string|max:15",
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $supplier = UnitMeasure::create($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Unit measure created sucefully.",
            "i18n" => "api.unitMeasureCreated",
            "data" => $supplier->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $supplier = UnitMeasure::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "Unit measure not found.",
            ], 404);
        }
        return response()->json([
            "status" => "success",
            "data" => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = UnitMeasure::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "UnitMeasure not found.",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:150",
            "abbreviation" => "nullable|string|max:15",
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $supplier->update($request->all());
        return response()->json([
            "status" => "success",
            "data" => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $supplier = UnitMeasure::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "Unit measure not found.",
            ], 404);
        }
        $supplier->delete();
        return response()->json([
            "status" => "success",
            "message" => "Unit measure deleted sucefully."
        ], 204);
    }
}
