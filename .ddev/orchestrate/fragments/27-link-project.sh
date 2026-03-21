#!/usr/bin/env bash

# Symlink the project into the appropriate wp-content directory
# so WordPress can discover it as an installed plugin or theme.

PROJECT_NAME="${DDEV_PROJECT:-}"

if [ -z "$PROJECT_NAME" ]; then
    echo "No DDEV_PROJECT set, skipping link."
    return 0
fi

case "$PROJECT_MODE" in
    plugin)
        LINK_TARGET="${WP_PATH}/wp-content/plugins/${PROJECT_NAME}"
        ;;
    theme)
        LINK_TARGET="${WP_PATH}/wp-content/themes/${PROJECT_NAME}"
        ;;
    *)
        echo "Unknown PROJECT_MODE: ${PROJECT_MODE}. Skipping link."
        return 0
        ;;
esac

if [ -L "$LINK_TARGET" ]; then
    echo "Symlink already exists: ${LINK_TARGET}"
    return 0
fi

if [ -d "$LINK_TARGET" ]; then
    echo "Directory already exists: ${LINK_TARGET}. Skipping."
    return 0
fi

echo "Linking ${PROJECT_MODE}: ${PROJECT_NAME} -> ${DOCROOT}"
ln -sf "${DOCROOT}" "$LINK_TARGET"
echo "Project linked as ${PROJECT_MODE}."