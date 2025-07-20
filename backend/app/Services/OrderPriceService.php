<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\Product;
use Carbon\Carbon;

class OrderPriceService
{
    public function calculate(array $requestData): array
    {
        $itemsData = $this->calculateItemsPrice($requestData['items']);

        $couponData = $this->getCouponDiscount($requestData, $itemsData['subtotal']);
        $deliveryCost = 0;

        $total = $itemsData['subtotal'] - $couponData['discount'] + $deliveryCost;

        return [
            'subtotal' => $itemsData['subtotal'],
            'discount' => $couponData['discount'],
            'total' => $total,
            'items' => $itemsData['items'],
            'coupon' => $couponData['coupon'],
        ];
    }

    private function calculateItemsPrice(array $itemsInput): array
    {
        $subtotal = 0;
        $processedItems = [];

        foreach ($itemsInput as $itemInput) {
            $product = Product::find($itemInput['product_id']);
            if (!$product || !$product->status) {
                throw new ApiException(__('api.product_unavailable', ['name' => $product?->name ?? 'ID ' . $itemInput['product_id']]));
            }

            $productPrice = $product->price;
            if ($product->promotional_price) {
                $productPrice = $product->promotional_price;
            }

            $additionalsData = $this->calculateAdditionalsPrice($itemInput);
            $itemTotal = ($productPrice * $itemInput['quantity']) + $additionalsData['total'];
            $subtotal += $itemTotal;

            $processedItems[] = [
                'product_id' => $product->id,
                'quantity' => $itemInput['quantity'],
                'unit_price' => $productPrice,
                'total_price' => $itemTotal,
                'notes' => $itemInput['notes'] ?? null,
                'additionals' => $additionalsData['additionals'],
            ];
        }

        return ['subtotal' => $subtotal, 'items' => $processedItems];
    }

    private function calculateAdditionalsPrice(array $itemInput): array
    {
        $total = 0;
        $processedAdditionals = [];

        if (!isset($itemInput['additionals'])) {
            return ['total' => $total, 'additionals' => $processedAdditionals];
        }

        foreach ($itemInput['additionals'] as $additionalInput) {
            $ingredient = Ingredient::find($additionalInput['ingredient_id']);

            if (!$ingredient || !$ingredient->is_additional || !$ingredient->status) {
                throw new ApiException(__('api.ingredient_unavailable', ['name' => $ingredient?->name]));
            }

            $additionalPrice = $ingredient->unit_price * $additionalInput['quantity'];
            $total += $additionalPrice;

            $processedAdditionals[] = [
                'ingredient_id' => $ingredient->id,
                'quantity' => $additionalInput['quantity'],
                'unit_price' => $ingredient->unit_price,
                'total_price' => $additionalPrice,
            ];
        }

        return ['total' => $total, 'additionals' => $processedAdditionals];
    }

    private function getCouponDiscount(array $requestData, float $subtotal): array
    {
        $defaultResponse = ['discount' => 0, 'coupon' => null];

        if (!isset($requestData['coupon_code'])) {
            return $defaultResponse;
        }

        $coupon = Coupon::where('code', $requestData['coupon_code'])
            ->where('status', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->first();

        if (!$coupon) {
            return $defaultResponse;
        }

        if ($subtotal < $coupon->min_order_value) {
            return $defaultResponse;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return $defaultResponse;
        }

        if ($coupon->discount_type === 'fixed') {
            return ['discount' => $coupon->discount_value, 'coupon' => $coupon];
        }

        $discountValue = $subtotal * ($coupon->discount_value / 100);
        return ['discount' => $discountValue, 'coupon' => $coupon];
    }
}
