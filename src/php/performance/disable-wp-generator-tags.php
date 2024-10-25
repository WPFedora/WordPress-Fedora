<?php
/*
Plugin Name: WP Fedora - Disable WordPress Generator Tag
Description: Disables the WordPress generator meta tag from the HTML header to hide the WordPress version.
Version: 1.0
Author: WP Fedora
*/

// Disable the WordPress generator tag (WordPress version number in the meta tag)
function wp_fedora_disable_wp_generator_tag() {
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'wp_fedora_disable_wp_generator_tag');