#!/usr/bin/env bash

# Download WordPress core if not already present.

mkdir -p "${WP_PATH}"

if [ -f "${WP_PATH}/wp-includes/version.php" ]; then
    INSTALLED_VERSION=$(grep "^\$wp_version" "${WP_PATH}/wp-includes/version.php" | cut -d"'" -f2)
    echo "WordPress ${INSTALLED_VERSION} already downloaded."

    if [ "$WP_VERSION" != "latest" ] && [ "$WP_VERSION" != "$INSTALLED_VERSION" ]; then
        echo "Updating to ${WP_VERSION}..."
        wp core download --version="${WP_VERSION}" --locale="${WP_LOCALE}" --path="${WP_PATH}" --force --skip-content
    fi
else
    echo "Downloading WordPress ${WP_VERSION}..."
    wp core download --version="${WP_VERSION}" --locale="${WP_LOCALE}" --path="${WP_PATH}"
fi
