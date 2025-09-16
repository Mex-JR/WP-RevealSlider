/**
 * Reveal Slider Admin JavaScript
 */

jQuery(document).ready(function($) {

    // Quick sanity log to ensure admin script is loaded
    if ( window.console && console.log ) {
        console.log('reveal-slider admin.js loaded');
    }

    // Media uploader functionality (delegated to support .image-uploader)
    $(document).on('click', '.image-uploader, .image-uploader .image-preview', function (e) {
        e.preventDefault();

        var container = $(this);
        // if user clicked inner element (.image-preview), find closest uploader
        if ( ! container.hasClass('image-uploader') ) {
            container = container.closest('.image-uploader');
        }
        var target = container.data('target');
        var targetInput = $('#' + target);
        var previewContainer = $('#' + target + '_preview');

        // Create a new media frame
        if ( typeof wp === 'undefined' || typeof wp.media === 'undefined' ) {
            if ( window.console && console.error ) {
                console.error('wp.media is not available. Make sure wp_enqueue_media() was called.');
            }
            return;
        }

        var mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        // Ensure the media frame opens on the 'Media Library' (browse) state when possible
        mediaUploader.on('open', function () {
            try {
                // Preferred: set the state to 'library' if supported
                if ( typeof mediaUploader.setState === 'function' ) {
                    mediaUploader.setState('library');
                }
                // Fallback: set content mode to 'browse' which switches to the library view
                if ( mediaUploader.content && typeof mediaUploader.content.mode === 'function' ) {
                    mediaUploader.content.mode('browse');
                }
                // Extra fallback: programmatically click the router tab with data-mode="browse"
                try {
                    var contentGet = mediaUploader.content && typeof mediaUploader.content.get === 'function' ? mediaUploader.content.get() : null;
                    var $el = contentGet && contentGet.$el ? contentGet.$el : null;
                    if ( $el && $el.find ) {
                        var $router = $el.find('.media-router a[data-mode="browse"]');
                        if ( $router.length ) {
                            setTimeout(function() {
                                $router.trigger('click');
                            }, 50);
                        }
                    }
                } catch (e) {
                    if ( window.console && console.warn ) {
                        console.warn('Could not programmatically switch media router to browse:', e);
                    }
                }
            } catch (err) {
                if ( window.console && console.warn ) {
                    console.warn('Could not switch media frame to library/browse state:', err);
                }
            }
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            if ( targetInput.length ) {
                targetInput.val(attachment.url);
            }
            if ( previewContainer.length ) {
                previewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
            }
        });

        mediaUploader.open();
    });

    // Form submission
    $('#reveal-slider-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var originalText = submitButton.text();

        // Add loading state
        submitButton.text('Saving...').prop('disabled', true);
        form.addClass('reveal-slider-loading');

        // Prepare data
        var formData = {
            action: 'save_reveal_slider',
            nonce: revealSliderAjax.nonce,
            slider_id: form.find('input[name="slider_id"]').val(),
            slider_name: form.find('input[name="slider_name"]').val(),
            before_image: form.find('input[name="before_image"]').val(),
            after_image: form.find('input[name="after_image"]').val(),
            before_label: form.find('input[name="before_label"]').val(),
            after_label: form.find('input[name="after_label"]').val(),
            initial_position: form.find('input[name="initial_position"]').val(),
                orientation: form.find('select[name="orientation"]').val(),
                control_type: form.find('select[name="control_type"]').val()
        };

        // Send AJAX request
        $.post(revealSliderAjax.ajaxurl, formData)
            .done(function(response) {
                if (response.success) {
                    // Show success message and redirect
                    alert(response.data.message);
                    window.location.href = response.data.redirect;
                } else {
                    alert(response.data.message);
                }
            })
            .fail(function() {
                alert('An error occurred while saving the slider.');
            })
            .always(function() {
                // Remove loading state
                submitButton.text(originalText).prop('disabled', false);
                form.removeClass('reveal-slider-loading');
            });
    });

    // Delete slider
    $('.delete-slider').on('click', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this slider?')) {
            return;
        }

        var button = $(this);
        var sliderId = button.data('id');
        var row = button.closest('tr');

        // Add loading state
        button.prop('disabled', true).text('Deleting...');

        // Prepare data
        var formData = {
            action: 'delete_reveal_slider',
            nonce: revealSliderAjax.nonce,
            slider_id: sliderId
        };

        // Send AJAX request
        $.post(revealSliderAjax.ajaxurl, formData)
            .done(function(response) {
                if (response.success) {
                    row.fadeOut(function() {
                        row.remove();
                    });
                } else {
                    alert(response.data.message);
                }
            })
            .fail(function() {
                alert('An error occurred while deleting the slider.');
            })
            .always(function() {
                button.prop('disabled', false).text('Delete');
            });
    });

    // Copy shortcode functionality
    $('.copy-shortcode').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var shortcode = button.data('shortcode');

        // Create a temporary textarea to copy the text
        var tempTextarea = $('<textarea>');
        $('body').append(tempTextarea);
        tempTextarea.val(shortcode).select();
        document.execCommand('copy');
        tempTextarea.remove();

        // Show feedback
        var originalText = button.text();
        button.text('Copied!').css('color', '#46b450');

        setTimeout(function() {
            button.text(originalText).css('color', '');
        }, 2000);
    });
});
