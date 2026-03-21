#!/usr/bin/env bash

wp rewrite structure '/%year%/%monthnum%/%day%/%postname%/' --path="${WP_PATH}"
wp rewrite flush --path="${WP_PATH}"
