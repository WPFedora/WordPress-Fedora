<?php
/*
Plugin Name: WP Fedora - Autosave Interval
Description: Sets a custom autosave interval for posts/pages based on user settings from the WP Fedora Core.
Version: 1.1
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Function to set the autosave interval based on the user option
function wp_fedora_set_autosave_interval() {
    $autosave_interval = get_option( 'wp_fedora_autosave_interval', 60 ); // Default to 60 seconds
    
    if ( $autosave_interval == 0 ) {
        // Disable autosave by deregistering the autosave script
        add_action( 'admin_enqueue_scripts', 'wp_fedora_disable_autosave' );
    } elseif ( $autosave_interval >= 10 && $autosave_interval <= 300 ) { // Ensure interval is between 10 and 300 seconds
        add_filter( 'autosave_interval', function() use ( $autosave_interval ) {
            return $autosave_interval;
        });
    }
}

// Function to deregister autosave script when interval is set to 0
function wp_fedora_disable_autosave() {
    wp_deregister_script( 'autosave' );
}

add_action( 'init', 'wp_fedora_set_autosave_interval' );
