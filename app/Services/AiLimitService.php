<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\AiUserLimit;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AiLimitService
{
    /**
     * Check if a user is allowed to use AI (Cache-First)
     */
    public function checkCanUseAI($userId)
    {
        $now = now();
        $date = $now->format('Y-m-d');
        $monthYear = $now->format('m-Y');

        // 1. Check Anti-Spam (4s cooldown)
        if (Cache::has("ai_cooldown_{$userId}")) {
            return [
                'allowed' => false,
                'reason' => 'anti_spam',
                'message' => 'Please wait a few seconds before sending another message.'
            ];
        }

        // 2. Load Limits and Current Usage (Seed Cache if empty)
        $limits = $this->getUserLimits($userId);
        
        if ($limits->is_blocked) {
            return [
                'allowed' => false,
                'reason' => 'blocked',
                'message' => 'Your AI access has been temporarily blocked.'
            ];
        }

        // 3. Daily Request Check (Cache-first)
        $dailyKey = "ai_daily_{$userId}_{$date}";
        $dailyCount = Cache::remember($dailyKey, 86400, function () use ($userId, $date) {
            return AiUsageLog::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->count();
        });

        if ($dailyCount >= $limits->daily_request_limit) {
            return [
                'allowed' => false,
                'reason' => 'daily_limit',
                'message' => 'Daily AI limit reached. Try again tomorrow.'
            ];
        }

        // 4. Monthly Token Check (Cache-first)
        $monthlyKey = "ai_monthly_{$userId}_{$monthYear}";
        $monthlyTokens = Cache::remember($monthlyKey, 2592000, function () use ($userId, $monthYear) {
            return AiUsageLog::where('user_id', $userId)
                ->whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$monthYear])
                ->sum('total_tokens');
        });

        if ($monthlyTokens >= $limits->monthly_token_limit) {
            return [
                'allowed' => false,
                'reason' => 'monthly_limit',
                'message' => 'Monthly AI quota exceeded. Please upgrade your plan.'
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Record usage after successful AI call
     */
    public function recordUsage($userId, $model, $tokensIn, $tokensOut)
    {
        $now = now();
        $date = $now->format('Y-m-d');
        $monthYear = $now->format('m-Y');
        $total = $tokensIn + $tokensOut;

        // 1. Calculate Cost
        $rates = config('ai_limits.rates.' . $model, config('ai_limits.rates.gpt-4o-mini'));
        $cost = ($tokensIn * $rates['input']) + ($tokensOut * $rates['output']);

        // 2. Save to DB
        AiUsageLog::create([
            'user_id' => $userId,
            'model' => $model,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'total_tokens' => $total,
            'estimated_cost' => $cost,
        ]);

        // 3. Update Cache
        $dailyKey = "ai_daily_{$userId}_{$date}";
        $monthlyKey = "ai_monthly_{$userId}_{$monthYear}";
        
        if (Cache::has($dailyKey)) Cache::increment($dailyKey);
        if (Cache::has($monthlyKey)) Cache::increment($monthlyKey, $total);

        // 4. Set Anti-Spam (4s)
        $cooldown = config('ai_limits.defaults.anti_spam_seconds', 4);
        Cache::put("ai_cooldown_{$userId}", true, $cooldown);

        // 5. Update last_request_at (persistently)
        AiUserLimit::updateOrCreate(['user_id' => $userId], ['last_request_at' => $now]);
    }

    /**
     * Get user limits with caching
     */
    private function getUserLimits($userId)
    {
        return Cache::remember("ai_limits_{$userId}", 3600, function () use ($userId) {
            return AiUserLimit::firstOrCreate(
                ['user_id' => $userId],
                [
                    'daily_request_limit' => config('ai_limits.defaults.daily_request_limit'),
                    'monthly_token_limit' => config('ai_limits.defaults.monthly_token_limit'),
                ]
            );
        });
    }
}
