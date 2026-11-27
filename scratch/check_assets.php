<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SETTINGS ===\n";
$settings = \App\Models\Setting::whereIn('key', ['site_name', 'site_logo', 'logo', 'site_favicon', 'favicon'])->get();
foreach ($settings as $s) {
    echo "{$s->key} => {$s->value}\n";
}

echo "\nLogo URL: " . \App\Models\Setting::getLogoUrl() . "\n";
echo "Favicon URL: " . \App\Models\Setting::getFaviconUrl() . "\n";

echo "\n=== POSTS (First 5) ===\n";
$posts = \App\Models\Post::select('id', 'title', 'featured_image')->take(5)->get();
foreach ($posts as $p) {
    $exists = $p->featured_image && file_exists(public_path('storage/' . $p->featured_image));
    echo "Post #{$p->id}: image={$p->featured_image} (exists=" . ($exists ? 'YES' : 'NO') . ")\n";
}
