<?php

/*
Plugin Name:  wp-static-options
Plugin URI:   https://github.com/notus-sh/wp-static-options
Description:  Set WordPress and plugins options through configuration files instead of the wp_options table.
Version:      1.0.0
Author:       notus.sh
Author URI:   https://notus.sh/
License:      Apache License 2.0
*/

// Default value for STATIC_OPTIONS_DIR
defined('STATIC_OPTIONS_DIR') || define('STATIC_OPTIONS_DIR', realpath(WP_CONTENT_DIR . '/config/'));

add_action('muplugins_loaded', function () {
    WPStaticOptions\setup(STATIC_OPTIONS_DIR);
});
