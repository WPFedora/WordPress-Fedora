<?php
/*
Module Name: Disable Dashicons on Frontend
Description: This module disables Dashicons from loading on the front end for non-logged-in users.
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function wp_fedora_disable_dashicons_for_non_logged_in_users() {
    if ( !is_user_logged_in() ) { // Only dequeue Dashicons if the user is not logged in
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}
add_action('wp_enqueue_scripts', 'wp_fedora_disable_dashicons_for_non_logged_in_users', 100);


