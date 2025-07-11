<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseApiController extends Controller
{
    protected string $model;
    protected string $name;
    protected array $storeRules = [];
    protected array $updateRules = [];
    protected array $relations = [];
    protected array $with = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $query = $this->model::query();

        if ($this->with) {
            $query->with($this->with);
        }

        return response()->json([
            "status" => "success",
            "data" => $query->get()
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

        $resource = $this->model::create($request->except($this->getRelationFields()));

        $this->handleRelations($resource, $request);

        return response()->json([
            "status" => "success",
            "message" => "{$this->name} created successfully.",
            "i18n" => "api." . strtolower($this->name) . "Created",
            "data" => $this->loadWithRelations($resource)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $query = $this->model::query();

        if ($this->with) {
            $query->with($this->with);
        }

        $resource = $query->find($id);

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
    public function update(Request $request, string $id): JsonResponse
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

        $resource->update($request->except($this->getRelationFields()));

        $this->handleRelations($resource, $request);

        return response()->json([
            "status" => "success",
            "data" => $this->loadWithRelations($resource),
            "message" => "{$this->name} updated successfully.",
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
            "message" => "{$this->name} deleted successfully.",
            "i18n" => "api." . strtolower($this->name) . "Deleted"
        ], 204);
    }

    protected function handleRelations($resource, Request $request): void
    {
        foreach ($this->relations as $relation => $config) {
            if (!$request->has($relation)) continue;

            $method = $config['method'] ?? 'sync';
            $data = $this->prepareRelationData($request->get($relation), $config);

            $resource->$relation()->$method($data);
        }
    }


    protected function prepareRelationData(array $data, array $config): array
    {
        $relationData = [];
        $fields = $config['fields'] ?? [];

        foreach ($data as $item) {
            $id = $item['id'];
            $relationData[$id] = [];

            foreach ($fields as $field) {
                $relationData[$id][$field] = $item[$field] ?? null;
            }
        }

        return $relationData;
    }

    protected function getRelationFields(): array
    {
        return array_keys($this->relations);
    }

    protected function loadWithRelations($resource)
    {
        if ($this->with) {
            return $resource->load($this->with);
        }

        return $resource->fresh();
    }
}
