<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => Supplier::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:150",
            "cnpj" => "nullable|string|max:20",
            "phone" => "nullable|string|max:20",
            "email" => "nullable|string|max:150"
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $supplier = Supplier::create($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Supplier created sucefully.",
            "i18n" => "api.supplierCreated",
            "data" => $supplier->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "Supplier not found.",
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
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "Supplier not found.",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:150",
            "cnpj" => "nullable|string|max:20",
            "phone" => "nullable|string|max:20",
            "email" => "nullable|string|max:150",
            "status" => "nullable|boolean"
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
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                "status" => "error",
                "message" => "Supplier not found.",
            ], 404);
        }
        $supplier->delete();
        return response()->json([
            "status" => "success",
            "message" => "Supplier deleted sucefully."
        ], 204);
    }
}
