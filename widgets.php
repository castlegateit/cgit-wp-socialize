<?php

/**
 * Register widget
 */
add_action('widgets_init', function() {
    register_widget('Cgit\Socialize\Widgets\Share');
});
