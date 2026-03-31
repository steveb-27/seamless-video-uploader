# Seamless Video Uploader - WordPress Plugin

A WordPress plugin that enables seamless video uploads through the media library with automatic autoplay, muting, and fullscreen controls. Works with pages, posts, and WooCommerce product galleries.

## Features

- ✅ Upload videos through the standard WordPress media uploader
- ✅ Automatic video insertion with autoplay (muted)
- ✅ Built-in fullscreen toggle button
- ✅ Works with pages, posts, and custom post types
- ✅ Full WooCommerce product gallery integration
- ✅ Support for multiple video formats (MP4, WebM, MOV, AVI, etc.)
- ✅ Responsive and mobile-friendly
- ✅ Automatic pause when video is out of viewport (performance optimization)
- ✅ Click video to toggle mute/unmute
- ✅ Keyboard support (press 'F' for fullscreen)

## Installation

1. **Upload the plugin files:**
    - Create a folder named `seamless-video-uploader` in `/wp-content/plugins/`
    - Upload all plugin files to this folder

2. **Activate the plugin:**
    - Go to WordPress Admin → Plugins
    - Find "Seamless Video Uploader" in the list
    - Click "Activate"

3. **That's it!** The plugin works immediately.

## File Structure

```
seamless-video-uploader/
├── seamless-video-uploader.php    (Main plugin file)
├── css/
│   └── frontend.css               (Frontend styles)
├── js/
│   ├── frontend.js                (Frontend JavaScript)
│   └── admin.js                   (Admin/media uploader JavaScript)
└── README.md                      (This file)
```

## Usage

### Inserting Videos in Pages/Posts

1. Create or edit a page/post
2. Click "Add Media" button
3. Upload your video file (or select existing video)
4. Click "Insert into post"
5. The video will automatically be inserted with autoplay controls

### Using Videos in WooCommerce Product Galleries

1. Edit a product
2. In the "Product Gallery" section, click "Add product gallery images"
3. Upload or select your video
4. The video will appear in the gallery alongside images
5. Videos will autoplay when visible in the gallery

### Video Controls

- **Autoplay**: Videos start playing automatically when visible (muted)
- **Fullscreen**: Click the fullscreen button (⛶) in bottom-right corner
- **Mute/Unmute**: Click anywhere on the video to toggle sound
- **Standard Controls**: Pause, play, volume, and timeline controls are available

### Supported Video Formats

- MP4 (`.mp4`)
- WebM (`.webm`)
- MOV (`.mov`)
- M4V (`.m4v`)
- AVI (`.avi`)
- WMV (`.wmv`)
- MPEG (`.mpg`, `.mpeg`)
- OGV (`.ogv`)

## Customization

### Modify Video Appearance

Edit `/css/frontend.css` to change:
- Video container styles
- Fullscreen button appearance
- Aspect ratios
- Responsive breakpoints

### Change Video Behavior

Edit `/js/frontend.js` to modify:
- Autoplay settings
- Intersection Observer thresholds
- Keyboard shortcuts
- Mute/unmute behavior

### Adjust Default Attributes

In `seamless-video-uploader.php`, modify the `generate_video_html()` function to change default video attributes like:
- `autoplay`
- `muted`
- `loop`
- `playsinline`
- `controlslist`

## Performance Optimization

The plugin includes several optimizations:

1. **Lazy Loading**: Videos only play when visible in viewport
2. **Automatic Pause**: Videos pause when scrolled out of view
3. **Efficient DOM Handling**: Uses event delegation and observers
4. **Minimal Resources**: Lightweight CSS and JavaScript

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (iOS requires `playsinline` attribute)
- Mobile browsers: Full support with touch controls

## Troubleshooting

### Videos won't autoplay
- Some browsers block autoplay with sound. The plugin mutes videos by default to allow autoplay.
- Check browser console for any error messages.

### Fullscreen not working
- Ensure your browser allows fullscreen API.
- Try using keyboard shortcut 'F' instead of clicking button.

### Videos not appearing in WooCommerce gallery
- Verify WooCommerce is installed and activated.
- Clear browser cache and WooCommerce cache.
- Check that video format is supported.

### File upload fails
- Check your server's upload size limits in `php.ini`:
    - `upload_max_filesize`
    - `post_max_size`
    - `max_execution_time`

## Developer Hooks

### Filters

```php
// Modify video HTML output
add_filter('sb27_video_html', 'custom_video_html', 10, 3);
function custom_video_html($html, $url, $id) {
    // Your custom HTML
    return $html;
}

// Modify allowed video MIME types
add_filter('sb27_video_mimes', 'custom_video_mimes');
function custom_video_mimes($mimes) {
    $mimes['mkv'] = 'video/x-matroska';
    return $mimes;
}
```

### Actions

```php
// Run code when plugin initializes
add_action('sb27_init', 'my_custom_function');
function my_custom_function() {
    // Your code here
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- jQuery (included with WordPress)

## License

GPL v2 or later

## Support

For issues, questions, or feature requests, please:
1. Check the troubleshooting section above
2. Review your browser console for errors
3. Verify all files are properly uploaded

## Changelog

### Version 1.0.0
- Initial release
- Video upload support
- Autoplay with mute
- Fullscreen controls
- WooCommerce integration
- Responsive design
- Performance optimizations

## Credits

Developed for seamless video integration in WordPress and WooCommerce.