<?php
/*
Plugin Name: WP Fedora Light Theme
Description: Provides a modern light mode for the WordPress admin interface.
Version: 1.0
Author: WP Fedora
*/

function wp_fedora_enqueue_light_theme() {
    // Define the path to the correct admin-light-theme.css file
    $file_path = plugin_dir_path(__FILE__) . 'admin-light-theme.css';
    
    // Check if the file exists before calling filemtime
    if ( file_exists( $file_path ) ) {
        $version = filemtime( $file_path );  // Cache-busting version number based on file modification time
    } else {
        $version = '1.0';  // Fallback version if the file doesn't exist
    }
    
    // Enqueue the light theme CSS with the correct file name and version
    wp_enqueue_style( 'wp-fedora-light-theme', plugin_dir_url(__FILE__) . 'admin-light-theme.css', array(), $version, 'all' );
}

add_action( 'admin_enqueue_scripts', 'wp_fedora_enqueue_light_theme', 999 );
