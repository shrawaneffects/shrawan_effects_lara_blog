#!/bin/bash
set -e

REPO_DIR="/home/sites/25b/c/c4cef84f1f/repo_git"
HOME_DIR="/home/sites/25b/c/c4cef84f1f"

echo "=== Shrawan Effects Full Performance Deployment Started ==="

# 1. Sync App, Routes, Resources, Database
echo "Syncing app, routes, resources to $HOME_DIR..."
cp -rf "$REPO_DIR/app" "$HOME_DIR/"
cp -rf "$REPO_DIR/routes" "$HOME_DIR/"
cp -rf "$REPO_DIR/resources" "$HOME_DIR/"
mkdir -p "$HOME_DIR/database"
cp -rf "$REPO_DIR/database/deploy_updates.php" "$HOME_DIR/database/" 2>/dev/null || true

# 2. Sync Public Assets (CSS, .htaccess, robots.txt)
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

# 5. Clean up database canonical URLs, internal links, and semantic headings
if [ -f "database/deploy_updates.php" ]; then
    echo "Running database updates..."
    php database/deploy_updates.php || true
fi

# 6. Clear & warm Laravel caches
if [ -f "artisan" ]; then
    echo "Optimizing Laravel caches..."
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan config:clear || true
fi

echo "=== Shrawan Effects Deployment Completed Successfully ==="
