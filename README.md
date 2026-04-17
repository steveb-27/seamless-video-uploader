# SteveB27 Video Uploader

A WordPress plugin that automatically applies autoplay, mute, loop, and a fullscreen toggle whenever a video is inserted from the media library — with no per-video configuration required. Also adds video support to WooCommerce product galleries.

**WordPress.org listing:** _coming soon_
**Author:** [Steve B-27](https://steveb27.com)

---

## How this differs from WordPress core

WordPress 6.0+ already supports autoplay, muted, loop, and playsinline in the Gutenberg **Video block** — so it's worth being clear about where this plugin adds value:

| Scenario | Core WordPress | This plugin |
|---|---|---|
| Gutenberg Video block — autoplay/muted/loop | ✅ Manual toggle per block | ✅ Applied automatically |
| Classic editor / media library "Insert into post" | ❌ No autoplay support | ✅ Automatic |
| Viewport-aware pause (Intersection Observer) | ❌ | ✅ |
| WooCommerce product gallery video support | ❌ | ✅ |
| Media & Text block autoplay | ❌ No UI controls | ✅ Via `enhance_video_content` filter |
| Expanded upload MIME types (MOV, AVI, WMV…) | ❌ Restricted by default | ✅ |
| Custom fullscreen overlay button + F shortcut | ❌ | ✅ |

The primary audience is users who want zero-configuration video behaviour — upload, insert, done — especially in WooCommerce stores and classic editor sites.

---

## File structure

```
steveb27-video-uploader/
├── steveb27-video-uploader.php   Main plugin file, all PHP logic
├── readme.txt                    WordPress.org directory listing
├── css/
│   ├── frontend.css              Public-facing video player styles
│   └── admin.css                 Admin notice styles
├── js/
│   ├── frontend.js               Intersection Observer, fullscreen, mute toggle
│   └── admin.js                  Media library enhancements
└── .wordpress-org/               Banner, icon, and screenshot assets (SVN only)
```

---

## Developer hooks

### Filters

```php
// Modify the generated video HTML
add_filter( 'sb27_video_html', function( $html, $url, $id ) {
    // return modified $html
    return $html;
}, 10, 3 );

// Add or remove allowed video MIME types
add_filter( 'sb27_video_mimes', function( $mimes ) {
    $mimes['mkv'] = 'video/x-matroska';
    return $mimes;
} );
```

### Actions

```php
// Runs after the plugin initialises
add_action( 'sb27_init', function() {
    // your code
} );
```

---

## Releases

Releases are deployed automatically to WordPress.org SVN via GitHub Actions when a new release is published. The release tag must match the `Version` in `steveb27-video-uploader.php` and the `Stable tag` in `readme.txt`.

**Release checklist:**
- [ ] Bump `Version` in plugin header (`steveb27-video-uploader.php`)
- [ ] Bump `Stable tag` in `readme.txt`
- [ ] Update `Changelog` section in `readme.txt`
- [ ] Add secrets to GitHub repo if not already set: `SVN_USERNAME`, `SVN_PASSWORD`
- [ ] Publish GitHub Release with a tag matching the version (e.g. `1.0.2`)

---

## Pre-launch checklist

### WordPress.org assets needed

The `.wordpress-org/` folder is deployed to the SVN `/assets/` directory (banners, icons, screenshots). Create these before submitting:

**Plugin icon** (displayed in the WP admin plugins list):
- [ ] `icon-128x128.png` — 128×128px
- [ ] `icon-256x256.png` — 256×256px (retina)

**Plugin banner** (displayed at the top of the WordPress.org listing page):
- [ ] `banner-772x250.png` — standard
- [ ] `banner-1544x500.png` — retina / high-DPI

**Screenshots** (must match the numbered descriptions in `readme.txt`):
- [ ] `screenshot-1.png` — The media library with a video selected, showing the play-icon overlay that the plugin adds to video thumbnails
- [ ] `screenshot-2.png` — A video inserted on a page in the editor, showing the rendered player with the fullscreen button visible in the corner
- [ ] `screenshot-3.png` — A WooCommerce product edit screen, with a video visible in the Product Gallery panel alongside images

Screenshots must be PNG or JPG, max 1200px wide. Capture at a clean zoom level (100%) with no browser chrome or OS UI visible.

### Functional testing

**Core insertion:**
- [ ] Upload an MP4 via Add Media → confirm it inserts with autoplay + muted + loop
- [ ] Upload a MOV/WebM file → confirm MIME type is accepted and inserts correctly
- [ ] Insert same video twice on one page → confirm both players work independently

**Autoplay / viewport behaviour:**
- [ ] Load a page with a video below the fold → confirm it does not play until scrolled into view
- [ ] Scroll video out of view → confirm it pauses
- [ ] Scroll back into view → confirm it resumes

**Fullscreen:**
- [ ] Click the fullscreen overlay button → confirm it enters fullscreen
- [ ] Press `F` key while video is focused → confirm keyboard shortcut works
- [ ] Press `Escape` → confirm fullscreen exits cleanly

**Mute toggle:**
- [ ] Click the video → confirm it unmutes
- [ ] Click again → confirm it mutes

**WooCommerce:**
- [ ] Add a video to a product gallery alongside images → confirm it renders correctly
- [ ] View product page → confirm video autoplays when visible

**Review notice:**
- [ ] Temporarily set `svu_activation_time` option to 8 days ago in the database
- [ ] Load the WP dashboard → confirm the notice appears only there, not on other admin screens
- [ ] Click "I already did!" → confirm it dismisses and does not reappear
- [ ] Repeat and click "Maybe later" → confirm it reappears after 7 more days
- [ ] Confirm the notice respects the `DISABLE_NAG_NOTICES` constant

**Compatibility:**
- [ ] Test with Gutenberg (block editor)
- [ ] Test with Classic Editor plugin active
- [ ] Test with WooCommerce active and inactive
- [ ] Test on Chrome, Firefox, Safari, and mobile Safari (iOS requires `playsinline`)

### Plugin Check tool

- [ ] Install the [Plugin Check plugin](https://wordpress.org/plugins/plugin-check/) on a test site
- [ ] Run it against SteveB27 Video Uploader and resolve any errors or warnings before submitting

### WordPress.org submission

- [ ] Create a free account at [wordpress.org](https://wordpress.org) if not already done (username: `steveb27`)
- [ ] Submit via [wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/)
- [ ] Wait for manual review (typically 1–10 business days)
- [ ] Once approved, set up `SVN_USERNAME` and `SVN_PASSWORD` secrets in this GitHub repo
- [ ] Make first release to push code to SVN

### Marketing

- [ ] Write a short launch post or tweet linking to the WordPress.org listing
- [ ] Add the plugin to your site at [steveb27.com](https://steveb27.com) — this is already set as the Plugin URI and Author URI
- [ ] Consider posting in [/r/Wordpress](https://reddit.com/r/Wordpress) and the [WordPress.org support forums](https://wordpress.org/support/) once listed
- [ ] Respond promptly to early support threads — review scores and response rate are visible on the listing and affect discoverability