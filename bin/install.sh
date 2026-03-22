#!/usr/bin/env bash
#
# Install script for Sovereignty theme development.
#
# Usage:
#   ./bin/install.sh         # Production: install, build, clean node_modules
#   ./bin/install.sh --dev   # Development: install, build, keep node_modules
#

set -euo pipefail

DEV_MODE=0
if [[ "${1:-}" == "--dev" ]]; then
    DEV_MODE=1
fi

echo "=== Sovereignty Install ==="

# Step 1: PHP dependencies.
if command -v composer &> /dev/null; then
    echo "Installing Composer dependencies..."
    if [[ "$DEV_MODE" -eq 1 ]]; then
        composer install --no-interaction
    else
        composer install --no-dev --no-interaction --optimize-autoloader
    fi
else
    echo "Error: composer not found. Install it from https://getcomposer.org/"
    exit 1
fi

# Step 2: Node dependencies.
if command -v npm &> /dev/null; then
    echo "Installing Node dependencies..."
    npm ci
else
    echo "Error: npm not found. Install Node.js from https://nodejs.org/"
    exit 1
fi

# Step 3: Build CSS.
echo "Building CSS..."
npm run build

# Step 4: Generate version.php.
echo "Generating version.php..."
composer generate-version

# Step 5: Clean up for production.
if [[ "$DEV_MODE" -eq 0 ]]; then
    echo "Cleaning up node_modules for production..."
    rm -rf node_modules
fi

echo ""
echo "=== Install complete ==="
if [[ "$DEV_MODE" -eq 1 ]]; then
    echo "Development mode: node_modules kept."
    echo "Run 'npm run watch' to start SASS watcher."
else
    echo "Production mode: ready for deployment."
fi
