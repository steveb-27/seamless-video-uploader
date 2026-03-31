<?php
/**
 * Plugin Name: Seamless Video Uploader
 * Plugin URI: https://steveb27.com
 * Description: Enables video uploads through the media library and automatically inserts them with autoplay, muted, and fullscreen controls. Works with pages, posts, and WooCommerce product galleries.
 * Version: 1.0.1
 * Author: Steve B-27
 * Author URI: https://steveb27.com
 * License: GPL v2 or later
 * Text Domain: seamless-video-uploader
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Video_Autoplay_Enhancer {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Add video MIME types to allowed uploads
        add_filter('upload_mimes', array($this, 'add_video_mime_types'));
        
        // Modify video insertion in editor
        add_filter('media_send_to_editor', array($this, 'insert_video_html'), 10, 3);
        
        // Add custom video player HTML for galleries and media
        add_filter('wp_get_attachment_image', array($this, 'replace_video_thumbnail'), 10, 5);
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Enqueue frontend scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        
        // WooCommerce integration
        add_filter('woocommerce_single_product_image_thumbnail_html', array($this, 'woocommerce_video_thumbnail'), 10, 2);
        
        // Handle video attachments in content
        add_filter('the_content', array($this, 'enhance_video_content'), 20);
    }
    
    /**
     * Add video MIME types to WordPress upload
     */
    public function add_video_mime_types($mimes) {
        $mimes['mp4'] = 'video/mp4';
        $mimes['m4v'] = 'video/x-m4v';
        $mimes['mov'] = 'video/quicktime';
        $mimes['wmv'] = 'video/x-ms-wmv';
        $mimes['avi'] = 'video/avi';
        $mimes['mpg'] = 'video/mpeg';
        $mimes['mpeg'] = 'video/mpeg';
        $mimes['ogv'] = 'video/ogg';
        $mimes['webm'] = 'video/webm';
        return $mimes;
    }
    
    /**
     * Insert custom video HTML when adding to editor
     */
    public function insert_video_html($html, $id, $attachment) {
        $post = get_post($id);
        
        if (strpos($post->post_mime_type, 'video/') === 0) {
            $url = wp_get_attachment_url($id);
            $html = $this->generate_video_html($url, $id);
        }
        
        return $html;
    }
    
    /**
     * Generate video player HTML
     */
    private function generate_video_html($url, $id = 0, $class = '') {
        $poster = '';
        
        // Try to get video thumbnail if available
        if ($id) {
            $thumb_id = get_post_thumbnail_id($id);
            if ($thumb_id) {
                $poster = wp_get_attachment_url($thumb_id);
            }
        }
        
        $poster_attr = $poster ? 'poster="' . esc_url($poster) . '"' : '';
        $class_attr = $class ? 'class="' . esc_attr($class) . '"' : '';
        
        $html = sprintf(
            '<div class="sb27-video-container" data-video-id="%d">
                <video %s %s controls muted autoplay loop playsinline controlslist="nodownload">
                    <source src="%s" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <button class="sb27-fullscreen-btn" aria-label="Toggle Fullscreen">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 2h6v2H4v4H2V2zm14 0h-6v2h4v4h2V2zM2 18h6v-2H4v-4H2v6zm16 0h-6v-2h4v-4h2v6z"/>
                    </svg>
                </button>
            </div>',
            intval($id),
            $class_attr,
            $poster_attr,
            esc_url($url)
        );
        
        return $html;
    }
    
    /**
     * Replace video thumbnails with actual video players
     */
    public function replace_video_thumbnail($html, $attachment_id, $size, $icon, $attr) {
        $post = get_post($attachment_id);
        
        if ($post && strpos($post->post_mime_type, 'video/') === 0) {
            $url = wp_get_attachment_url($attachment_id);
            $class = isset($attr['class']) ? $attr['class'] : '';
            return $this->generate_video_html($url, $attachment_id, $class);
        }
        
        return $html;
    }
    
    /**
     * WooCommerce product gallery video support
     */
    public function woocommerce_video_thumbnail($html, $attachment_id) {
        $post = get_post($attachment_id);
        
        if ($post && strpos($post->post_mime_type, 'video/') === 0) {
            $url = wp_get_attachment_url($attachment_id);
            return $this->generate_video_html($url, $attachment_id, 'woocommerce-product-gallery__image');
        }
        
        return $html;
    }
    
    /**
     * Enhance video content in posts/pages
     */
    public function enhance_video_content($content) {
        // Look for video attachments and ensure they have proper attributes
        if (has_shortcode($content, 'video')) {
            // WordPress native video shortcode - add our attributes
            $content = preg_replace_callback(
                '/\[video([^\]]*)\]/i',
                function($matches) {
                    $attrs = $matches[1];
                    // Add autoplay, muted, loop if not present
                    if (strpos($attrs, 'autoplay') === false) {
                        $attrs .= ' autoplay="on"';
                    }
                    if (strpos($attrs, 'muted') === false) {
                        $attrs .= ' muted="on"';
                    }
                    if (strpos($attrs, 'loop') === false) {
                        $attrs .= ' loop="on"';
                    }
                    return '[video' . $attrs . ']';
                },
                $content
            );
        }
        
        return $content;
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook || 'upload.php' === $hook) {
            wp_enqueue_script(
                'sb27-admin',
                plugin_dir_url(__FILE__) . 'js/admin.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'sb27-frontend',
            plugin_dir_url(__FILE__) . 'css/frontend.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'sb27-frontend',
            plugin_dir_url(__FILE__) . 'js/frontend.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}

// Initialize the plugin
function video_autoplay_enhancer_init() {
    return Video_Autoplay_Enhancer::get_instance();
}
add_action('plugins_loaded', 'video_autoplay_enhancer_init');
