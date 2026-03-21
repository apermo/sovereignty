#!/usr/bin/env bash

# Generate wp-config.php from template with DDEV database credentials.

TEMPLATE_FILE="/var/www/html/.ddev/orchestrate/templates/wp-config.php.tpl"
CONFIG_FILE="${WP_PATH}/wp-config.php"

if [ -f "$CONFIG_FILE" ]; then
    echo "wp-config.php already exists, skipping."
    return 0
fi

if [ ! -f "$TEMPLATE_FILE" ]; then
    echo "Template not found, generating via WP-CLI..."
    wp config create \
        --dbname=db \
        --dbuser=db \
        --dbpass=db \
        --dbhost=db \
        --path="${WP_PATH}" \
        --skip-check
else
    echo "Generating wp-config.php from template..."
    sed \
        -e "s|{{DB_NAME}}|db|g" \
        -e "s|{{DB_USER}}|db|g" \
        -e "s|{{DB_PASSWORD}}|db|g" \
        -e "s|{{DB_HOST}}|db|g" \
        "$TEMPLATE_FILE" > "$CONFIG_FILE"
fi

echo "wp-config.php created."
