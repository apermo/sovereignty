#!/usr/bin/env bash

# Import a database dump instead of running wp core install.

if [ -z "${WP_DB_IMPORT:-}" ]; then
    return 0
fi

DB_FILE="/var/www/html/${WP_DB_IMPORT}"

if [ ! -f "$DB_FILE" ]; then
    echo "Database file not found: ${WP_DB_IMPORT}"
    echo "Falling back to fresh install."
    return 0
fi

echo "Importing database from ${WP_DB_IMPORT}..."
if [[ "$DB_FILE" == *.gz ]]; then
    gunzip -c "$DB_FILE" | wp db import - --path="${WP_PATH}"
else
    wp db import "$DB_FILE" --path="${WP_PATH}"
fi

# Search-replace old URL with DDEV site URL
SITE_URL="${DDEV_PRIMARY_URL:-https://${DDEV_HOSTNAME}}"
OLD_URL=$(wp option get siteurl --path="${WP_PATH}" 2>/dev/null || true)

if [ -n "$OLD_URL" ] && [ "$OLD_URL" != "$SITE_URL" ]; then
    echo "Replacing ${OLD_URL} → ${SITE_URL}..."
    wp search-replace "$OLD_URL" "$SITE_URL" --all-tables --path="${WP_PATH}"
fi

export WP_DB_IMPORTED=1
echo "Database imported successfully."
