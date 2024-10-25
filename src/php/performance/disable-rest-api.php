<?php
/*
Plugin Name: WP Fedora - Disable REST API
Description: Disables the WordPress REST API for unauthenticated users to improve security.
Version: 1.1
Author: WP Fedora
*/

// Disable REST API for unauthenticated users
function wp_fedora_disable_rest_api( $access ) {
    // Allow REST API access only for logged-in users
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_not_logged_in', __( 'You must be logged in to access the REST API.', 'wp-fedora' ), array( 'status' => 401 ) );
    }
    return $access;
}
add_filter( 'rest_authentication_errors', 'wp_fedora_disable_rest_api' );
