=== Seamless Video Uploader ===
Contributors: steveb27
Tags: video, upload, autoplay, woocommerce, media
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Upload videos through the WordPress media library and insert them with autoplay, muted playback, and fullscreen controls — no shortcodes needed.

== Description ==

Seamless Video Uploader extends the standard WordPress media library to handle video files as first-class citizens. Upload a video, click "Insert into post," and it lands on the page as a fully configured video player: autoplaying, muted, looping, and mobile-friendly — no shortcodes, no gutenberg blocks to configure, no fuss.

**Features**

* Upload videos through the standard WordPress media uploader
* Automatic insertion with autoplay (muted) and loop
* Built-in fullscreen toggle button
* Works with pages, posts, and custom post types
* Full WooCommerce product gallery integration
* Support for MP4, WebM, MOV, AVI, WMV, MPEG, and OGV
* Responsive and mobile-friendly
* Videos pause automatically when scrolled out of viewport
* Click the video to toggle mute/unmute
* Keyboard shortcut: press **F** for fullscreen

== Installation ==

1. Upload the `seamless-video-uploader` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress Admin.
3. That's it — the plugin works immediately.

== Frequently Asked Questions ==

= My videos won't autoplay. What's wrong? =

Most browsers require videos to be muted before they allow autoplay. The plugin mutes videos by default for exactly this reason. If autoplay still doesn't work, check your browser console for errors.

= Fullscreen isn't working. =

Ensure your browser allows the Fullscreen API (some iframe embeds restrict it). You can also use the **F** keyboard shortcut as an alternative.

= Videos aren't appearing in my WooCommerce product gallery. =

Make sure WooCommerce is installed and activated, then clear your browser cache and any WooCommerce caches. Also confirm the video format is one of the supported types listed above.

= My file upload is failing. =

Check your server's PHP upload limits in `php.ini`:
* `upload_max_filesize`
* `post_max_size`
* `max_execution_time`

Large video files often require increasing these values.

= How do I change the video appearance or behaviour? =

Edit `css/frontend.css` to change the visual style, and `js/frontend.js` to adjust autoplay thresholds, keyboard shortcuts, or mute behaviour. See the Developer Hooks section in the README for filter/action reference.

== Screenshots ==

1. Videos automatically get a play-icon overlay in the media library.
2. Inserted video player with fullscreen button on a page.
3. Video in a WooCommerce product gallery alongside images.

== Changelog ==
= 1.0.2 =
* Added deployment workflow to Wordpress.org
* Added marketing graphics for Wordpress.org plugin directory

= 1.0.1 =
* Added `Requires at least`, `Tested up to`, and `Requires PHP` headers.
* Added internationalisation (i18n) support across all user-facing strings.
* Added polite review request notice (shown once after 7 days, dismissible).
* Added "Leave a Review" and "More Plugins" links on the Plugins screen.
* Extracted inline admin styles to `css/admin.css`.
* Introduced plugin constants (`SVU_VERSION`, `SVU_PLUGIN_URL`, etc.) for maintainability.
* Added activation/deactivation hooks.

= 1.0.0 =
* Initial release.
* Video upload support through the media library.
* Autoplay with mute and loop.
* Fullscreen controls.
* WooCommerce product gallery integration.
* Responsive design and viewport-aware pause.

== Upgrade Notice ==

= 1.0.2 =
Minor improvements: Add directory graphics and packaging workflow