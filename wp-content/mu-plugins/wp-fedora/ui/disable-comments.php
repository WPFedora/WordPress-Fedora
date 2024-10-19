<?php
/*
Module Name: Disable Comments
Description: This module disables comments across the entire site, removing comment forms and hiding existing comments.
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Disable support for comments and trackbacks in post types
function wp_fedora_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'wp_fedora_disable_comments_post_types_support');

// Close comments on the front-end
function wp_fedora_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'wp_fedora_disable_comments_status', 20, 2);
add_filter('pings_open', 'wp_fedora_disable_comments_status', 20, 2);

// Hide existing comments
function wp_fedora_disable_comments_hide_existing_comments($comments) {
    return array();
}
add_filter('comments_array', 'wp_fedora_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function wp_fedora_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'wp_fedora_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function wp_fedora_disable_comments_admin_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'wp_fedora_disable_comments_admin_redirect');

// Remove comments metabox from dashboard
function wp_fedora_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'wp_fedora_disable_comments_dashboard');

// Remove comments links from admin bar
function wp_fedora_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'wp_fedora_disable_comments_admin_bar');
