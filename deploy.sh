#!/bin/bash
set -e

REPO_DIR="/home/sites/25b/c/c4cef84f1f/repo_git"
HOME_DIR="/home/sites/25b/c/c4cef84f1f"

echo "=== Shrawan Effects Full Performance Deployment Started ==="

# 1. Sync App, Routes, Resources
echo "Syncing app, routes, resources to $HOME_DIR..."
cp -rf "$REPO_DIR/app" "$HOME_DIR/"
cp -rf "$REPO_DIR/routes" "$HOME_DIR/"
cp -rf "$REPO_DIR/resources" "$HOME_DIR/"

# 2. Sync Public Assets (CSS, .htaccess)
echo "Syncing public assets to public_html and public..."
mkdir -p "$HOME_DIR/public_html/css" "$HOME_DIR/public/css"
cp -rf "$REPO_DIR/public/css/"* "$HOME_DIR/public_html/css/" 2>/dev/null || true
cp -rf "$REPO_DIR/public/css/"* "$HOME_DIR/public/css/" 2>/dev/null || true

cp -f "$REPO_DIR/public/.htaccess" "$HOME_DIR/public_html/.htaccess" 2>/dev/null || true
cp -f "$REPO_DIR/public/.htaccess" "$HOME_DIR/public/.htaccess" 2>/dev/null || true
cp -f "$REPO_DIR/public/robots.txt" "$HOME_DIR/public_html/robots.txt" 2>/dev/null || true
cp -f "$REPO_DIR/public/robots.txt" "$HOME_DIR/public/robots.txt" 2>/dev/null || true

# 3. Sync Storage Assets & WebP Images
echo "Syncing storage images and cache .htaccess..."
mkdir -p "$HOME_DIR/storage/app/public" "$HOME_DIR/public_html/storage" "$HOME_DIR/public/storage"

if [ -d "$REPO_DIR/storage/app/public" ]; then
    cp -rf "$REPO_DIR/storage/app/public/"* "$HOME_DIR/storage/app/public/" 2>/dev/null || true
    cp -rf "$REPO_DIR/storage/app/public/"* "$HOME_DIR/public_html/storage/" 2>/dev/null || true
    cp -rf "$REPO_DIR/storage/app/public/"* "$HOME_DIR/public/storage/" 2>/dev/null || true
fi

if [ -f "$REPO_DIR/public/storage/.htaccess" ]; then
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/storage/app/public/.htaccess" 2>/dev/null || true
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/public_html/storage/.htaccess" 2>/dev/null || true
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/public/storage/.htaccess" 2>/dev/null || true
fi

cd "$HOME_DIR"

# 4. Ensure storage symlink is active
if [ -f "artisan" ]; then
    php artisan storage:link || true
fi

# 5. Clean up any post_seo database canonical URLs and post content internal links containing /blog/
if [ -f "bootstrap/app.php" ]; then
    echo "Updating canonical_url and internal links in database..."
    php -r '
    require __DIR__ . "/vendor/autoload.php";
    $app = require_once __DIR__ . "/bootstrap/app.php";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    try {
        $countSeo = Illuminate\Support\Facades\DB::table("post_seo")
            ->where("canonical_url", "LIKE", "%/blog/%")
            ->update([
                "canonical_url" => Illuminate\Support\Facades\DB::raw("REPLACE(canonical_url, \"/blog/\", \"/\")")
            ]);
        $countContent = Illuminate\Support\Facades\DB::table("posts")
            ->where("content", "LIKE", "%/blog/%")
            ->update([
                "content" => Illuminate\Support\Facades\DB::raw("REPLACE(REPLACE(content, \"https://shrawaneffects.com/blog/\", \"https://shrawaneffects.com/\"), \"href=\\\"/blog/\", \"href=\\\"/\")")
            ]);
        echo "Updated $countSeo canonical URLs and $countContent post content internal links in database.\n";

        // Promote h4 to h3, and h3 to h2 for vivo-x500-series
        $vivoPost = Illuminate\Support\Facades\DB::table("posts")->where("slug", "vivo-x500-series")->first();
        if ($vivoPost) {
            $updated = preg_replace('/<h3(\s+[^>]*)?>/i', '<h2$1>', $vivoPost->content);
            $updated = preg_replace('/<\/h3>/i', '</h2>', $updated);
            $updated = preg_replace('/<h4(\s+[^>]*)?>/i', '<h3$1>', $updated);
            $updated = preg_replace('/<\/h4>/i', '</h3>', $updated);
            Illuminate\Support\Facades\DB::table("posts")->where("id", $vivoPost->id)->update(["content" => $updated]);
            echo "Promoted vivo-x500-series headings to semantic H2 and H3.\n";
        }
    } catch (\Throwable $e) {
        echo "Note: " . $e->getMessage() . "\n";
    }
    ' || true
fi

# 6. Clear & warm Laravel caches
if [ -f "artisan" ]; then
    echo "Optimizing Laravel caches..."
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan config:clear || true
fi

echo "=== Shrawan Effects Deployment Completed Successfully ==="
