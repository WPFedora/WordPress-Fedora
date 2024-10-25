<?php
/*
Plugin Name: WP Fedora - Remove Dashboard Widgets
Description: Removes all default WordPress dashboard widgets for a cleaner dashboard.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Remove default WordPress dashboard widgets
function wp_fedora_remove_default_dashboard_widgets() {
    // Global WordPress dashboard
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');    // Quick Draft
    remove_meta_box('dashboard_primary', 'dashboard', 'side');        // WordPress Events and News
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');      // Secondary (sometimes used by plugins)
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // Incoming Links (deprecated)
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');      // Plugins
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');    // At a Glance (Right Now)
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');     // Activity
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal'); // Recent Drafts
}
add_action('wp_dashboard_setup', 'wp_fedora_remove_default_dashboard_widgets');
