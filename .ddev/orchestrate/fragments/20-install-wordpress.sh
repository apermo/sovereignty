#!/usr/bin/env bash

# Run WordPress core installation if not already installed.

if [ "${WP_DB_IMPORTED:-0}" = "1" ]; then
    echo "Database was imported, skipping install."
    return 0
fi

if wp core is-installed --path="${WP_PATH}" 2>/dev/null; then
    echo "WordPress already installed."
    return 0
fi

SITE_URL="${DDEV_PRIMARY_URL:-https://${DDEV_HOSTNAME}}"
SITE_TITLE="${DDEV_PROJECT:-WordPress}"

if [ "${WP_MULTISITE}" = "1" ]; then
    SUBDOMAIN_FLAG=""
    if [ "${WP_MULTISITE_SUBDOMAIN}" = "1" ]; then
        SUBDOMAIN_FLAG="--subdomains"
    fi

    echo "Installing WordPress multisite..."
    wp core multisite-install \
        --url="${SITE_URL}" \
        --title="${SITE_TITLE}" \
        --admin_user="${WP_ADMIN_USER}" \
        --admin_password="${WP_ADMIN_PASSWORD}" \
        --admin_email="${WP_ADMIN_EMAIL}" \
        --locale="${WP_LOCALE}" \
        --path="${WP_PATH}" \
        --skip-email \
        ${SUBDOMAIN_FLAG}
else
    echo "Installing WordPress..."
    wp core install \
        --url="${SITE_URL}" \
        --title="${SITE_TITLE}" \
        --admin_user="${WP_ADMIN_USER}" \
        --admin_password="${WP_ADMIN_PASSWORD}" \
        --admin_email="${WP_ADMIN_EMAIL}" \
        --locale="${WP_LOCALE}" \
        --path="${WP_PATH}" \
        --skip-email
fi

echo "WordPress installed at ${SITE_URL}"
