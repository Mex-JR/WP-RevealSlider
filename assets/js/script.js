/**
 * Reveal Slider Plugin JavaScript
 *
 * This file contains all the JavaScript functionality
 * for the interactive reveal slider components.
 */

jQuery(document).ready(function($) {

    // Initialize all reveal sliders on the page
    $('.reveal-slider-wrapper').each(function() {
        initRevealSlider($(this));
    });

    function initRevealSlider($slider) {
        var $container = $slider.find('.reveal-slider-container');
        var $after = $slider.find('.reveal-slider-after');
        var $handle = $slider.find('.reveal-slider-handle');
        var $handleButton = $slider.find('.reveal-slider-handle-button');

        var orientation = $slider.data('orientation') || 'horizontal';
        var initialPosition = $slider.data('initial-position') || 50;
    var controlType = $slider.data('control-type') || 'arrows';

        var isDragging = false;
        var containerRect = null;

        // Set initial position
        updateSliderPosition(initialPosition);

        // Make the whole visible handle interactive and keyboard-focusable
        $handle.attr('tabindex', 0).attr('role', 'slider');

        // Mouse events (only enable dragging when controlType allows)
        if ( controlType === 'arrows' || controlType === 'line' ) {
            // bind to the handle (full line) so the entire line is draggable
            $handle.on('mousedown', startDrag);
            $(document).on('mousemove', drag);
            $(document).on('mouseup', stopDrag);
        }

        // Touch events (only for draggable control types)
        if ( controlType === 'arrows' || controlType === 'line' ) {
            $handle.on('touchstart', function(e) {
                e.preventDefault();
                startDrag(e.originalEvent.touches[0]);
            });

            $(document).on('touchmove', function(e) {
                if (isDragging) {
                    e.preventDefault();
                    drag(e.originalEvent.touches[0]);
                }
            });

            $(document).on('touchend', stopDrag);
        }

        // Click on slider to move handle (only if draggable)
        if ( controlType === 'arrows' || controlType === 'line' ) {
            $container.on('click', function(e) {
                if (!isDragging) {
                    containerRect = $container[0].getBoundingClientRect();
                    var position;

                    if (orientation === 'horizontal') {
                        position = ((e.clientX - containerRect.left) / containerRect.width) * 100;
                    } else {
                        position = ((e.clientY - containerRect.top) / containerRect.height) * 100;
                    }

                    position = Math.max(0, Math.min(100, position));
                    updateSliderPosition(position);
                }
            });
        }

        // Hover behavior: for controlType 'hover' move line when mouse moves over container
        if ( controlType === 'hover' ) {
            // Initially apply blur to the after image for a comparison effect
            $after.addClass('reveal-slider-hover-blurred');

            var hoverRestoreTimeout = null;

            $container.on('mousemove', function(e) {
                // Remove blur immediately when user moves
                $after.removeClass('reveal-slider-hover-blurred');
                if ( hoverRestoreTimeout ) {
                    clearTimeout( hoverRestoreTimeout );
                }

                containerRect = $container[0].getBoundingClientRect();
                var position;
                if (orientation === 'horizontal') {
                    position = ((e.clientX - containerRect.left) / containerRect.width) * 100;
                } else {
                    position = ((e.clientY - containerRect.top) / containerRect.height) * 100;
                }
                position = Math.max(0, Math.min(100, position));
                updateSliderPosition(position);

                // Restore blur after 700ms of no movement
                hoverRestoreTimeout = setTimeout(function() {
                    $after.addClass('reveal-slider-hover-blurred');
                }, 700);
            });

            // For touch devices, use touchmove on container (remove blur while moving)
            $container.on('touchstart touchmove', function(e) {
                $after.removeClass('reveal-slider-hover-blurred');
                if ( hoverRestoreTimeout ) {
                    clearTimeout( hoverRestoreTimeout );
                }
                var touch = e.originalEvent.touches[0];
                containerRect = $container[0].getBoundingClientRect();
                var position;
                if (orientation === 'horizontal') {
                    position = ((touch.clientX - containerRect.left) / containerRect.width) * 100;
                } else {
                    position = ((touch.clientY - containerRect.top) / containerRect.height) * 100;
                }
                position = Math.max(0, Math.min(100, position));
                updateSliderPosition(position);
            });

            // Restore blur when pointer leaves or touch ends
            $container.on('mouseleave touchend touchcancel', function() {
                if ( hoverRestoreTimeout ) {
                    clearTimeout( hoverRestoreTimeout );
                }
                // small delay to allow last move to settle
                setTimeout(function() {
                    $after.addClass('reveal-slider-hover-blurred');
                }, 150);
            });
        }

        function startDrag(e) {
            isDragging = true;
            containerRect = $container[0].getBoundingClientRect();
            $slider.addClass('reveal-slider-dragging');

            // Prevent text selection
            $('body').css('user-select', 'none');
        }

        function drag(e) {
            if (!isDragging || !containerRect) return;

            var position;

            if (orientation === 'horizontal') {
                position = ((e.clientX - containerRect.left) / containerRect.width) * 100;
            } else {
                position = ((e.clientY - containerRect.top) / containerRect.height) * 100;
            }

            position = Math.max(0, Math.min(100, position));
            updateSliderPosition(position);
        }

        function stopDrag() {
            if (isDragging) {
                isDragging = false;
                containerRect = null;
                $slider.removeClass('reveal-slider-dragging');

                // Re-enable text selection
                $('body').css('user-select', '');
            }
        }

        function updateSliderPosition(position) {
            if (orientation === 'horizontal') {
                $handle.css('left', position + '%');
                $after.css('clip-path', 'inset(0 0 0 ' + position + '%)');
            } else {
                $handle.css('top', position + '%');
                $after.css('clip-path', 'inset(' + position + '% 0 0 0)');
            }
        }

        // Handle window resize
        $(window).on('resize', function() {
            if (isDragging) {
                containerRect = $container[0].getBoundingClientRect();
            }
        });
    }

    // Keyboard accessibility
    $(document).on('keydown', '.reveal-slider-handle-button, .reveal-slider-handle', function(e) {
        var $slider = $(this).closest('.reveal-slider-wrapper');
        var $handle = $(this).closest('.reveal-slider-handle');
        var orientation = $slider.data('orientation') || 'horizontal';
        var step = 2; // 2% per keypress

        var currentPosition;
        if (orientation === 'horizontal') {
            currentPosition = parseFloat($handle.css('left')) / $slider.width() * 100;
        } else {
            currentPosition = parseFloat($handle.css('top')) / $slider.height() * 100;
        }

        var newPosition = currentPosition;

        if (orientation === 'horizontal') {
            if (e.keyCode === 37) { // Left arrow
                newPosition = Math.max(0, currentPosition - step);
                e.preventDefault();
            } else if (e.keyCode === 39) { // Right arrow
                newPosition = Math.min(100, currentPosition + step);
                e.preventDefault();
            }
        } else {
            if (e.keyCode === 38) { // Up arrow
                newPosition = Math.max(0, currentPosition - step);
                e.preventDefault();
            } else if (e.keyCode === 40) { // Down arrow
                newPosition = Math.min(100, currentPosition + step);
                e.preventDefault();
            }
        }

        if (newPosition !== currentPosition) {
            updateSliderPosition(newPosition);
        }

        function updateSliderPosition(position) {
            var $container = $slider.find('.reveal-slider-container');
            var $after = $slider.find('.reveal-slider-after');

            if (orientation === 'horizontal') {
                $handle.css('left', position + '%');
                $after.css('clip-path', 'inset(0 0 0 ' + position + '%)');
            } else {
                $handle.css('top', position + '%');
                $after.css('clip-path', 'inset(' + position + '% 0 0 0)');
            }
        }
    });

    // Add focus styles and accessibility
    $('.reveal-slider-handle-button').attr('tabindex', '0').attr('role', 'slider');

    console.log('Flowfunnel Reveal Slider plugin loaded and initialized');
});
