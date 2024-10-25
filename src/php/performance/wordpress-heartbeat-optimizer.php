<?php
/*
Plugin Name: WP Fedora Heartbeat Optimizer
Description: Optimize the WordPress Heartbeat API frequency.
Version: 1.0
Author: WP Fedora
*/

// Modify the Heartbeat API frequency
function wp_fedora_optimize_heartbeat( $settings ) {
    $frequency = get_option( 'wp_fedora_heartbeat_frequency', 15 ); // Default 15 seconds
    $settings['interval'] = max( intval( $frequency ), 5 ); // Ensure minimum of 5 seconds
    return $settings;
}
add_filter( 'heartbeat_settings', 'wp_fedora_optimize_heartbeat' );
