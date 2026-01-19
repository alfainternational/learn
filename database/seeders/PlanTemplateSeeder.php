<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanTemplate;

class PlanTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'خطة إطلاق متجر إلكتروني',
                'description' => 'قالب شامل لتجهيز وإطلاق متجر إلكتروني جديد، يركز على الاستحواذ على العملاء وتحسين معدل التحويل.',
                'industry' => 'E-commerce',
                'category' => 'Launch',
                'is_premium' => false,
                'thumbnail_url' => null,
                'template_data' => [
                    'sections' => [
                        'diagnosis', 'target_audience', 'core_message', 'marketing_channels', 'content_plan', 'offer_stack', 'funnel', 'metrics'
                    ],
                    'default_content' => [
                        'marketing_goal' => 'تحقيق 1000 طلب في أول 3 أشهر',
                    ]
                ]
            ],
            [
                'name' => 'خطة تسويق تطبيق جوال',
                'description' => 'استراتيجية متكاملة لزيادة عدد تحميلات التطبيق وتفاعل المستخدمين.',
                'industry' => 'Technology',
                'category' => 'Growth',
                'is_premium' => true,
                'thumbnail_url' => null,
                'template_data' => [
                    'sections' => [
                        'target_audience', 'core_message', 'marketing_channels', 'app_store_optimization', 'content_plan', 'metrics'
                    ]
                ]
            ],
            [
                'name' => 'خطة تسويق مطعم / كوفي شوب',
                'description' => 'ركز على التسويق المحلي وجذب الزوار للموقع الجغرافي.',
                'industry' => 'Food & Beverage',
                'category' => 'Local Business',
                'is_premium' => false,
                'thumbnail_url' => null,
                'template_data' => [
                    'sections' => [
                        'diagnosis', 'target_audience', 'offer_stack', 'social_media', 'local_seo', 'events'
                    ]
                ]
            ],
            [
                'name' => 'خطة العلامة التجارية الشخصية',
                'description' => 'للمدربين والمستشارين والمؤثرين لبناء سلطة في مجالهم.',
                'industry' => 'Consulting',
                'category' => 'Personal Branding',
                'is_premium' => true,
                'thumbnail_url' => null,
                'template_data' => [
                    'sections' => [
                        'personal_story', 'target_audience', 'content_pillars', 'social_media', 'monetization'
                    ]
                ]
            ],
            [
                'name' => 'خطة نمو SaaS (B2B)',
                'description' => 'استراتيجية تسويق للشركات البرمجية التي تبيع لشركات أخرى.',
                'industry' => 'SaaS',
                'category' => 'B2B',
                'is_premium' => true,
                'thumbnail_url' => null,
                'template_data' => [
                    'sections' => [
                        'ideal_customer_profile', 'value_proposition', 'cold_outreach', 'content_marketing', 'linkedin_strategy', 'lead_nurturing'
                    ]
                ]
            ]
        ];

        foreach ($templates as $data) {
            PlanTemplate::create($data);
        }
    }
}
