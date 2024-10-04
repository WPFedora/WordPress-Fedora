<?php
/*
Plugin Name: WP Fedora - Admin Menu Organizer
Description: Reorganizes the WordPress admin menu to group content and utilities for better access.
Version: 1.3
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
    remove_menu_page('users.php');          // Users
    remove_menu_page('themes.php');         // Appearance
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
    add_submenu_page('wp_fedora_content', 'All Posts', 'All Posts', 'edit_posts', 'edit.php'); // All Posts
    add_submenu_page('wp_fedora_content', 'Add New Post', 'Add New Post', 'edit_posts', 'post-new.php'); // Add New Post
    add_submenu_page('wp_fedora_content', 'Categories', 'Categories', 'manage_categories', 'edit-tags.php?taxonomy=category'); // Categories
    add_submenu_page('wp_fedora_content', 'Tags', 'Tags', 'manage_categories', 'edit-tags.php?taxonomy=post_tag'); // Tags

    add_submenu_page('wp_fedora_content', 'All Pages', 'All Pages', 'edit_pages', 'edit.php?post_type=page'); // All Pages
    add_submenu_page('wp_fedora_content', 'Add New Page', 'Add New Page', 'edit_pages', 'post-new.php?post_type=page'); // Add New Page
    
    add_submenu_page('wp_fedora_content', 'Library', 'Library', 'upload_files', 'upload.php'); // Media Library
    add_submenu_page('wp_fedora_content', 'Add New Media', 'Add New Media', 'upload_files', 'media-new.php'); // Add New Media
    
    add_submenu_page('wp_fedora_content', 'Comments', 'Comments', 'edit_posts', 'edit-comments.php'); // Comments

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
    add_submenu_page('wp_fedora_utilities', 'Installed Plugins', 'Installed Plugins', 'activate_plugins', 'plugins.php'); // Installed Plugins
    add_submenu_page('wp_fedora_utilities', 'Add New Plugin', 'Add New Plugin', 'install_plugins', 'plugin-install.php'); // Add New Plugin
    add_submenu_page('wp_fedora_utilities', 'Plugin Editor', 'Plugin Editor', 'edit_plugins', 'plugin-editor.php'); // Plugin Editor

    add_submenu_page('wp_fedora_utilities', 'All Users', 'All Users', 'list_users', 'users.php'); // All Users
    add_submenu_page('wp_fedora_utilities', 'Add New User', 'Add New User', 'create_users', 'user-new.php'); // Add New User
    add_submenu_page('wp_fedora_utilities', 'Profile', 'Profile', 'read', 'profile.php'); // Profile

    add_submenu_page('wp_fedora_utilities', 'Tools', 'Tools', 'edit_posts', 'tools.php'); // Tools
    add_submenu_page('wp_fedora_utilities', 'Import', 'Import', 'import', 'import.php'); // Import
    add_submenu_page('wp_fedora_utilities', 'Export', 'Export', 'export', 'export.php'); // Export
    add_submenu_page('wp_fedora_utilities', 'Site Health', 'Site Health', 'manage_options', 'site-health.php'); // Site Health
    add_submenu_page('wp_fedora_utilities', 'Export Personal Data', 'Export Personal Data', 'manage_options', 'export-personal-data.php'); // Export Personal Data
    add_submenu_page('wp_fedora_utilities', 'Erase Personal Data', 'Erase Personal Data', 'manage_options', 'erase-personal-data.php'); // Erase Personal Data

    add_submenu_page('wp_fedora_utilities', 'Settings', 'Settings', 'manage_options', 'options-general.php'); // Settings
    add_submenu_page('wp_fedora_utilities', 'Writing', 'Writing', 'manage_options', 'options-writing.php'); // Writing Settings
    add_submenu_page('wp_fedora_utilities', 'Reading', 'Reading', 'manage_options', 'options-reading.php'); // Reading Settings
    add_submenu_page('wp_fedora_utilities', 'Discussion', 'Discussion', 'manage_options', 'options-discussion.php'); // Discussion Settings
    add_submenu_page('wp_fedora_utilities', 'Media Settings', 'Media Settings', 'manage_options', 'options-media.php'); // Media Settings
    add_submenu_page('wp_fedora_utilities', 'Permalinks', 'Permalinks', 'manage_options', 'options-permalink.php'); // Permalink Settings
    add_submenu_page('wp_fedora_utilities', 'Privacy', 'Privacy', 'manage_options', 'options-privacy.php'); // Privacy Settings

    // Add custom "Appearance" menu (relocate Appearance submenus)
    add_menu_page(
        'Appearance',         // Page title
        'Appearance',         // Menu title
        'manage_options',     // Capability
        'wp_fedora_appearance', // Menu slug
        '',                   // Callback function (empty because it's just a parent menu)
        'dashicons-admin-appearance', // Icon (WordPress dashicon for appearance)
        60                    // Position
    );

    add_submenu_page('wp_fedora_appearance', 'Themes', 'Themes', 'switch_themes', 'themes.php'); // Themes
    add_submenu_page('wp_fedora_appearance', 'Customize', 'Customize', 'customize', 'customize.php'); // Customize
    add_submenu_page('wp_fedora_appearance', 'Editor', 'Editor', 'edit_theme_options', 'theme-editor.php'); // Theme Editor
}

add_action('admin_menu', 'wp_fedora_reorganize_admin_menu');
