<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class ApiController extends Controller
{
    protected string $model;
    protected string $name;
    protected array $relations = [];
    protected array $with = [];
    protected ?string $formRequest = null;

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

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validateRequest($request);
            $resource = $this->model::create($data);
            $this->saveRelations($resource, $request);

            return response()->json([
                "status" => "success",
                "message" => __('api.created', ['name' => $this->getTranslatedName()]),
                "data" => $this->loadWithRelations($resource)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('api.validation_error'),
                'errors' => $e->errors()
            ], $e->status);
        }
    }

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
                "message" => __('api.not_found', ['name' => $this->getTranslatedName()]),
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $resource
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $resource = $this->model::find($id);

        if (!$resource) {
            return response()->json([
                "status" => "error",
                "message" => __('api.not_found', ['name' => $this->getTranslatedName()]),
            ], 404);
        }

        try {
            $data = $this->validateRequest($request);
            $resource->update($data);
            $this->saveRelations($resource, $request);

            return response()->json([
                "status" => "success",
                "data" => $this->loadWithRelations($resource),
                "message" => __('api.updated', ['name' => $this->getTranslatedName()]),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('api.validation_error'),
                'errors' => $e->errors()
            ], $e->status);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $resource = $this->model::find($id);

        if (!$resource) {
            return response()->json([
                "status" => "error",
                "message" => __('api.not_found', ['name' => $this->getTranslatedName()]),
            ], 404);
        }

        $resource->delete();

        return response()->json([
            "status" => "success",
            "message" => __('api.deleted', ['name' => $this->getTranslatedName()]),
        ], 204);
    }

    protected function validateRequest(Request $request): array
    {
        if ($this->formRequest) {
            /** @var FormRequest $formRequest */
            $formRequest = app($this->formRequest);
            return $formRequest->validated();
        }

        return $request->except($this->getRelationFields());
    }

    protected function saveRelations($model, Request $request): void
    {
        foreach ($this->relations as $relationName => $relationSetup) {
            if (!$request->has($relationName)) {
                continue;
            }

            $saveMethod = $relationSetup['method'] ?? 'sync';
            $requestItems = $request->input($relationName);

            $pivotData = $this->buildRelationData($requestItems, $relationSetup);

            $model->$relationName()->$saveMethod($pivotData);
        }
    }

    protected function buildRelationData(array $items, array $setup): array
    {
        $fields = $setup['fields'] ?? [];
        $result = [];

        foreach ($items as $item) {
            $id = $item['id'];
            $pivotValues = [];

            foreach ($fields as $field) {
                $pivotValues[$field] = $item[$field] ?? null;
            }

            $result[$id] = $pivotValues;
        }

        return $result;
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

    protected function getTranslatedName(): string
    {
        $key = 'entities.' . strtolower($this->name);
        $translated = __($key);

        return $translated !== $key ? $translated : $this->name;
    }
}
