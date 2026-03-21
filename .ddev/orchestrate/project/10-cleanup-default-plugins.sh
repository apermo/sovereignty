#!/usr/bin/env bash

wp plugin is-installed akismet --path="${WP_PATH}" && wp plugin uninstall akismet --path="${WP_PATH}" || true
wp plugin is-installed hello --path="${WP_PATH}" && wp plugin uninstall hello --path="${WP_PATH}" || true
