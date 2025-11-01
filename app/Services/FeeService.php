<?php

namespace App\Services;

use App\Models\TransactionFee;

class FeeService
{
    /**
     * Get transaction fee with fallback logic
     * Both type and network are compulsory
     */
    public function getFee(string $type, string $network, float $amount = null): float
    {
        // Validate inputs
        if (empty($type) || empty($network)) {
            return 10.00;
        }

        $cacheKey = "fee_{$type}_{$network}";

        return cache()->remember($cacheKey, 3600, function() use ($type, $network, $amount) {
            $query = TransactionFee::where('type', $type)
                ->where('status', 'active');

            // Priority 1: Exact match (type + network)
            $exactFee = $query->where('network', $network)->first();
            if ($exactFee) {
                return $this->calculateFee($exactFee, $amount);
            }

            // Final fallback
            return 0.00;
        });
    }

    /**
     * Get fee details including breakdown
     */
    public function getFeeDetails(string $type, string $network, float $amount = null): array
    {
        $fee = $this->getFee($type, $network, $amount);

        return [
            'type' => $type,
            'network' => $network,
            'fee_amount' => $fee,
            'amount' => $amount,
            'total' => $amount ? $amount + $fee : $fee,
        ];
    }

    /**
     * Calculate fee based on fee configuration
     */
    private function calculateFee($feeConfig, $amount = null): float
    {
        // If you implement percentage fees in the future
        if (isset($feeConfig->is_percentage) && $feeConfig->is_percentage && $amount) {
            return $amount * ($feeConfig->amount / 100);
        }

        // Fixed fee
        return (float) $feeConfig->amount;
    }

    /**
     * Check if a specific fee exists
     */
    public function feeExists(string $type, string $network): bool
    {
        return TransactionFee::where('type', $type)
            ->where('network', $network)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get all available fees for a specific type
     */
    public function getFeesByType(string $type): array
    {
        return TransactionFee::where('type', $type)
            ->where('status', 'active')
            ->get()
            ->toArray();
    }

    /**
     * Get all available fees for a specific network
     */
    public function getFeesByNetwork(string $network): array
    {
        return TransactionFee::where('network', $network)
            ->where('status', 'active')
            ->get()
            ->toArray();
    }
}
