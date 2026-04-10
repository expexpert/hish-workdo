<?php

return [
    'defaults' => [
        'daily_request_limit' => env('AI_DAILY_LIMIT', 30),
        'monthly_token_limit' => env('AI_MONTHLY_LIMIT', 25000),
        'anti_spam_seconds' => env('AI_ANTISPAM_SECONDS', 10),
    ],
    'bot_secret' => env('WHATSAPP_BOT_SECRET', 'super-secret'),
    'rates' => [
        'gpt-4o-mini' => [
            'input' => 0.00000015,  // $0.15 per 1M tokens
            'output' => 0.00000060, // $0.60 per 1M tokens
        ],
        'whisper-1' => [
            'cost_per_minute' => 0.006, // $0.006 per minute
        ]
    ]
];
