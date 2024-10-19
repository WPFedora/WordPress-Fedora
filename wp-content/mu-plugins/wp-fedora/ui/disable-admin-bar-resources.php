<?php
/*
Plugin Name: WP Fedora - Disable Admin Bar Links
Description: Removes the WordPress admin bar links like About WordPress, Documentation, and others.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Remove admin bar links
function wp_fedora_remove_admin_bar_links() {
    global $wp_admin_bar;

    // Remove the WordPress logo and its submenus
    $wp_admin_bar->remove_menu('wp-logo');         // WordPress logo menu
    $wp_admin_bar->remove_menu('about');           // About WordPress
    $wp_admin_bar->remove_menu('wporg');           // WordPress.org
    $wp_admin_bar->remove_menu('documentation');   // Documentation
    $wp_admin_bar->remove_menu('support-forums');  // Support
    $wp_admin_bar->remove_menu('feedback');        // Feedback
}

add_action('wp_before_admin_bar_render', 'wp_fedora_remove_admin_bar_links');
