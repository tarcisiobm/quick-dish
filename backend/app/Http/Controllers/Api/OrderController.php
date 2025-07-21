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


    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query();

        if ($this->with) {
            $query->with($this->with);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        return $this->successResponse(null, $query->get());
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

    public function userOrders(Request $request): JsonResponse
    {
        $orders = $this->model::where('user_id', $request->user()->id)
            ->with($this->with)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(null, $orders);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($request->user()->id !== $order->user_id) {
            return $this->errorResponse('Unauthorized', null, 403);
        }

        if (!in_array($order->status, ['pending', 'preparing'])) {
            return $this->errorResponse('This order can no longer be cancelled.', null, 422);
        }

        $order->status = 'cancelled';
        $order->save();
        return $this->successResponse('Order cancelled successfully.', $this->loadWithRelations($order));
    }
}
