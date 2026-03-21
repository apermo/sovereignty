#!/usr/bin/env bash

# Skip if content already seeded (more than the default "Hello World" post)
POST_COUNT=$(wp post list --post_type=post --post_status=publish --format=count --path="${WP_PATH}" 2>/dev/null)
if [ "${POST_COUNT:-0}" -gt 1 ]; then
    echo "Content already seeded, skipping."
    return 0
fi

# Remove default content
wp post delete 1 --force --path="${WP_PATH}" 2>/dev/null
wp post delete 2 --force --path="${WP_PATH}" 2>/dev/null

# Import media from theme assets
THEME_ASSETS="${WP_PATH}/wp-content/themes/${DDEV_PROJECT}/assets"

BEACH_ID=$(wp media import "${THEME_ASSETS}/images/beach.jpeg" --title="Beach" --porcelain --path="${WP_PATH}")
wp post meta update "$BEACH_ID" _wp_attachment_image_alt "Sandy beach with ocean waves" --path="${WP_PATH}"

SEA_ID=$(wp media import "${THEME_ASSETS}/images/sea.jpeg" --title="Sea" --porcelain --path="${WP_PATH}")
wp post meta update "$SEA_ID" _wp_attachment_image_alt "Calm sea at the horizon" --path="${WP_PATH}"

LIGHTS_ID=$(wp media import "${THEME_ASSETS}/images/lights.jpeg" --title="Lights" --porcelain --path="${WP_PATH}")
wp post meta update "$LIGHTS_ID" _wp_attachment_image_alt "City lights at night" --path="${WP_PATH}"

# Create taxonomy terms
wp term create category "Technology" --slug=technology --path="${WP_PATH}" 2>/dev/null
wp term create category "Travel" --slug=travel --path="${WP_PATH}" 2>/dev/null
wp term create category "Photography" --slug=photography --path="${WP_PATH}" 2>/dev/null

# --- Standard posts ---

POST_ID=$(wp post create \
    --post_title="Welcome to Sovereignty" \
    --post_content="<p>This is the main test post demonstrating the standard post format. It includes a featured image, categories, and tags.</p><p>The theme renders this using the single post template with full entry header, content area, and entry footer including share actions and taxonomy information.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp post term set "$POST_ID" category technology --path="${WP_PATH}"
wp post term add "$POST_ID" post_tag featured sample --path="${WP_PATH}"
wp post meta update "$POST_ID" _thumbnail_id "$BEACH_ID" --path="${WP_PATH}"

wp comment create \
    --comment_post_ID="$POST_ID" \
    --comment_content="This is a test comment to verify comment rendering." \
    --comment_author="Test Commenter" \
    --comment_author_email="commenter@example.com" \
    --comment_approved=1 \
    --path="${WP_PATH}"

POST_ID=$(wp post create \
    --post_title="Exploring the Coast" \
    --post_content="<p>A journey along the coastline, capturing the beauty of waves and sand. This post tests the theme with a different featured image and category.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp post term set "$POST_ID" category travel --path="${WP_PATH}"
wp post term add "$POST_ID" post_tag photography --path="${WP_PATH}"
wp post meta update "$POST_ID" _thumbnail_id "$SEA_ID" --path="${WP_PATH}"

POST_ID=$(wp post create \
    --post_title="City Lights at Night" \
    --post_content="<p>Urban photography showcasing city illumination after dark. This post demonstrates how the theme handles the photography category.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp post term set "$POST_ID" category photography --path="${WP_PATH}"
wp post meta update "$POST_ID" _thumbnail_id "$LIGHTS_ID" --path="${WP_PATH}"

POST_ID=$(wp post create \
    --post_title="Thoughts on Web Standards" \
    --post_content="<p>The modern web is built on open standards. HTML, CSS, and JavaScript form the foundation, while newer specifications like microformats2 and ActivityPub extend the web's social capabilities.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp post term set "$POST_ID" category technology --path="${WP_PATH}"

# --- Post format: aside ---

POST_ID=$(wp post create \
    --post_title="Quick Aside" \
    --post_content="<p>Just a quick thought — the IndieWeb is about owning your content and your identity online.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'aside');" --path="${WP_PATH}"

# --- Post format: quote ---

POST_ID=$(wp post create \
    --post_title="On the Web" \
    --post_content='<blockquote><p>The web is more a social creation than a technical one.</p><cite>Tim Berners-Lee</cite></blockquote>' \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'quote');" --path="${WP_PATH}"

# --- Post format: link ---

POST_ID=$(wp post create \
    --post_title="IndieWeb" \
    --post_content='<p><a href="https://indieweb.org">IndieWeb</a> — a people-focused alternative to the corporate web.</p>' \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'link');" --path="${WP_PATH}"

# --- Post format: status ---

POST_ID=$(wp post create \
    --post_title="Status Update" \
    --post_content="<p>Testing the sovereignty theme with all post formats. Looking good so far!</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'status');" --path="${WP_PATH}"

# --- Post format: image ---

POST_ID=$(wp post create \
    --post_title="Sunset Beach" \
    --post_content="<p>A beautiful sunset captured at the beach.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'image');" --path="${WP_PATH}"
wp post meta update "$POST_ID" _thumbnail_id "$BEACH_ID" --path="${WP_PATH}"

# --- Post format: video ---

POST_ID=$(wp post create \
    --post_title="Big Buck Bunny" \
    --post_content="https://www.youtube.com/watch?v=aqz-KE-bpKQ" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'video');" --path="${WP_PATH}"

# --- Post format: audio ---

POST_ID=$(wp post create \
    --post_title="Podcast Episode" \
    --post_content="<p>An audio post format test. In production this would contain an embedded audio player.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'audio');" --path="${WP_PATH}"

# --- Post format: gallery ---

POST_ID=$(wp post create \
    --post_title="Photo Gallery" \
    --post_content="[gallery ids=\"${BEACH_ID},${SEA_ID},${LIGHTS_ID}\"]" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'gallery');" --path="${WP_PATH}"

# --- Post format: chat ---

POST_ID=$(wp post create \
    --post_title="Development Chat" \
    --post_content="<p>Alice: Have you seen the new theme?<br>Bob: Yes, it looks great!<br>Alice: The microformats support is solid.<br>Bob: Agreed, very IndieWeb-friendly.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp eval "set_post_format(${POST_ID}, 'chat');" --path="${WP_PATH}"

# --- Pages ---

ABOUT_ID=$(wp post create \
    --post_type=page \
    --post_title="About" \
    --post_content="<p>This is the about page for testing the sovereignty theme. It demonstrates the page template with standard content.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")

NOW_ID=$(wp post create \
    --post_type=page \
    --post_title="Now" \
    --post_content="<p>This is what I am focused on right now. This page uses the special /now page template with h-now microformat support.</p>" \
    --post_status=publish \
    --porcelain \
    --path="${WP_PATH}")
wp post meta update "$NOW_ID" _wp_page_template "page-now.php" --path="${WP_PATH}"

# --- Navigation menu ---

MENU_ID=$(wp menu create "Primary Menu" --porcelain --path="${WP_PATH}")
wp menu item add-custom "$MENU_ID" "Home" "/" --path="${WP_PATH}"
wp menu item add-post "$MENU_ID" "$ABOUT_ID" --path="${WP_PATH}"
wp menu item add-post "$MENU_ID" "$NOW_ID" --path="${WP_PATH}"
wp menu location assign "$MENU_ID" primary --path="${WP_PATH}"

echo "Content seeded successfully."
