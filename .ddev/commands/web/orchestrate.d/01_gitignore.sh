#!/bin/bash

cat << 'EOF' > ".gitignore"
# Ignores everything in the docroot; WordPress is downloaded, not committed.
*
EOF
