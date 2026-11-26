#!/bin/bash
set -e

REPO_DIR="/home/sites/25b/c/c4cef84f1f/repo_git"
TARGET_DIR="/home/sites/25b/c/c4cef84f1f/public_html"

echo "=== Shrawan Effects Deployment Started ==="
echo "Source: $REPO_DIR"
echo "Target: $TARGET_DIR"
echo "Time: $(date)"

mkdir -p "$TARGET_DIR"

# Copy repository files to target public_html
if command -v rsync >/dev/null 2>&1; then
    echo "Copying with rsync..."
    rsync -av --exclude='.git' --exclude='.env' "$REPO_DIR/" "$TARGET_DIR/"
else
    echo "Copying with cp..."
    cp -a "$REPO_DIR/." "$TARGET_DIR/"
fi

# Ensure root .htaccess and public/.htaccess are explicitly copied
if [ -f "$REPO_DIR/.htaccess" ]; then
    cp -f "$REPO_DIR/.htaccess" "$TARGET_DIR/.htaccess"
fi
if [ -f "$REPO_DIR/public/.htaccess" ]; then
    mkdir -p "$TARGET_DIR/public"
    cp -f "$REPO_DIR/public/.htaccess" "$TARGET_DIR/public/.htaccess"
fi

# Ensure Next.js index.html is in public
if [ -f "$REPO_DIR/public/index.html" ]; then
    cp -f "$REPO_DIR/public/index.html" "$TARGET_DIR/public/index.html"
fi

# Permissions
chmod 644 "$TARGET_DIR/.htaccess" 2>/dev/null || true
chmod 644 "$TARGET_DIR/public/.htaccess" 2>/dev/null || true
chmod -R 755 "$TARGET_DIR/public/_next" 2>/dev/null || true

# Clear Laravel caches if php is available
if command -v php >/dev/null 2>&1; then
    echo "Clearing Laravel optimization caches..."
    cd "$TARGET_DIR"
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
fi

echo "=== Shrawan Effects Deployment Completed Successfully ==="
