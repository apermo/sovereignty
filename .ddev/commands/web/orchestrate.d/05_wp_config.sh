#!/bin/bash

printf '
if ( defined( "WP_CLI" ) && WP_CLI && ! isset( $_SERVER["HTTP_HOST"] ) ) {
    $_SERVER["HTTP_HOST"] = "%s";
}
' "${DDEV_HOSTNAME}" | wp config create \
  --dbname=db \
  --dbuser=db \
  --dbpass=db \
  --dbhost=db \
  --force \
  --extra-php

wp config set WP_DEBUG true --raw
wp config set WP_DEBUG_DISPLAY true --raw
wp config set WP_DEBUG_LOG true --raw
wp config set SCRIPT_DEBUG true --raw
wp config set WP_SITEURL '(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] ? "https://" : "http://") . $_SERVER["HTTP_HOST"]' --raw
wp config set WP_HOME '(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] ? "https://" : "http://") . $_SERVER["HTTP_HOST"]' --raw
