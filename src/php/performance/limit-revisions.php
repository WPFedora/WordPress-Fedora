<?php
/*
Plugin Name: WP Fedora - Revisions Limit
Description: Sets a limit on the number of revisions stored for posts/pages based on user settings from the WP Fedora Core.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Function to set the revisions limit based on the user option
function wp_fedora_set_revision_limit() {
    $revision_limit = get_option( 'wp_fedora_revision_limit', -1 ); // Default to WordPress unlimited revisions (-1)
    
    if ( $revision_limit == 0 ) {
        // Disable revisions entirely
        add_filter( 'wp_revisions_to_keep', '__return_zero' );
    } elseif ( $revision_limit != -1 ) {
        add_filter( 'wp_revisions_to_keep', function( $num, $post ) use ( $revision_limit ) {
            return $revision_limit;
        }, 10, 2 );
    }
}
add_action( 'init', 'wp_fedora_set_revision_limit' );
