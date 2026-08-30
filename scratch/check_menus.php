<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\MenuItem::all();
echo "Total menu items in DB: " . $items->count() . PHP_EOL;
foreach ($items as $it) {
    echo "ID: {$it->id} | [{$it->location}] {$it->title} -> {$it->url} (active: {$it->is_active})\n";
}
