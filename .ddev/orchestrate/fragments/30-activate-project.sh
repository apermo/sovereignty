#!/usr/bin/env bash

# Activate the project plugin or theme based on PROJECT_MODE.

PROJECT_NAME="${DDEV_PROJECT:-}"

if [ -z "$PROJECT_NAME" ]; then
    echo "No DDEV_PROJECT set, skipping activation."
    return 0
fi

case "$PROJECT_MODE" in
    plugin)
        echo "Activating plugin: ${PROJECT_NAME}..."
        if wp plugin is-installed "$PROJECT_NAME" --path="${WP_PATH}" 2>/dev/null; then
            if [ "${WP_MULTISITE}" = "1" ]; then
                wp plugin activate "$PROJECT_NAME" --network --path="${WP_PATH}" 2>/dev/null || true
            else
                wp plugin activate "$PROJECT_NAME" --path="${WP_PATH}" 2>/dev/null || true
            fi
        else
            echo "Plugin '${PROJECT_NAME}' not found in plugins directory."
            echo "For mu-plugins, no activation needed — loaded automatically."
        fi
        ;;
    theme)
        echo "Activating theme: ${PROJECT_NAME}..."
        if [ "${WP_MULTISITE}" = "1" ]; then
            wp theme enable "$PROJECT_NAME" --network --path="${WP_PATH}" 2>/dev/null || true
            wp theme activate "$PROJECT_NAME" --path="${WP_PATH}" 2>/dev/null || \
                echo "Theme '${PROJECT_NAME}' not found."
        else
            wp theme activate "$PROJECT_NAME" --path="${WP_PATH}" 2>/dev/null || \
                echo "Theme '${PROJECT_NAME}' not found."
        fi
        ;;
    *)
        echo "Unknown PROJECT_MODE: ${PROJECT_MODE}. Skipping activation."
        ;;
esac
