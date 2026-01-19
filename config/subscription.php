<?php

return [
    'tiers' => [
        'free' => [
            'name' => 'مجاني',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_plans' => 1,
            'ai_credits_monthly' => 3,
            'features' => [
                'خطة تسويقية واحدة',
                '3 استخدامات AI شهرياً',
                'قوالب أساسية',
                'تصدير PDF',
            ],
        ],
        'basic' => [
            'name' => 'أساسي',
            'price_monthly' => 99,
            'price_yearly' => 990,
            'max_plans' => 3,
            'ai_credits_monthly' => -1, // Unlimited
            'features' => [
                '3 خطط تسويقية',
                'استخدام AI غير محدود',
                'جميع القوالب',
                'تصدير PDF + Excel',
                'مشاركة الخطط',
            ],
        ],
        'pro' => [
            'name' => 'احترافي',
            'price_monthly' => 299,
            'price_yearly' => 2990,
            'max_plans' => 10,
            'ai_credits_monthly' => -1,
            'features' => [
                '10 خطط تسويقية',
                'استخدام AI غير محدود',
                'جميع القوالب المتقدمة',
                'تصدير PDF + Excel + Word',
                'تحليلات متقدمة',
                'دعم أولوية',
            ],
        ],
        'enterprise' => [
            'name' => 'مؤسسي',
            'price_monthly' => 999,
            'price_yearly' => 9990,
            'max_plans' => 999,
            'ai_credits_monthly' => -1,
            'features' => [
                'خطط غير محدودة',
                'استخدام AI غير محدود',
                'جميع الميزات',
                'API Access',
                'دعم مخصص',
                'تدريب فريق',
            ],
        ],
    ],

    'trial_days' => 7,
    'grace_period_days' => 3,
];
