<?php
/*
Plugin Name: WP Fedora - Admin Footer Customizer
Description: Replaces the default WordPress footer message with a custom message.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Customize the footer text in the WordPress admin
function wp_fedora_custom_admin_footer_text() {
    echo '<p><i><strong>WP Fedora</strong> brings your websites and SEO to life!</i> Got ideas to make WP Fedora better? Submit them on <a href="https://github.com/WPFedora/WordPress-Fedora/issues" target="_blank">our Github repo</a>.</p>';
}
add_filter('admin_footer_text', 'wp_fedora_custom_admin_footer_text');

// Optionally, you can also remove or modify the WordPress version info in the footer
function wp_fedora_custom_admin_footer_version() {
    return ''; // You can return a custom message here or leave it blank to remove the version info
}
add_filter('update_footer', 'wp_fedora_custom_admin_footer_version', 11);
