<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:150",
            "description" => "nullable|string|max:255",
            "image" => "nullable|image|max:2048",
            "display_order" => "nullable|integer|min:1"
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $category = Category::create($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Category created sucefully.",
            "i18n" => "api.categoryCreated",
            "data" => $category->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => "error",
                "message" => "Category not found.",
            ], 404);
        }
        return response()->json([
            "status" => "success",
            "data" => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => "error",
                "message" => "Category not found.",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "nullable|string|max:150",
            "description" => "nullable|string|max:255",
            "image" => "nullable|image|max:2048",
            "display_order" => "nullable|integer|min:1",
            "status" => "nullable|boolean"
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $category->update($request->all());
        return response()->json([
            "status" => "success",
            "data" => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => "error",
                "message" => "Category not found.",
            ], 404);
        }
        $category->delete();
        return response()->json([
            "status" => "success",
            "message" => "Category deleted sucefully."
        ], 204);
    }
}
