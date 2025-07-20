<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Ingredient;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function decreaseFromOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                foreach ($item->product->ingredients as $productIngredient) {
                    $quantity = $productIngredient->pivot->quantity * $item->quantity;
                    $this->decreaseIngredient($productIngredient, $quantity);
                }
                foreach ($item->additionals as $additional) {
                    $this->decreaseIngredient($additional->ingredient, $additional->quantity);
                }
            }
        });
    }

    private function decreaseIngredient(Ingredient $ingredient, float $quantity): void
    {
        if ($ingredient->quantity < $quantity) {
            throw new ApiException(__('api.insufficient_stock', [
                'name' => $ingredient->name,
                'available' => $ingredient->quantity,
            ]));
        }
        $ingredient->decrement('quantity', $quantity);
    }
}
