#!/bin/bash
set -e

REPO_DIR="/home/sites/25b/c/c4cef84f1f/repo_git"
HOME_DIR="/home/sites/25b/c/c4cef84f1f"

copy_tree_force() {
    local src="$1"
    local dst="$2"
    [ -d "$src" ] || return 0
    for item in "$src"/*; do
        [ -e "$item" ] || continue
        cp -rf "$item" "$dst/"
    done
}

copy_tree_noclobber() {
    local src="$1"
    local dst="$2"
    [ -d "$src" ] || return 0
    for item in "$src"/*; do
        [ -e "$item" ] || continue
        cp -rn "$item" "$dst/"
    done
}

copy_tree_update() {
    local src="$1"
    local dst="$2"
    [ -d "$src" ] || return 0
    for item in "$src"/*; do
        [ -e "$item" ] || continue
        cp -ru "$item" "$dst/"
    done
}

echo "=== Shrawan Effects Full Performance Deployment Started ==="

# 1. Sync App, Routes, Resources, Database
echo "Syncing app, routes, resources to $HOME_DIR..."
cp -rf "$REPO_DIR/app" "$HOME_DIR/"
cp -rf "$REPO_DIR/routes" "$HOME_DIR/"
cp -rf "$REPO_DIR/resources" "$HOME_DIR/"
mkdir -p "$HOME_DIR/database"
if [ -f "$REPO_DIR/database/deploy_updates.php" ]; then
    cp -f "$REPO_DIR/database/deploy_updates.php" "$HOME_DIR/database/"
fi

# 2. Remove stale Next.js static export paths & Sync Public Assets (CSS, JS, .htaccess, robots.txt)
echo "Removing stale static export paths from public_html and public..."
for pub_dir in "$HOME_DIR/public_html" "$HOME_DIR/public"; do
    [ -d "$pub_dir" ] || continue
    for stale_target in _next blog category tag contact faq 404 404.html index.html index.txt; do
        target_path="$pub_dir/$stale_target"
        if [ -e "$target_path" ] || [ -L "$target_path" ]; then
            rm -rf -- "$target_path"
        fi
    done
done

echo "Syncing public assets to public_html and public..."
mkdir -p "$HOME_DIR/public_html/css" "$HOME_DIR/public/css" "$HOME_DIR/public_html/js" "$HOME_DIR/public/js"
copy_tree_force "$REPO_DIR/public/css" "$HOME_DIR/public_html/css"
copy_tree_force "$REPO_DIR/public/css" "$HOME_DIR/public/css"
copy_tree_force "$REPO_DIR/public/js" "$HOME_DIR/public_html/js"
copy_tree_force "$REPO_DIR/public/js" "$HOME_DIR/public/js"

if [ -f "$REPO_DIR/public/.htaccess" ]; then
    cp -f "$REPO_DIR/public/.htaccess" "$HOME_DIR/public_html/.htaccess"
    cp -f "$REPO_DIR/public/.htaccess" "$HOME_DIR/public/.htaccess"
fi
if [ -f "$REPO_DIR/public/robots.txt" ]; then
    cp -f "$REPO_DIR/public/robots.txt" "$HOME_DIR/public_html/robots.txt"
    cp -f "$REPO_DIR/public/robots.txt" "$HOME_DIR/public/robots.txt"
fi

# 3. Sync Storage Assets & Cache .htaccess (Real directories only — never symlinks on StackCP Apache)
echo "Ensuring real storage directories and syncing assets..."
if [ -L "$HOME_DIR/public_html/storage" ]; then
    echo "Removing symlink at $HOME_DIR/public_html/storage..."
    rm -f "$HOME_DIR/public_html/storage"
fi
if [ -L "$HOME_DIR/public/storage" ]; then
    echo "Removing symlink at $HOME_DIR/public/storage..."
    rm -f "$HOME_DIR/public/storage"
fi

mkdir -p "$HOME_DIR/storage/app/public" "$HOME_DIR/public_html/storage" "$HOME_DIR/public/storage"

# Sync existing live uploaded media from HOME_DIR/storage/app/public without clobbering newer files in public_html/storage
copy_tree_update "$HOME_DIR/storage/app/public" "$HOME_DIR/public_html/storage"
copy_tree_update "$HOME_DIR/storage/app/public" "$HOME_DIR/public/storage"

# Copy repo static storage assets without overwriting existing live uploaded media
copy_tree_noclobber "$REPO_DIR/storage/app/public" "$HOME_DIR/storage/app/public"
copy_tree_noclobber "$REPO_DIR/storage/app/public" "$HOME_DIR/public_html/storage"
copy_tree_noclobber "$REPO_DIR/storage/app/public" "$HOME_DIR/public/storage"

copy_tree_noclobber "$REPO_DIR/public/storage" "$HOME_DIR/public_html/storage"
copy_tree_noclobber "$REPO_DIR/public/storage" "$HOME_DIR/public/storage"

# Always deploy the clean RewriteEngine-free storage .htaccess
if [ -f "$REPO_DIR/public/storage/.htaccess" ]; then
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/storage/app/public/.htaccess"
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/public_html/storage/.htaccess"
    cp -f "$REPO_DIR/public/storage/.htaccess" "$HOME_DIR/public/storage/.htaccess"
fi

# 4. Enforce web-readable permissions (755 directories, 644 files) strictly on Shrawan Effects asset paths
#    (NEVER touch sibling sites such as public_html/shyamo)
echo "Enforcing 755/644 permissions on storage, css, and js directories..."
for target_dir in \
    "$HOME_DIR/public_html/storage" \
    "$HOME_DIR/public_html/css" \
    "$HOME_DIR/public_html/js" \
    "$HOME_DIR/public/storage" \
    "$HOME_DIR/public/css" \
    "$HOME_DIR/public/js" \
    "$HOME_DIR/storage/app/public"
do
    if [ -d "$target_dir" ]; then
        find "$target_dir" -type d -exec chmod 755 {} \;
        find "$target_dir" -type f -exec chmod 644 {} \;
    fi
done

for target_file in \
    "$HOME_DIR/public_html/.htaccess" \
    "$HOME_DIR/public/.htaccess" \
    "$HOME_DIR/public_html/robots.txt" \
    "$HOME_DIR/public/robots.txt"
do
    if [ -f "$target_file" ]; then
        chmod 644 "$target_file"
    fi
done

cd "$HOME_DIR"

# 5. Clean up database canonical URLs, internal links, and semantic headings
if [ -f "database/deploy_updates.php" ]; then
    echo "Running database updates..."
    php database/deploy_updates.php || true
fi

# 6. Clear compiled Blade views & Laravel caches
echo "Clearing compiled views and Laravel caches..."
rm -f "$HOME_DIR/storage/framework/views/"*.php 2>/dev/null || true
if [ -f "artisan" ]; then
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan config:clear || true
fi

echo "=== Shrawan Effects Deployment Completed Successfully ==="
