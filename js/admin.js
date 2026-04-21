/**
 * SteveB27 Video Uploader - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Enhance media uploader for videos
         */
        if (typeof wp !== 'undefined' && wp.media) {

            // Store original media editor
            const originalEditor = wp.media.editor;

            // Override media editor send attachment
            wp.media.editor.send = {
                attachment: function(props, attachment) {

                    // Check if it's a video
                    if (attachment.type === 'video') {

                        // Create custom video HTML
                        const videoHtml = createVideoHtml(attachment);

                        // Insert into editor
                        wp.media.editor.insert(videoHtml);

                        return false;
                    }

                    // For non-videos, use default behavior
                    return originalEditor.send.attachment.apply(this, arguments);
                }
            };

            /**
             * Create video HTML for insertion
             */
            function createVideoHtml(attachment) {
                const url = attachment.url;
                const id = attachment.id;
                const poster = attachment.image && attachment.image.src ? attachment.image.src : '';
                const posterAttr = poster ? `poster="${poster}"` : '';

                return `<div class="steveb27-video-container" data-video-id="${id}">
    <video ${posterAttr} controls muted autoplay loop playsinline controlslist="nodownload">
        <source src="${url}" type="${attachment.mime}">
        Your browser does not support the video tag.
    </video>
    <button class="steveb27-fullscreen-btn" aria-label="Toggle Fullscreen">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2 2h6v2H4v4H2V2zm14 0h-6v2h4v4h2V2zM2 18h6v-2H4v-4H2v6zm16 0h-6v-2h4v-4h2v6z"/>
        </svg>
    </button>
</div>`;
            }

            /**
             * Add video icon/indicator in media library
             */
            wp.media.view.Attachment.Library = wp.media.view.Attachment.Library.extend({
                render: function() {
                    wp.media.view.Attachment.prototype.render.apply(this, arguments);

                    if (this.model.get('type') === 'video') {
                        this.$el.addClass('steveb27-video-item');

                        // Add play icon overlay
                        if (!this.$el.find('.steveb27-play-icon').length) {
                            this.$el.find('.thumbnail').append(
                                '<span class="steveb27-play-icon">▶</span>'
                            );
                        }
                    }

                    return this;
                }
            });

            // Add CSS for video items in media library
            $('<style>')
                .text(`
                    .steveb27-video-item {
                        border: 2px solid #0073aa;
                    }
                    .steveb27-play-icon {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        font-size: 48px;
                        color: white;
                        text-shadow: 0 0 10px rgba(0,0,0,0.8);
                        pointer-events: none;
                        z-index: 10;
                    }
                    .attachment.steveb27-video-item .thumbnail:hover .steveb27-play-icon {
                        color: #0073aa;
                    }
                `)
                .appendTo('head');
        }

        /**
         * Handle WooCommerce product gallery
         */
        if ($('#woocommerce-product-images').length) {

            // Allow videos in product gallery
            $('#woocommerce-product-images').on('click', '.add_product_images', function() {

                // Wait for media modal to open
                setTimeout(function() {

                    // Add filter to show videos
                    if (wp.media && wp.media.frame) {
                        const frame = wp.media.frame;

                        if (frame.content && frame.content.get()) {
                            const library = frame.content.get().collection;

                            if (library && library.props) {
                                // Include videos in the query
                                const originalType = library.props.get('type');

                                if (originalType === 'image') {
                                    library.props.set('type', ['image', 'video']);
                                }
                            }
                        }
                    }
                }, 100);
            });
        }

        /**
         * Add helpful notice about video support
         */
        if ($('.upload-php').length || $('.post-type-product').length) {
            const notice = $('<div class="notice notice-info is-dismissible">')
                .html('<p><strong>SteveB27 Video Uploader:</strong> You can now upload videos (MP4, WebM, etc.) and they will automatically play muted with fullscreen controls when inserted.</p>')
                .hide()
                .fadeIn();

            $('.wrap h1').first().after(notice);

            // Make dismissible
            notice.on('click', '.notice-dismiss', function() {
                notice.fadeOut();
            });

            // Auto-hide after 10 seconds
            setTimeout(function() {
                notice.fadeOut();
            }, 10000);
        }

        /**
         * Video preview in media modal
         */
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).hasClass('attachment-details') || $(e.target).find('.attachment-details').length) {

                const detailsView = $(e.target).hasClass('attachment-details') ? $(e.target) : $(e.target).find('.attachment-details');

                // Check if it's a video
                const thumbnail = detailsView.find('.thumbnail');
                const videoElement = thumbnail.find('video');

                if (videoElement.length) {
                    // Add our custom controls to preview
                    videoElement.attr({
                        'controls': 'controls',
                        'muted': 'muted',
                        'autoplay': 'autoplay',
                        'loop': 'loop'
                    });

                    // Try to play
                    videoElement[0].play().catch(function(error) {
                        console.log('Preview autoplay prevented:', error);
                    });
                }
            }
        });
    });

})(jQuery);