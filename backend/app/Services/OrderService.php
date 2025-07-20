<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderPriceService $orderPriceService;
    private StockService $stockService;

    public function __construct(
        OrderPriceService $orderPriceService,
        StockService $stockService
    ) {
        $this->orderPriceService = $orderPriceService;
        $this->stockService = $stockService;
    }

    public function create(array $requestData): Order
    {
        $summary = $this->orderPriceService->calculate($requestData);

        return $this->save($requestData, $summary);
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        if ($newStatus === 'preparing' && $order->status === 'confirmed') {
            $this->stockService->decreaseFromOrder($order);
        }

        $statusTimeColumn = "{$newStatus}_at";
        $order->update([
            'status' => $newStatus,
            $statusTimeColumn => Carbon::now()
        ]);

        return $order;
    }

    private function save(array $requestData, array $summary): Order
    {
        $orderData = [
            'user_id' => $requestData['user_id'],
            'order_type' => $requestData['order_type'],
            'status' => 'pending',
            'subtotal' => $summary['subtotal'],
            'discount' => $summary['discount'],
            'total' => $summary['total'],
            'order_date' => Carbon::now(),
        ];

        if (isset($requestData['delivery_id'])) {
            $orderData['delivery_id'] = $requestData['delivery_id'];
        }
        if (isset($requestData['table_id'])) {
            $orderData['table_id'] = $requestData['table_id'];
        }
        if (isset($requestData['notes'])) {
            $orderData['notes'] = $requestData['notes'];
        }

        return DB::transaction(function () use ($orderData, $summary) {
            $order = Order::create($orderData);
            $this->saveItems($order, $summary['items']);

            if ($summary['coupon']) {
                $summary['coupon']->increment('used_count');
            }

            return $order;
        });
    }

    private function saveItems(Order $order, array $itemsData): void
    {
        foreach ($itemsData as $itemData) {
            $orderItem = $order->items()->create($itemData);

            if (!empty($itemData['additionals'])) {
                $orderItem->additionals()->createMany($itemData['additionals']);
            }
        }
    }
}
