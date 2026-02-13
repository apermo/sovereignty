#!/bin/bash

if ! wp core download --version="${WP_VERSION}" --locale="${WP_LOCALE}"; then
 echo 'WordPress is already installed.'
 exit
fi
