=== SteveB27 Video Uploader ===
Contributors: steveb27
Tags: video, upload, media, autoplay, woocommerce
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable video uploads through the media library with autoplay, muted, and fullscreen controls. Works with pages, posts, and WooCommerce.

== Description ==

SteveB27 Video Uploader makes it easy to add videos to your WordPress site with the same simplicity as adding images. Videos automatically play muted with fullscreen controls when inserted into pages, posts, or WooCommerce product galleries.

= Features =

* Upload videos through the standard WordPress media uploader
* Automatic video insertion with autoplay (muted)
* Built-in fullscreen toggle button
* Works with pages, posts, and custom post types
* Full WooCommerce product gallery integration
* Support for multiple video formats (MP4, WebM, MOV, AVI, etc.)
* Responsive and mobile-friendly
* Automatic pause when video is out of viewport (performance optimization)
* Click video to toggle mute/unmute
* Keyboard support (press 'F' for fullscreen)

= Supported Video Formats =

* MP4 (`.mp4`)
* WebM (`.webm`)
* MOV (`.mov`)
* M4V (`.m4v`)
* AVI (`.avi`)
* WMV (`.wmv`)
* MPEG (`.mpg`, `.mpeg`)
* OGV (`.ogv`)

= Usage =

**For Pages/Posts:**
1. Create or edit a page/post
2. Click "Add Media" button
3. Upload your video file (or select existing video)
4. Click "Insert into post"
5. The video will automatically be inserted with autoplay controls

**For WooCommerce Products:**
1. Edit a product
2. In the "Product Gallery" section, click "Add product gallery images"
3. Upload or select your video
4. The video will appear in the gallery alongside images
5. Videos will autoplay when visible in the gallery

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/steveb27-video-uploader/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Upload videos through the media library like you would images

== Frequently Asked Questions ==

= Why won't my videos autoplay? =

Some browsers block autoplay with sound. The plugin mutes videos by default to allow autoplay. Users can click the video to unmute.

= How do I make a video fullscreen? =

Click the fullscreen button in the bottom-right corner of any video, or press 'F' on your keyboard while the video is focused.

= Can I use this with WooCommerce? =

Yes! The plugin fully supports WooCommerce product galleries. Just add videos to your product gallery the same way you add images.

= What video formats are supported? =

The plugin supports MP4, WebM, MOV, M4V, AVI, WMV, MPEG, and OGV formats.

== Changelog ==

= 1.1.1 =
* Fixed: Inline script properly enqueued using wp_add_inline_script()
* Fixed: All function names now use unique steveb27_vu_ prefix
* Fixed: All CSS class names now use steveb27- prefix
* Improved: Better compliance with WordPress plugin directory guidelines

= 1.1.0 =
* Added: WooCommerce product gallery support
* Added: Admin video thumbnail placeholders
* Added: Review notice system
* Improved: Video attachment handling in admin
* Improved: Better mobile support

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.1 =
This version fixes all WordPress plugin directory compliance issues. Update recommended.