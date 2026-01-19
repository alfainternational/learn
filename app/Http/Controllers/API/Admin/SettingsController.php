<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Get all system settings
     */
    public function index()
    {
        $settings = DB::table('system_settings')->get();

        $formatted = [];
        foreach ($settings as $setting) {
            $formatted[$setting->setting_key] = [
                'value' => $this->castValue($setting->setting_value, $setting->setting_type),
                'type' => $setting->setting_type,
                'description' => $setting->description
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    /**
     * Update a setting
     */
    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        $setting = DB::table('system_settings')
            ->where('setting_key', $request->key)
            ->first();

        if (!$setting) {
            // Create new setting
            DB::table('system_settings')->insert([
                'setting_key' => $request->key,
                'setting_value' => $request->value,
                'setting_type' => $this->detectType($request->value),
                'description' => $request->description ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            // Update existing
            DB::table('system_settings')
                ->where('setting_key', $request->key)
                ->update([
                    'setting_value' => $request->value,
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعداد بنجاح'
        ]);
    }

    /**
     * Test Gemini API key
     */
    public function testGeminiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string'
        ]);

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$request->api_key}";

            $response = \Illuminate\Support\Facades\Http::timeout(10)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Hello, respond with OK if you receive this.']
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'المفتاح صالح ويعمل بشكل صحيح'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'المفتاح غير صالح أو منتهي الصلاحية'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بخدمة Gemini: ' . $e->getMessage()
            ], 500);
        }
    }

    private function castValue($value, $type)
    {
        return match($type) {
            'number' => (int) $value,
            'boolean' => $value === 'true' || $value === '1',
            'json' => json_decode($value, true),
            default => $value
        };
    }

    private function detectType($value): string
    {
        if (is_bool($value)) return 'boolean';
        if (is_numeric($value)) return 'number';
        if (is_array($value)) return 'json';
        return 'string';
    }
}
