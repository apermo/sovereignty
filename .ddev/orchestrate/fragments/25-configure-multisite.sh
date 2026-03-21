#!/usr/bin/env bash

# Write multisite constants to wp-config.php after multisite-install.
# wp core multisite-install creates network tables but does NOT write
# the required constants to wp-config.php — this fragment automates that.

if [ "${WP_DB_IMPORTED:-0}" = "1" ]; then
    echo "Database was imported, skipping multisite configuration."
    return 0
fi

if [ "${WP_MULTISITE}" != "1" ]; then
    return 0
fi

CONFIG_FILE="${WP_PATH}/wp-config.php"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "wp-config.php not found, skipping multisite configuration."
    return 0
fi

# Skip if already configured (idempotent)
if wp config get MULTISITE --path="${WP_PATH}" 2>/dev/null; then
    echo "Multisite constants already configured."
    return 0
fi

SITE_URL="${DDEV_PRIMARY_URL:-https://${DDEV_HOSTNAME}}"
DOMAIN=$(echo "$SITE_URL" | sed -e 's|https\?://||' -e 's|/.*||')

SUBDOMAIN_INSTALL="false"
if [ "${WP_MULTISITE_SUBDOMAIN}" = "1" ]; then
    SUBDOMAIN_INSTALL="true"
fi

echo "Writing multisite constants to wp-config.php..."
wp config set WP_ALLOW_MULTISITE true --raw --path="${WP_PATH}"
wp config set MULTISITE true --raw --path="${WP_PATH}"
wp config set SUBDOMAIN_INSTALL "${SUBDOMAIN_INSTALL}" --raw --path="${WP_PATH}"
wp config set DOMAIN_CURRENT_SITE "${DOMAIN}" --path="${WP_PATH}"
wp config set PATH_CURRENT_SITE "/" --path="${WP_PATH}"
wp config set SITE_ID_CURRENT_SITE 1 --raw --path="${WP_PATH}"
wp config set BLOG_ID_CURRENT_SITE 1 --raw --path="${WP_PATH}"

echo "Multisite constants configured."
