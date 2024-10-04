<?php
/*
Plugin Name: WP Fedora - Admin Menu Organizer
Description: Reorganizes the WordPress admin menu to group content and utilities for better access.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Reorganize the WordPress admin menu
function wp_fedora_reorganize_admin_menu() {
    // Remove default menu items
    remove_menu_page('edit.php');           // Posts
    remove_menu_page('upload.php');         // Media
    remove_menu_page('edit.php?post_type=page');  // Pages
    remove_menu_page('edit-comments.php');  // Comments
    remove_menu_page('plugins.php');        // Plugins
    remove_menu_page('tools.php');          // Tools
    remove_menu_page('options-general.php'); // Settings

    // Add custom "Content" menu
    add_menu_page(
        'Content',           // Page title
        'Content',           // Menu title
        'manage_options',    // Capability
        'wp_fedora_content', // Menu slug (doesn't have to be a real page)
        '',                  // Callback function (empty because it's just a parent menu)
        'dashicons-admin-post', // Icon (WordPress dashicon for content)
        6                    // Position
    );

    // Add submenus under "Content"
    add_submenu_page('wp_fedora_content', 'Pages', 'Pages', 'edit_pages', 'edit.php?post_type=page');
    add_submenu_page('wp_fedora_content', 'Posts', 'Posts', 'edit_posts', 'edit.php');
    add_submenu_page('wp_fedora_content', 'Comments', 'Comments', 'edit_posts', 'edit-comments.php');
    add_submenu_page('wp_fedora_content', 'Media', 'Media', 'upload_files', 'upload.php');

    // Add custom "Utilities" menu
    add_menu_page(
        'Utilities',         // Page title
        'Utilities',         // Menu title
        'manage_options',    // Capability
        'wp_fedora_utilities', // Menu slug
        '',                  // Callback function (empty because it's just a parent menu)
        'dashicons-admin-tools', // Icon (WordPress dashicon for utilities)
        80                   // Position (after Appearance)
    );

    // Add submenus under "Utilities"
    add_submenu_page('wp_fedora_utilities', 'Plugins', 'Plugins', 'activate_plugins', 'plugins.php');
    add_submenu_page('wp_fedora_utilities', 'Tools', 'Tools', 'edit_posts', 'tools.php');
    add_submenu_page('wp_fedora_utilities', 'Settings', 'Settings', 'manage_options', 'options-general.php');
}

add_action('admin_menu', 'wp_fedora_reorganize_admin_menu');
