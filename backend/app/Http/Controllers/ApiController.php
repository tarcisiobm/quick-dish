<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\Model;
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

    protected array $successTemplates = [
        'created' => ['message' => 'api.created', 'status' => 201],
        'updated' => ['message' => 'api.updated', 'status' => 200],
        'deleted' => ['message' => 'api.deleted', 'status' => 200],
    ];

    protected array $errorTemplates = [
        'not_found' => ['message' => 'api.not_found', 'status' => 404],
        'validation' => ['message' => 'api.validation_error', 'status' => 422],
    ];

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query();
        if ($this->with) {
            $query->with($this->with);
        }
        return $this->successResponse(null, $query->get());
    }

    public function show(string $id): JsonResponse
    {
        $resource = $this->findResource($id);
        if (!$resource) {
            return $this->errorResponse('not_found');
        }
        return $this->successResponse(null, $resource);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->validateRequest($request);
            $this->beforeStore($validatedData, $request);
            $resource = $this->model::create($validatedData);
            $this->saveRelations($resource, $request);
            $this->afterStore($resource, $request);
            return $this->successResponse('created', $resource);
        } catch (ValidationException $e) {
            return $this->errorResponse('validation', $e->errors());
        } catch (ApiException $e) {
            return $this->errorResponse($e->getMessage(), $e->getData(), $e->getStatusCode());
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $resource = $this->findResource($id);
            if (!$resource) {
                return $this->errorResponse('not_found');
            }
            $validatedData = $this->validateRequest($request);
            $this->beforeUpdate($validatedData, $resource, $request);
            $resource->update($validatedData);
            $this->saveRelations($resource, $request);
            $this->afterUpdate($resource, $request);
            return $this->successResponse('updated', $resource);
        } catch (ValidationException $e) {
            return $this->errorResponse('validation', $e->errors());
        } catch (ApiException $e) {
            return $this->errorResponse($e->getMessage(), $e->getData(), $e->getStatusCode());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $resource = $this->findResource($id);
            if (!$resource) {
                return $this->errorResponse('not_found');
            }
            $this->beforeDestroy($resource);
            $resource->delete();
            $this->afterDestroy($resource);
            return $this->successResponse('deleted');
        } catch (ApiException $e) {
            return $this->errorResponse($e->getMessage(), $e->getData(), $e->getStatusCode());
        }
    }

    protected function beforeStore(array $validatedData, Request $request): void
    {
    }

    protected function afterStore(Model $resource, Request $request): void
    {
    }

    protected function beforeUpdate(array $validatedData, Model $resource, Request $request): void
    {
    }

    protected function afterUpdate(Model $resource, Request $request): void
    {
    }

    protected function beforeDestroy(Model $resource): void
    {
    }

    protected function afterDestroy(Model $resource): void
    {
    }

    protected function successResponse($message = null, $data = null, int $status = 200): JsonResponse
    {
        if (is_string($message) && isset($this->successTemplates[$message])) {
            return $this->buildSuccessFromTemplate($message, $data);
        }
        return $this->buildSuccessResponse($message, $data, $status);
    }

    protected function errorResponse($message, $errors = null, ?int $status = null): JsonResponse
    {
        if (is_string($message) && isset($this->errorTemplates[$message])) {
            return $this->buildErrorFromTemplate($message, $errors);
        }
        return $this->buildErrorResponse($message, $errors, $status ?? 400);
    }

    private function buildSuccessFromTemplate(string $template, $resource): JsonResponse
    {
        $config = $this->successTemplates[$template];
        $message = __($config['message'], ['name' => $this->getTranslatedName()]);
        $data = $this->loadWithRelations($resource);
        return $this->buildSuccessResponse($message, $data, $config['status']);
    }

    private function buildErrorFromTemplate(string $template, $errors): JsonResponse
    {
        $config = $this->errorTemplates[$template];
        $message = __($config['message'], ['name' => $this->getTranslatedName()]);
        return $this->buildErrorResponse($message, $errors, $config['status']);
    }

    private function buildSuccessResponse($message, $data, int $status): JsonResponse
    {
        $response = ['status' => 'success'];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $status);
    }

    private function buildErrorResponse($message, $errors, int $status): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        if ($errors) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }

    protected function validateRequest(Request $request): array
    {
        if (!$this->formRequest) {
            return $request->except($this->getRelationFields());
        }
        $formRequest = app($this->formRequest);
        return $formRequest->validated();
    }

    protected function saveRelations(Model $model, Request $request): void
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

    protected function findResource(string $id): ?Model
    {
        $query = $this->model::query();
        if ($this->with) {
            $query->with($this->with);
        }
        return $query->find($id);
    }

    protected function getRelationFields(): array
    {
        return array_keys($this->relations);
    }

    protected function loadWithRelations($resource)
    {
        if ($this->with && $resource) {
            return $resource->load($this->with);
        }
        return $resource;
    }

    protected function getTranslatedName(): string
    {
        $key = 'entities.' . strtolower($this->name);
        $translated = __($key);
        return $translated !== $key ? $translated : $this->name;
    }
}
