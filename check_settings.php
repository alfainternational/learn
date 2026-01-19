<?php
// Check system settings
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/');
$kernel->handle($request);

$settings = DB::table('system_settings')->get();

echo "System Settings:\n";
echo "================\n";
foreach ($settings as $setting) {
    echo "Key: {$setting->setting_key}\n";
    echo "Value: {$setting->setting_value}\n";
    echo "Type: {$setting->setting_type}\n";
    echo "Description: {$setting->description}\n";
    echo "---\n";
}

echo "\nTotal settings: " . count($settings) . "\n";
