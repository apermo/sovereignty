#!/bin/bash

# Skip if content already seeded (more than the default "Hello World" post)
POST_COUNT=$(wp post list --post_type=post --post_status=publish --format=count 2>/dev/null)
if [ "${POST_COUNT:-0}" -gt 1 ]; then
    echo "Content already seeded, skipping."
    return 0
fi

# Remove default content
wp post delete 1 --force 2>/dev/null
wp post delete 2 --force 2>/dev/null

# Import media from theme assets
BEACH_ID=$(wp media import "${THEME_FOLDER}/assets/images/beach.jpeg" --title="Beach" --porcelain)
wp post meta update "$BEACH_ID" _wp_attachment_image_alt "Sandy beach with ocean waves"

SEA_ID=$(wp media import "${THEME_FOLDER}/assets/images/sea.jpeg" --title="Sea" --porcelain)
wp post meta update "$SEA_ID" _wp_attachment_image_alt "Calm sea at the horizon"

LIGHTS_ID=$(wp media import "${THEME_FOLDER}/assets/images/lights.jpeg" --title="Lights" --porcelain)
wp post meta update "$LIGHTS_ID" _wp_attachment_image_alt "City lights at night"

# Create taxonomy terms
wp term create category "Technology" --slug=technology 2>/dev/null
wp term create category "Travel" --slug=travel 2>/dev/null
wp term create category "Photography" --slug=photography 2>/dev/null

# --- Standard posts ---

POST_ID=$(wp post create \
    --post_title="Welcome to Sovereignty" \
    --post_content="<p>This is the main test post demonstrating the standard post format. It includes a featured image, categories, and tags.</p><p>The theme renders this using the single post template with full entry header, content area, and entry footer including share actions and taxonomy information.</p>" \
    --post_status=publish \
    --porcelain)
wp post term set "$POST_ID" category technology
wp post term add "$POST_ID" post_tag featured sample
wp post meta update "$POST_ID" _thumbnail_id "$BEACH_ID"

wp comment create \
    --comment_post_ID="$POST_ID" \
    --comment_content="This is a test comment to verify comment rendering." \
    --comment_author="Test Commenter" \
    --comment_author_email="commenter@example.com" \
    --comment_approved=1

POST_ID=$(wp post create \
    --post_title="Exploring the Coast" \
    --post_content="<p>A journey along the coastline, capturing the beauty of waves and sand. This post tests the theme with a different featured image and category.</p>" \
    --post_status=publish \
    --porcelain)
wp post term set "$POST_ID" category travel
wp post term add "$POST_ID" post_tag photography
wp post meta update "$POST_ID" _thumbnail_id "$SEA_ID"

POST_ID=$(wp post create \
    --post_title="City Lights at Night" \
    --post_content="<p>Urban photography showcasing city illumination after dark. This post demonstrates how the theme handles the photography category.</p>" \
    --post_status=publish \
    --porcelain)
wp post term set "$POST_ID" category photography
wp post meta update "$POST_ID" _thumbnail_id "$LIGHTS_ID"

POST_ID=$(wp post create \
    --post_title="Thoughts on Web Standards" \
    --post_content="<p>The modern web is built on open standards. HTML, CSS, and JavaScript form the foundation, while newer specifications like microformats2 and ActivityPub extend the web's social capabilities.</p>" \
    --post_status=publish \
    --porcelain)
wp post term set "$POST_ID" category technology

# --- Post format: aside ---

POST_ID=$(wp post create \
    --post_title="Quick Aside" \
    --post_content="<p>Just a quick thought — the IndieWeb is about owning your content and your identity online.</p>" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'aside');"

# --- Post format: quote ---

POST_ID=$(wp post create \
    --post_title="On the Web" \
    --post_content='<blockquote><p>The web is more a social creation than a technical one.</p><cite>Tim Berners-Lee</cite></blockquote>' \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'quote');"

# --- Post format: link ---

POST_ID=$(wp post create \
    --post_title="IndieWeb" \
    --post_content='<p><a href="https://indieweb.org">IndieWeb</a> — a people-focused alternative to the corporate web.</p>' \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'link');"

# --- Post format: status ---

POST_ID=$(wp post create \
    --post_title="Status Update" \
    --post_content="<p>Testing the sovereignty theme with all post formats. Looking good so far!</p>" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'status');"

# --- Post format: image ---

POST_ID=$(wp post create \
    --post_title="Sunset Beach" \
    --post_content="<p>A beautiful sunset captured at the beach.</p>" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'image');"
wp post meta update "$POST_ID" _thumbnail_id "$BEACH_ID"

# --- Post format: video ---

POST_ID=$(wp post create \
    --post_title="Big Buck Bunny" \
    --post_content="https://www.youtube.com/watch?v=aqz-KE-bpKQ" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'video');"

# --- Post format: audio ---

POST_ID=$(wp post create \
    --post_title="Podcast Episode" \
    --post_content="<p>An audio post format test. In production this would contain an embedded audio player.</p>" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'audio');"

# --- Post format: gallery ---

POST_ID=$(wp post create \
    --post_title="Photo Gallery" \
    --post_content="[gallery ids=\"${BEACH_ID},${SEA_ID},${LIGHTS_ID}\"]" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'gallery');"

# --- Post format: chat ---

POST_ID=$(wp post create \
    --post_title="Development Chat" \
    --post_content="<p>Alice: Have you seen the new theme?<br>Bob: Yes, it looks great!<br>Alice: The microformats support is solid.<br>Bob: Agreed, very IndieWeb-friendly.</p>" \
    --post_status=publish \
    --porcelain)
wp eval "set_post_format(${POST_ID}, 'chat');"

# --- Pages ---

ABOUT_ID=$(wp post create \
    --post_type=page \
    --post_title="About" \
    --post_content="<p>This is the about page for testing the sovereignty theme. It demonstrates the page template with standard content.</p>" \
    --post_status=publish \
    --porcelain)

NOW_ID=$(wp post create \
    --post_type=page \
    --post_title="Now" \
    --post_content="<p>This is what I am focused on right now. This page uses the special /now page template with h-now microformat support.</p>" \
    --post_status=publish \
    --porcelain)
wp post meta update "$NOW_ID" _wp_page_template "page-now.php"

# --- Navigation menu ---

MENU_ID=$(wp menu create "Primary Menu" --porcelain)
wp menu item add-custom "$MENU_ID" "Home" "/"
wp menu item add-post "$MENU_ID" "$ABOUT_ID"
wp menu item add-post "$MENU_ID" "$NOW_ID"
wp menu location assign "$MENU_ID" primary

echo "Content seeded successfully."