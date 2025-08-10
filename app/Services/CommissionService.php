<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Order;
use App\Models\User;

class CommissionService
{
    /**
     * Calculate and create commissions for an order.
     *
     * @param  Order  $order
     * @return void
     */
    public function calculateCommissions(Order $order): void
    {
        $buyer = $order->user;
        
        // Skip if buyer has no upline
        if (!$buyer->upline) {
            return;
        }

        // Get the upline chain (max 10 levels)
        $uplineChain = $buyer->getUplineChain(10);
        
        foreach ($uplineChain as $uplineData) {
            $reseller = $uplineData['user'];
            $level = $uplineData['level'];
            
            // Skip if reseller doesn't have a reseller level
            if (!$reseller->resellerLevel) {
                continue;
            }

            // Calculate commission based on reseller's level
            $commissionPercentage = $this->getCommissionPercentageForLevel($reseller->resellerLevel->level, $level);
            
            if ($commissionPercentage > 0) {
                $commissionAmount = ($order->subtotal * $commissionPercentage) / 100;
                
                Commission::create([
                    'order_id' => $order->id,
                    'reseller_id' => $reseller->id,
                    'from_user_id' => $buyer->id,
                    'level' => $level,
                    'order_amount' => $order->subtotal,
                    'commission_percentage' => $commissionPercentage,
                    'commission_amount' => $commissionAmount,
                    'type' => $reseller->is_affiliate ? 'affiliate' : 'reseller',
                    'status' => 'pending',
                ]);

                // Update reseller's total commission
                $reseller->increment('total_commission_earned', $commissionAmount);
            }
        }
    }

    /**
     * Get commission percentage based on reseller level and upline level.
     *
     * @param  int  $resellerLevel
     * @param  int  $uplineLevel
     * @return float
     */
    protected function getCommissionPercentageForLevel(int $resellerLevel, int $uplineLevel): float
    {
        // Commission decreases with distance (level)
        $baseCommission = match($resellerLevel) {
            1 => 2.0,  // Bronze
            2 => 3.0,  // Silver
            3 => 4.0,  // Gold
            4 => 5.0,  // Platinum
            5 => 6.0,  // Diamond
            6 => 7.0,  // Master
            7 => 8.0,  // Grand Master
            8 => 9.0,  // Elite
            9 => 10.0, // Supreme
            10 => 12.0, // Legendary
            default => 1.0,
        };

        // Reduce commission by level distance
        $reduction = ($uplineLevel - 1) * 0.5;
        $finalCommission = max(0.1, $baseCommission - $reduction);

        return round($finalCommission, 2);
    }

    /**
     * Process commission payments.
     *
     * @param  array  $commissionIds
     * @return int
     */
    public function processCommissionPayments(array $commissionIds): int
    {
        $commissions = Commission::whereIn('id', $commissionIds)
            ->where('status', 'pending')
            ->get();

        $processedCount = 0;
        
        foreach ($commissions as $commission) {
            $commission->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            $processedCount++;
        }

        return $processedCount;
    }

    /**
     * Get commission statistics for a reseller.
     *
     * @param  User  $reseller
     * @return array
     */
    public function getResellerCommissionStats(User $reseller): array
    {
        $totalEarned = $reseller->commissions()->where('status', 'paid')->sum('commission_amount');
        $pendingCommissions = $reseller->commissions()->where('status', 'pending')->sum('commission_amount');
        $thisMonthEarned = $reseller->commissions()
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('commission_amount');

        return [
            'total_earned' => $totalEarned,
            'pending_commissions' => $pendingCommissions,
            'this_month_earned' => $thisMonthEarned,
            'commission_count' => $reseller->commissions()->count(),
        ];
    }
}