#!/bin/bash
set -e

REPO_DIR="/home/sites/25b/c/c4cef84f1f/repo_git"
HOME_DIR="/home/sites/25b/c/c4cef84f1f"

echo "=== Shrawan Effects Top-Level Post Clean URLs Deployment Started ==="
echo "Syncing app, routes, resources to $HOME_DIR..."

cp -rf "$REPO_DIR/app" "$HOME_DIR/"
cp -rf "$REPO_DIR/routes" "$HOME_DIR/"
cp -rf "$REPO_DIR/resources" "$HOME_DIR/"

cd "$HOME_DIR"

# Clean up any post_seo database canonical URLs containing /blog/
if [ -f "bootstrap/app.php" ]; then
    echo "Updating canonical_url in database..."
    php -r '
    require __DIR__ . "/vendor/autoload.php";
    $app = require_once __DIR__ . "/bootstrap/app.php";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    try {
        $count = Illuminate\Support\Facades\DB::table("post_seo")
            ->where("canonical_url", "LIKE", "%/blog/%")
            ->update([
                "canonical_url" => Illuminate\Support\Facades\DB::raw("REPLACE(canonical_url, \"/blog/\", \"/\")")
            ]);
        echo "Updated $count canonical URLs in post_seo.\n";
    } catch (\Throwable $e) {
        echo "Note: " . $e->getMessage() . "\n";
    }
    ' || true
fi

# Clear Laravel route and view caches
if [ -f "artisan" ]; then
    echo "Clearing Laravel caches..."
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "=== Shrawan Effects Deployment Completed Successfully ==="
