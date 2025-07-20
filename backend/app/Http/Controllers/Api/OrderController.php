<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends ApiController
{
    protected string $model = Order::class;
    protected string $name = 'Order';
    protected ?string $formRequest = OrderRequest::class;
    protected array $with = [
        'user',
        'employee',
        'delivery',
        'table',
        'items.product',
        'items.additionals.ingredient'
    ];
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->validateRequest($request);
            $order = $this->orderService->create($validatedData);
            return $this->successResponse('created', $this->loadWithRelations($order));
        } catch (ValidationException $e) {
            return $this->errorResponse('validation', $e->errors());
        } catch (ApiException $e) {
            return $this->errorResponse($e->getMessage(), $e->getData(), $e->getStatusCode());
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $order = $this->findResource($id);
            if (!$order) {
                return $this->errorResponse('not_found');
            }

            $validatedData = $this->validateRequest($request);
            $updatedOrder = $this->orderService->updateStatus($order, $validatedData['status']);

            return $this->successResponse('updated', $this->loadWithRelations($updatedOrder));
        } catch (ValidationException $e) {
            return $this->errorResponse('validation', $e->errors());
        } catch (ApiException $e) {
            return $this->errorResponse($e->getMessage(), $e->getData(), $e->getStatusCode());
        }
    }
}
