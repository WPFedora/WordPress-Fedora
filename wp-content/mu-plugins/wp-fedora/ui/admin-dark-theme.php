<?php
/*
Plugin Name: WP Fedora Darkmode UI
Description: Provides a modern dark mode for the WordPress admin interface, disabling light mode when active.
Version: 1.2
Author: WP Fedora
*/

// Enqueue dark mode CSS for the admin area with cache-busting
function wp_fedora_enqueue_darkmode_ui() {
    // Cache-busting: Use file modification time as the version number to force reloads when the file changes
    $version = filemtime(plugin_dir_path(__FILE__) . 'admin-dark-theme.css');
    
    // Enqueue dark theme CSS with the version as the file modification time
    wp_enqueue_style( 'wp-fedora-dark-theme', plugin_dir_url(__FILE__) . 'admin-dark-theme.css', array(), $version, 'all' );
}
add_action( 'admin_enqueue_scripts', 'wp_fedora_enqueue_darkmode_ui', 999 ); // High priority to ensure dark theme loads last

// Dequeue the light theme CSS to disable it when dark mode is active
function wp_fedora_dequeue_light_theme() {
    // Dequeue the light theme CSS from the WP Fedora core plugin
    wp_dequeue_style( 'wp-fedora-light-theme' );
}
add_action( 'admin_enqueue_scripts', 'wp_fedora_dequeue_light_theme', 1000 ); // Ensure this runs after the light theme has been enqueued
