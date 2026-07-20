<?php

namespace App\Services;

use App\Models\Order;

class CustomerOrderSession
{
    private const KEY = 'customer_order_ids';
    private const TTL_DAYS = 7;

    public function remember(Order $order): void
    {
        $entries = $this->entries();
        $entries[$order->id] = now()->timestamp;

        session([self::KEY => $entries]);
    }

    public function ids(): array
    {
        return array_keys($this->entries());
    }

    public function contains(Order $order): bool
    {
        return in_array($order->id, $this->ids(), true);
    }

    private function entries(): array
    {
        $expiresAt = now()->subDays(self::TTL_DAYS)->timestamp;

        $entries = collect(session(self::KEY, []))
            ->mapWithKeys(function ($storedAt, $orderId) {
                if (is_array($storedAt)) {
                    return [(int) ($storedAt['id'] ?? $orderId) => (int) ($storedAt['stored_at'] ?? now()->timestamp)];
                }

                return [(int) $orderId => (int) $storedAt];
            })
            ->filter(function ($storedAt, $orderId) use ($expiresAt) {
                return $orderId > 0 && $storedAt >= $expiresAt;
            })
            ->sortDesc()
            ->all();

        session([self::KEY => $entries]);

        return $entries;
    }
}
