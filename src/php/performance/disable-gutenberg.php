<?php
/*
Plugin Name: WP Fedora Disable Gutenberg
Description: Disables Gutenberg editor and enables the Classic Editor for posts, pages, and custom post types. Removes Gutenberg styles from both the admin and the front end.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Disable Gutenberg for posts, pages, and custom post types
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Enqueue the Classic Editor styles
function wp_fedora_enqueue_classic_editor_styles() {
    // Enqueue classic editor CSS for WordPress admin pages
    wp_enqueue_style('classic-editor-styles', includes_url('css/editor.min.css'), array(), null);
}
add_action('admin_enqueue_scripts', 'wp_fedora_enqueue_classic_editor_styles');

// Hide the 'Try Gutenberg' prompt in WordPress Dashboard
remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');

// Remove the Gutenberg-specific styles from front-end
function wp_fedora_remove_gutenberg_styles() {
    wp_dequeue_style('wp-block-library'); // Remove Gutenberg block library CSS
    wp_dequeue_style('wp-block-library-theme'); // Remove Gutenberg theme-specific CSS
    wp_dequeue_style('wc-block-style'); // Remove WooCommerce block styles if using WooCommerce
}
add_action('wp_enqueue_scripts', 'wp_fedora_remove_gutenberg_styles', 100);

// Optional: Prevent loading of Gutenberg's default block styles in the admin
function wp_fedora_disable_gutenberg_admin_styles() {
    wp_dequeue_style('wp-block-editor'); // Remove Gutenberg editor styles in admin
}
add_action('admin_enqueue_scripts', 'wp_fedora_disable_gutenberg_admin_styles', 100);