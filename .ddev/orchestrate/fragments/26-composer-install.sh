#!/usr/bin/env bash

# Run composer install if a composer.json exists in the project root.

if [ ! -f "${DOCROOT}/composer.json" ]; then
    return 0
fi

if [ -d "${DOCROOT}/vendor" ]; then
    echo "Vendor directory already exists, skipping composer install."
    return 0
fi

echo "Running composer install..."
composer install --working-dir="${DOCROOT}" --no-interaction --quiet
echo "Composer dependencies installed."