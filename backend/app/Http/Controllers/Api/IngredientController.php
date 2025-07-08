<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => Ingredient::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'description' => 'string',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_measure_id' => 'required|exists:unit_measures,id',
            'is_additional' => 'boolean',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $ingredient = Ingredient::create($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Ingredient created sucefully.",
            "i18n" => "api.supplierCreated",
            "data" => $ingredient->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json([
                "status" => "error",
                "message" => "Ingredient not found.",
            ], 404);
        }
        return response()->json([
            "status" => "success",
            "data" => $ingredient
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json([
                "status" => "error",
                "message" => "Ingredient not found.",
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

        $ingredient->update($request->all());
        return response()->json([
            "status" => "success",
            "data" => $ingredient
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json([
                "status" => "error",
                "message" => "Ingredient not found.",
            ], 404);
        }
        $ingredient->delete();
        return response()->json([
            "status" => "success",
            "message" => "Ingredient deleted sucefully."
        ], 204);
    }
}
