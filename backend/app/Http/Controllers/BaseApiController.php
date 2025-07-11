<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseApiController extends Controller
{
    protected $model;
    protected $name;
    protected $storeRules = [];
    protected $updateRules = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data" => $this->model::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->storeRules);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $resource = $this->model::create($request->all());

        return response()->json([
            "status" => "success",
            "message" => "{$this->name} created sucefully.",
            "i18n" => "api." . strtolower($this->name) . "Created",
            "data" => $resource->fresh()
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $resource = $this->model::find($id);

        if (!$resource) {
            return response()->json([
                "status" => "error",
                "message" => "{$this->name} not found.",
                "i18n" => "api." . strtolower($this->name) . "NotFound"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $resource
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $resource = $this->model::find($id);

        if (!$resource) {
            return response()->json([
                "status" => "error",
                "message" => "{$this->name} not found.",
                "i18n" => "api." . strtolower($this->name) . "NotFound"
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->updateRules);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $resource->update($request->all());

        return response()->json([
            "status" => "success",
            "data" => $resource,
            "i18n" => "api." . strtolower($this->name) . "Updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $resource = $this->model::find($id);

        if (!$resource) {
            return response()->json([
                "status" => "error",
                "message" => "{$this->name} not found.",
                "i18n" => "api." . strtolower($this->name) . "NotFound"
            ], 404);
        }

        $resource->delete();

        return response()->json([
            "status" => "success",
            "message" => "{$this->name} deleted sucefully.",
            "i18n" => "api." . strtolower($this->name) . "Deleted"
        ], 204);
    }
}
