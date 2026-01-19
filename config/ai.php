<?php

return [
    'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
    'api_key' => env('GEMINI_API_KEY'),
    'max_tokens' => 2000,
    'temperature' => 0.7,
    'cache_ttl' => 3600, // 1 hour
    'rate_limit' => [
        'free' => 3, // per month
        'basic' => -1, // unlimited
        'pro' => -1,
        'enterprise' => -1,
    ],
];
