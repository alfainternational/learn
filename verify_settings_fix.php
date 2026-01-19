<?php
// Test settings update after fix
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrap the application
$request = Illuminate\Http\Request::create('/');
$kernel->handle($request);

// First, let's check the table structure
echo "=== Checking Table Structure ===\n";
$columns = \Illuminate\Support\Facades\DB::select("DESCRIBE system_settings");
foreach ($columns as $column) {
    echo "{$column->Field} ({$column->Type})\n";
}

echo "\n=== Testing Insert Operation ===\n";
try {
    \Illuminate\Support\Facades\DB::table('system_settings')->insert([
        'setting_key' => 'test_key',
        'setting_value' => 'test_value',
        'setting_type' => 'string',
        'description' => 'Test setting',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ Insert successful\n";
    
    // Clean up
    \Illuminate\Support\Facades\DB::table('system_settings')->where('setting_key', 'test_key')->delete();
    echo "✓ Cleanup successful\n";
} catch (Exception $e) {
    echo "✗ Insert failed: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Gemini API Key Save ===\n";
try {
    \Illuminate\Support\Facades\DB::table('system_settings')->updateOrInsert(
        ['setting_key' => 'gemini_api_key'],
        [
            'setting_value' => 'test_gemini_key_12345',
            'setting_type' => 'string',
            'description' => 'Gemini API Key for AI features',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    echo "✓ Gemini API key save successful\n";
    
    $saved = \Illuminate\Support\Facades\DB::table('system_settings')->where('setting_key', 'gemini_api_key')->first();
    echo "Saved value: {$saved->setting_value}\n";
} catch (Exception $e) {
    echo "✗ Save failed: " . $e->getMessage() . "\n";
}

echo "\n=== All Settings ===\n";
$settings = \Illuminate\Support\Facades\DB::table('system_settings')->get();
foreach ($settings as $setting) {
    echo "- {$setting->setting_key}: {$setting->setting_value}\n";
}
