<?php
/**
 * Plugin Name: SteveB27 Video Uploader
 * Plugin URI: https://steveb27.com/product/steveb27-video-uploader/
 * Description: Enables video uploads through the media library and automatically inserts them with autoplay, muted, and fullscreen controls. Works with pages, posts, and WooCommerce product galleries.
 * Version: 1.1.0
 * Author: Steve B-27
 * Author URI: https://steveb27.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: steveb27-video-uploader
 * Requires at least: 6.0
 * Requires PHP: 7.2
 * Tested up to: 6.9
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('SVU_VERSION', '1.1.0');
define('SVU_PLUGIN_FILE', __FILE__);
define('SVU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SVU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SVU_REVIEW_URL', 'https://wordpress.org/support/plugin/steveb27-video-uploader/reviews/#new-post');
define('SVU_AUTHOR_URL', 'https://steveb27.com');

class SteveB27_Video_Uploader {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('upload_mimes',                              array($this, 'add_video_mime_types'));
        add_filter('media_send_to_editor',                      array($this, 'insert_video_html'), 10, 3);
        add_filter('wp_get_attachment_image',                   array($this, 'replace_video_thumbnail'), 10, 5);
        add_filter('wp_get_attachment_image_src',               array($this, 'video_attachment_image_src'), 10, 4);
        add_action('admin_enqueue_scripts',                     array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts',                        array($this, 'enqueue_frontend_scripts'));
        add_filter('woocommerce_single_product_image_thumbnail_html', array($this, 'woocommerce_video_thumbnail'), 10, 2);
        add_filter('the_content',                               array($this, 'enhance_video_content'), 20);
        add_action('admin_notices',                             array($this, 'maybe_show_review_notice'));
        add_action('wp_ajax_svu_dismiss_review_notice',         array($this, 'ajax_dismiss_review_notice'));
        add_filter('plugin_action_links_' . plugin_basename(SVU_PLUGIN_FILE), array($this, 'add_plugin_action_links'));
    }

    // -------------------------------------------------------------------------
    // Video MIME types
    // -------------------------------------------------------------------------

    public function add_video_mime_types($mimes) {
        $mimes['mp4']  = 'video/mp4';
        $mimes['m4v']  = 'video/x-m4v';
        $mimes['mov']  = 'video/quicktime';
        $mimes['wmv']  = 'video/x-ms-wmv';
        $mimes['avi']  = 'video/avi';
        $mimes['mpg']  = 'video/mpeg';
        $mimes['mpeg'] = 'video/mpeg';
        $mimes['ogv']  = 'video/ogg';
        $mimes['webm'] = 'video/webm';
        return $mimes;
    }

    // -------------------------------------------------------------------------
    // Video HTML generation
    // -------------------------------------------------------------------------

    public function insert_video_html($html, $id, $attachment) {
        $post = get_post($id);
        if (strpos($post->post_mime_type, 'video/') === 0) {
            $url  = wp_get_attachment_url($id);
            $html = $this->generate_video_html($url, $id);
        }
        return $html;
    }

    private function generate_video_html($url, $id = 0, $class = '') {
        $poster = '';
        if ($id) {
            $thumb_id = get_post_thumbnail_id($id);
            if ($thumb_id) {
                $poster = wp_get_attachment_url($thumb_id);
            }
        }

        $poster_attr = $poster ? 'poster="' . esc_url($poster) . '"' : '';
        $class_attr  = $class  ? 'class="'  . esc_attr($class)  . '"' : '';

        return sprintf(
            '<div class="sb27-video-container" data-video-id="%d">
                <video %s %s controls muted autoplay loop playsinline controlslist="nodownload">
                    <source src="%s" type="video/mp4">
                    %s
                </video>
                <button class="sb27-fullscreen-btn" aria-label="%s">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 2h6v2H4v4H2V2zm14 0h-6v2h4v4h2V2zM2 18h6v-2H4v-4H2v6zm16 0h-6v-2h4v-4h2v6z"/>
                    </svg>
                </button>
            </div>',
            intval($id),
            $class_attr,
            $poster_attr,
            esc_url($url),
            esc_html__('Your browser does not support the video tag.', 'steveb27-video-uploader'),
            esc_attr__('Toggle Fullscreen', 'steveb27-video-uploader')
        );
    }

    public function replace_video_thumbnail($html, $attachment_id, $size, $icon, $attr) {
        $post = get_post($attachment_id);
        if ($post && strpos($post->post_mime_type, 'video/') === 0) {
            // In admin (media library grid, post editor, WooCommerce gallery metabox)
            // a full video player breaks the UI — return a styled thumbnail placeholder instead.
            if (is_admin()) {
                return $this->generate_admin_video_thumb($attachment_id);
            }
            $url   = wp_get_attachment_url($attachment_id);
            $class = isset($attr['class']) ? $attr['class'] : '';
            return $this->generate_video_html($url, $attachment_id, $class);
        }
        return $html;
    }

    /**
     * A small dark thumbnail with a play icon, used wherever wp_get_attachment_image()
     * is called in admin for a video attachment (media library, gallery metaboxes, etc.).
     */
    private function generate_admin_video_thumb($attachment_id) {
        $title = esc_attr(get_the_title($attachment_id));
        return sprintf(
            '<div class="sb27-admin-video-thumb" title="%s" aria-label="%s">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="40" height="40" fill="#1a1a2e"/>
                    <polygon points="14,10 14,30 32,20" fill="#ffffff" opacity="0.85"/>
                </svg>
                <span class="sb27-admin-video-label">%s</span>
            </div>',
            $title,
            esc_attr__('Video attachment', 'steveb27-video-uploader'),
            esc_html__('VIDEO', 'steveb27-video-uploader')
        );
    }

    public function woocommerce_video_thumbnail($html, $attachment_id) {
        $post = get_post($attachment_id);
        if ($post && strpos($post->post_mime_type, 'video/') === 0) {
            $url        = wp_get_attachment_url($attachment_id);
            $thumb_url  = $this->get_video_poster_url($attachment_id);
            $title      = esc_attr(get_the_title($attachment_id));

            // WooCommerce's gallery JS (flexslider + photoswipe) reads data-thumb
            // from the .woocommerce-product-gallery__image wrapper to build the
            // thumbnail strip. Without it the item is silently ignored.
            // We also add data-svu-video so our frontend JS can disable the
            // photoswipe lightbox on this item (lightbox can't display video).
            return sprintf(
                '<div data-thumb="%s" data-thumb-alt="%s" class="woocommerce-product-gallery__image" data-svu-video="1">
                    %s
                </div>',
                esc_url($thumb_url),
                $title,
                $this->generate_video_html($url, $attachment_id)
            );
        }
        return $html;
    }

    /**
     * Returns the best available thumbnail URL for a video:
     * the attachment's featured image (poster) if set, otherwise an inline SVG
     * data URI so there is always something for the WooCommerce thumbnail strip.
     */
    private function get_video_poster_url($attachment_id) {
        $thumb_id = get_post_thumbnail_id($attachment_id);
        if ($thumb_id) {
            $src = wp_get_attachment_image_src($thumb_id, 'woocommerce_thumbnail');
            if ($src) {
                return $src[0];
            }
        }
        // Inline SVG data URI — no external request, always available.
        return $this->get_video_placeholder_url();
    }

    /**
     * Hook: wp_get_attachment_image_src
     *
     * WooCommerce's thumbnail strip (and any other caller of wp_get_attachment_image_src)
     * gets false/empty for video attachments because videos have no intrinsic image.
     * This causes the thumbnail <img> to render with src="" (blank image).
     *
     * We intercept here — before the <img> is built — and return a valid image array
     * pointing to the poster image or our SVG placeholder, so the thumbnail always
     * has something to display.
     *
     * @param array|false $image  Existing result: [ url, width, height, is_intermediate ]
     * @param int         $attachment_id
     * @param mixed       $size
     * @param bool        $icon
     * @return array|false
     */
    public function video_attachment_image_src($image, $attachment_id, $size, $icon) {
        // Only act when WP couldn't produce an image (the normal case for videos).
        if ($image) {
            return $image;
        }

        $post = get_post($attachment_id);
        if (!$post || strpos($post->post_mime_type, 'video/') !== 0) {
            return $image;
        }

        // Try a real poster image first.
        $thumb_id = get_post_thumbnail_id($attachment_id);
        if ($thumb_id) {
            $src = wp_get_attachment_image_src($thumb_id, $size);
            if ($src) {
                return $src;
            }
        }

        // Fall back to SVG placeholder.  Return dimensions that match a square
        // thumbnail so layout doesn't collapse.
        $placeholder = $this->get_video_placeholder_url();
        return array($placeholder, 200, 200, false);
    }

    /**
     * URL to the plugin's video placeholder image.
     * Using a real file URL (not a data URI) is essential — esc_url() silently
     * strips data: URIs, which would leave data-thumb="" and cause WooCommerce's
     * gallery JS to render <img src="">.
     */
    private function get_video_placeholder_url() {
        return SVU_PLUGIN_URL . 'img/video-placeholder.svg';
    }

    public function enhance_video_content($content) {
        if (has_shortcode($content, 'video')) {
            $content = preg_replace_callback(
                '/\[video([^\]]*)\]/i',
                function($matches) {
                    $attrs = $matches[1];
                    if (strpos($attrs, 'autoplay') === false) { $attrs .= ' autoplay="on"'; }
                    if (strpos($attrs, 'muted')    === false) { $attrs .= ' muted="on"';    }
                    if (strpos($attrs, 'loop')     === false) { $attrs .= ' loop="on"';     }
                    return '[video' . $attrs . ']';
                },
                $content
            );
        }
        return $content;
    }

    // -------------------------------------------------------------------------
    // Scripts & styles
    // -------------------------------------------------------------------------

    public function enqueue_admin_scripts($hook) {
        if (in_array($hook, array('post.php', 'post-new.php', 'upload.php'), true)) {
            wp_enqueue_script(
                'svu-admin',
                SVU_PLUGIN_URL . 'js/admin.js',
                array('jquery'),
                SVU_VERSION,
                true
            );
        }

        wp_enqueue_style('svu-admin', SVU_PLUGIN_URL . 'css/admin.css', array(), SVU_VERSION);

        // Provide AJAX URL + nonce to all admin screens (needed for notice dismissal)
        wp_localize_script('jquery', 'svuAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('svu_dismiss_review_notice'),
        ));
    }

    public function enqueue_frontend_scripts() {
        wp_enqueue_style('svu-frontend', SVU_PLUGIN_URL . 'css/frontend.css', array(), SVU_VERSION);
        wp_enqueue_script('svu-frontend', SVU_PLUGIN_URL . 'js/frontend.js', array('jquery'), SVU_VERSION, true);
    }

    // -------------------------------------------------------------------------
    // Review notice
    // -------------------------------------------------------------------------

    /**
     * Show a polite review request on the dashboard after 7 days.
     * - Admins only
     * - Dashboard screen only
     * - Shown once; "Maybe later" snoozes for another 7 days
     * - Respects the community DISABLE_NAG_NOTICES constant
     */
    public function maybe_show_review_notice() {
        if (defined('DISABLE_NAG_NOTICES') && DISABLE_NAG_NOTICES) {
            return;
        }
        if (!current_user_can('manage_options')) {
            return;
        }
        $screen = get_current_screen();
        if (!$screen || 'dashboard' !== $screen->id) {
            return;
        }
        $user_id = get_current_user_id();
        if (get_user_meta($user_id, 'svu_review_notice_dismissed', true)) {
            return;
        }
        $activated = get_option('svu_activation_time');
        if (!$activated || (time() - intval($activated)) < (7 * DAY_IN_SECONDS)) {
            return;
        }
        ?>
        <div class="notice svu-review-notice" id="svu-review-notice">
            <div class="svu-review-notice__inner">
                <div class="svu-review-notice__icon">🎬</div>
                <div class="svu-review-notice__content">
                    <p>
                        <strong><?php esc_html_e('SteveB27 Video Uploader', 'steveb27-video-uploader'); ?></strong> &mdash;
                        <?php esc_html_e("You've been using the plugin for a week — thanks for sticking with it! If it's been useful, a quick review on WordPress.org would mean a lot and helps others find it.", 'steveb27-video-uploader'); ?>
                    </p>
                    <p class="svu-review-notice__actions">
                        <a href="<?php echo esc_url(SVU_REVIEW_URL); ?>" target="_blank" rel="noopener noreferrer"
                           class="button button-primary svu-review-btn" data-svu-action="reviewed">
                            ⭐ <?php esc_html_e('Leave a Review', 'steveb27-video-uploader'); ?>
                        </a>
                        <a href="#" class="button svu-review-btn" data-svu-action="reviewed">
                            <?php esc_html_e('I already did!', 'steveb27-video-uploader'); ?>
                        </a>
                        <a href="#" class="button-link svu-review-later" data-svu-action="later">
                            <?php esc_html_e('Maybe later', 'steveb27-video-uploader'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <script>
            (function($) {
                $('#svu-review-notice').on('click', '[data-svu-action]', function(e) {
                    var $btn   = $(this);
                    var action = $btn.data('svu-action');
                    // Only prevent default for non-link actions or internal anchors
                    if ($btn.attr('href') === '#' || !$btn.attr('href')) {
                        e.preventDefault();
                    }
                    $.post(svuAdmin.ajaxUrl, {
                        action: 'svu_dismiss_review_notice',
                        nonce:  svuAdmin.nonce,
                        dismissal_type: action
                    });
                    $('#svu-review-notice').fadeOut(300, function() { $(this).remove(); });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function ajax_dismiss_review_notice() {
        check_ajax_referer('svu_dismiss_review_notice', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_die();
        }
        $type    = isset($_POST['dismissal_type']) ? sanitize_text_field(wp_unslash($_POST['dismissal_type'])) : 'dismissed';
        $user_id = get_current_user_id();
        if ('later' === $type) {
            // Snooze: reset the clock so the notice reappears after another 7 days
            update_option('svu_activation_time', time());
        } else {
            update_user_meta($user_id, 'svu_review_notice_dismissed', '1');
        }
        wp_die();
    }

    // -------------------------------------------------------------------------
    // Plugin action links
    // -------------------------------------------------------------------------

    public function add_plugin_action_links($links) {
        $extra = array(
            '<a href="' . esc_url(SVU_REVIEW_URL) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html__('Leave a Review', 'steveb27-video-uploader') . '</a>',
            '<a href="' . esc_url(SVU_AUTHOR_URL) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html__('More Plugins', 'steveb27-video-uploader') . '</a>',
        );
        return array_merge($links, $extra);
    }
}

// -------------------------------------------------------------------------
// Activation / deactivation hooks
// -------------------------------------------------------------------------

function svu_on_activation() {
    // add_option does nothing if the option already exists, preserving the
    // original timestamp across deactivate/reactivate cycles.
    add_option('svu_activation_time', time());
}
register_activation_hook(SVU_PLUGIN_FILE, 'svu_on_activation');

function svu_on_deactivation() {
    delete_option('svu_activation_time');
}
register_deactivation_hook(SVU_PLUGIN_FILE, 'svu_on_deactivation');

// -------------------------------------------------------------------------
// Bootstrap
// -------------------------------------------------------------------------

function steveb27_video_uploader_init() {
    return SteveB27_Video_Uploader::get_instance();
}
add_action('plugins_loaded', 'steveb27_video_uploader_init');