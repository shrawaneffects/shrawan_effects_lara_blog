<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Delete test menu items
$deleted = \App\Models\MenuItem::where('title', 'LIKE', '%Special Showcase%')
    ->orWhere('title', 'LIKE', '%Secret Menu%')
    ->orWhere('title', 'LIKE', '%Hidden Showcase%')
    ->orWhere('title', 'LIKE', '%Temporary Link%')
    ->orWhere('url', 'LIKE', '%/showcase%')
    ->orWhere('url', 'LIKE', '%/secret-%')
    ->delete();

echo "Deleted {$deleted} junk/test menu items.\n";

$remaining = \App\Models\MenuItem::all();
echo "Remaining clean menu items: " . $remaining->count() . "\n";
foreach ($remaining as $it) {
    echo "ID: {$it->id} | [{$it->location}] {$it->title} -> {$it->url} (active: {$it->is_active})\n";
}
