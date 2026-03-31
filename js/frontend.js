/**
 * Seamless Video Uploader - Frontend JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Initialize video players
         */
        function initVideoPlayers() {
            $('.sb27-video-container').each(function() {
                const container = $(this);
                const video = container.find('video')[0];
                const fullscreenBtn = container.find('.sb27-fullscreen-btn');

                if (!video) return;

                // Ensure video autoplays (some browsers block this)
                const playPromise = video.play();

                if (playPromise !== undefined) {
                    playPromise.catch(function(error) {
                        // Auto-play was prevented
                        console.log('Autoplay prevented:', error);
                        // Video will still be playable with user interaction
                    });
                }

                // Fullscreen button click handler
                fullscreenBtn.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleFullscreen(container[0]);
                });

                // Add keyboard support for fullscreen
                container.on('keydown', function(e) {
                    if (e.key === 'f' || e.key === 'F') {
                        toggleFullscreen(container[0]);
                    }
                });

                // Update button visibility on fullscreen change
                $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange', function() {
                    updateFullscreenButton(container);
                });

                // Intersection Observer for performance (pause when not visible)
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                video.play();
                            } else {
                                video.pause();
                            }
                        });
                    }, {
                        threshold: 0.5
                    });

                    observer.observe(video);
                }
            });
        }

        /**
         * Toggle fullscreen mode
         */
        function toggleFullscreen(element) {
            if (!document.fullscreenElement && !document.webkitFullscreenElement &&
                !document.mozFullScreenElement && !document.msFullscreenElement) {
                // Enter fullscreen
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        /**
         * Update fullscreen button appearance
         */
        function updateFullscreenButton(container) {
            const isFullscreen = document.fullscreenElement === container[0] ||
                document.webkitFullscreenElement === container[0] ||
                document.mozFullScreenElement === container[0] ||
                document.msFullscreenElement === container[0];

            const btn = container.find('.sb27-fullscreen-btn');

            if (isFullscreen) {
                btn.attr('aria-label', 'Exit Fullscreen');
                btn.find('svg').html('<path d="M2 8h4v4h2V6H2v2zm10-6h-2v6h6V6h-4V2zM8 16H2v-2h4v-4h2v6zm8 0h-6v-6h2v4h4v2z"/>');
            } else {
                btn.attr('aria-label', 'Enter Fullscreen');
                btn.find('svg').html('<path d="M2 2h6v2H4v4H2V2zm14 0h-6v2h4v4h2V2zM2 18h6v-2H4v-4H2v6zm16 0h-6v-2h4v-4h2v6z"/>');
            }
        }

        /**
         * Handle WooCommerce product gallery
         */
        function initWooCommerceGallery() {
            if (typeof wc_single_product_params === 'undefined') {
                return;
            }

            // Handle video in product gallery
            $('.woocommerce-product-gallery').on('click', '.sb27-video-container', function(e) {
                // Prevent opening in lightbox/photoswipe
                if (!$(e.target).is('video, .sb27-fullscreen-btn, .sb27-fullscreen-btn *')) {
                    const video = $(this).find('video')[0];
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                    }
                }
            });

            // Pause videos when lightbox opens on other images
            $('.woocommerce-product-gallery').on('click', 'a:not(.sb27-video-container a)', function() {
                $('.woocommerce-product-gallery video').each(function() {
                    this.pause();
                });
            });
        }

        /**
         * Handle video muting toggle
         */
        $(document).on('click', '.sb27-video-container video', function(e) {
            const video = this;

            // Toggle mute on video click (not fullscreen button)
            if (!$(e.target).closest('.sb27-fullscreen-btn').length) {
                video.muted = !video.muted;

                // Show mute indicator
                showMuteIndicator($(video).parent(), video.muted);
            }
        });

        /**
         * Show mute/unmute indicator
         */
        function showMuteIndicator(container, isMuted) {
            const indicator = $('<div class="sb27-mute-indicator">' +
                (isMuted ? '🔇' : '🔊') + '</div>');

            indicator.css({
                position: 'absolute',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                background: 'rgba(0, 0, 0, 0.7)',
                color: '#fff',
                padding: '15px 20px',
                borderRadius: '8px',
                fontSize: '24px',
                zIndex: '100',
                pointerEvents: 'none'
            });

            container.append(indicator);

            setTimeout(function() {
                indicator.fadeOut(300, function() {
                    indicator.remove();
                });
            }, 1000);
        }

        // Initialize on page load
        initVideoPlayers();
        initWooCommerceGallery();

        // Re-initialize for dynamically added content
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).find('.sb27-video-container').length || $(e.target).hasClass('sb27-video-container')) {
                setTimeout(initVideoPlayers, 100);
            }
        });

        // Handle AJAX loaded content (WooCommerce, etc.)
        $(document.body).on('updated_wc_div', function() {
            setTimeout(initVideoPlayers, 100);
        });
    });

})(jQuery);