<?php
/*
Plugin Name: WP Fedora Core
Version: 1.12
Description: Core functionalities for WP Fedora, including bulk meta editing, disabling WP generator tag, disabling REST API, Meta Robots Settings, Darkmode UI, Admin Footer Customizer, Admin Bar Resources Disabler, Admin Bar Hover Transition, SVG Upload Support, Heartbeat Optimizer, Revisions Limit, Autosave Interval, Disable Emojis, Dashicons, OEmbed, and other SEO tools.
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants for paths
define( 'WP_FEDORA_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_FEDORA_URL', plugin_dir_url( __FILE__ ) );

// Check if the plugin is being loaded as a must-use plugin or standard plugin
if ( defined( 'WPMU_PLUGIN_DIR' ) && strpos( __FILE__, WPMU_PLUGIN_DIR ) !== false ) {
    // If loaded as an MU plugin
    add_action( 'muplugins_loaded', 'wp_fedora_load_files' );
} else {
    // If loaded as a standard plugin
    add_action( 'plugins_loaded', 'wp_fedora_load_files' );
}

// Hide the plugin from the plugins screen
add_action('pre_current_active_plugins', 'wp_fedora_hide_from_plugins');
function wp_fedora_hide_from_plugins() {
    global $wp_list_table;
    $hide_plugins = array(
        'wp-fedora-core.php' // The name of the main plugin file
    );
    $my_plugins = $wp_list_table->items;
    foreach ($my_plugins as $key => $val) {
        if (in_array($key, $hide_plugins)) {
            unset($wp_list_table->items[$key]);
        }
    }
}

// Set default options on first load
function wp_fedora_set_default_options() {
    if ( get_option( 'wp_fedora_enable_rss' ) === false ) {
        update_option( 'wp_fedora_enable_rss', 0 ); // RSS disabled by default
    }
    if ( get_option( 'wp_fedora_enable_gutenberg' ) === false ) {
        update_option( 'wp_fedora_enable_gutenberg', 0 ); // Gutenberg disabled by default
    }
    if ( get_option( 'wp_fedora_disable_meta_editor' ) === false ) {
        update_option( 'wp_fedora_disable_meta_editor', 0 ); // Meta Editor enabled by default
    }
    if ( get_option( 'wp_fedora_disable_bulk_meta_editor' ) === false ) {
        update_option( 'wp_fedora_disable_bulk_meta_editor', 0 ); // Bulk Meta Editor enabled by default
    }
    if ( get_option( 'wp_fedora_enable_wp_generator_tag' ) === false ) {
        update_option( 'wp_fedora_enable_wp_generator_tag', 0 ); // WP Generator Tag disabled by default
    }
    if ( get_option( 'wp_fedora_enable_rest_api' ) === false ) {
        update_option( 'wp_fedora_enable_rest_api', 0 ); // REST API disabled by default
    }
    if ( get_option( 'wp_fedora_disable_meta_robots' ) === false ) {
        update_option( 'wp_fedora_disable_meta_robots', 0 ); // Meta Robots Settings enabled by default
    }
    if ( get_option( 'wp_fedora_enable_darkmode_ui' ) === false ) {
        update_option( 'wp_fedora_enable_darkmode_ui', 0 ); // Darkmode UI disabled by default
    }
    if ( get_option( 'wp_fedora_disable_light_mode' ) === false ) {
        update_option( 'wp_fedora_disable_light_mode', 0 ); // Light Mode enabled by default
    }
    if ( get_option( 'wp_fedora_enable_dashboard_widgets' ) === false ) {
        update_option( 'wp_fedora_enable_dashboard_widgets', 0 ); // Dashboard Widgets disabled by default
    }
    if ( get_option( 'wp_fedora_disable_admin_footer_customizer' ) === false ) {
        update_option( 'wp_fedora_disable_admin_footer_customizer', 0 ); // Admin Footer Customizer enabled by default
    }
    if ( get_option( 'wp_fedora_enable_admin_bar_resources' ) === false ) {
        update_option( 'wp_fedora_enable_admin_bar_resources', 0 ); // Admin Bar Resources disabled by default
    }
    if ( get_option( 'wp_fedora_disable_admin_bar_transition' ) === false ) {
        update_option( 'wp_fedora_disable_admin_bar_transition', 0 ); // Admin Bar Hover Transition disabled by default
    }
    if ( get_option( 'wp_fedora_disable_svg_upload' ) === false ) {
        update_option( 'wp_fedora_disable_svg_upload', 0 ); // SVG Upload enabled by default
    }
    if ( get_option( 'wp_fedora_disable_dashicons' ) === false ) {
        update_option( 'wp_fedora_disable_dashicons', 0 ); // Dashicons disabled by default on front-end
    }
}
add_action( 'init', 'wp_fedora_set_default_options' );

// Add settings section and fields to General Settings
function wp_fedora_register_settings() {
    // Register settings for Fedora
    register_setting( 'general', 'wp_fedora_enable_rss', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_gutenberg', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_meta_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_bulk_meta_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_wp_generator_tag', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_rest_api', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_meta_robots', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_darkmode_ui', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );

    // Add a new section under General Settings
    add_settings_section(
        'wp_fedora_settings_section', // Section ID
        'Fedora Settings',            // Title
        '__return_false',             // No callback function to display description
        'general'                     // Displayed on General Settings page
    );

    // Add fields for toggling features
    add_settings_field(
        'wp_fedora_enable_rss',          // Field ID
        'Enable RSS Feeds',              // Field Title
        'wp_fedora_toggle_field_callback', // Callback to render the checkbox
        'general',                        // Page to display the section (General Settings)
        'wp_fedora_settings_section',     // Section ID
        ['label_for' => 'wp_fedora_enable_rss'] // Label for accessibility
    );

    add_settings_field(
        'wp_fedora_enable_gutenberg',
        'Enable Gutenberg',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_gutenberg']
    );

    add_settings_field(
        'wp_fedora_disable_meta_editor',
        'Disable Meta Editor',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_meta_editor']
    );

    add_settings_field(
        'wp_fedora_disable_bulk_meta_editor',
        'Disable Bulk Meta Editor',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_bulk_meta_editor']
    );

    add_settings_field(
        'wp_fedora_enable_wp_generator_tag',
        'Enable WP Generator Tag',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_wp_generator_tag']
    );

    add_settings_field(
        'wp_fedora_enable_rest_api',
        'Enable REST API',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_rest_api']
    );

    add_settings_field(
        'wp_fedora_disable_meta_robots',
        'Disable Page/Post Meta Robots',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_meta_robots']
    );

    add_settings_field(
        'wp_fedora_enable_darkmode_ui',
        'Enable Darkmode UI',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_darkmode_ui']
    );
}
add_action( 'admin_init', 'wp_fedora_register_settings' );

// Callback function to render a checkbox (unchecked by default)
function wp_fedora_toggle_field_callback( $args ) {
    $option_name = $args['label_for'];
    $checked = get_option( $option_name ) ? 'checked' : '';
    echo "<input type='checkbox' id='{$option_name}' name='{$option_name}' value='1' {$checked} />";
}

// Load necessary feature and admin files based on user settings
function wp_fedora_load_files() {
    // Enable RSS feeds if checked
    if ( get_option( 'wp_fedora_enable_rss' ) ) {
        // RSS Feeds are enabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-rss-feeds.php';
    }

    // Enable Gutenberg if checked
    if ( get_option( 'wp_fedora_enable_gutenberg' ) ) {
        // Gutenberg is enabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-gutenberg.php';
    }

    // Disable Meta Editor if checked
    if ( get_option( 'wp_fedora_disable_meta_editor' ) ) {
        // Meta Editor is disabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/meta-editor.php';
    }

    // Disable Bulk Meta Editor if checked
    if ( get_option( 'wp_fedora_disable_bulk_meta_editor' ) ) {
        // Bulk Meta Editor is disabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/bulk-meta-editor.php';
    }

    // Enable WP Generator Tag if checked
    if ( get_option( 'wp_fedora_enable_wp_generator_tag' ) ) {
        // WP Generator Tag is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-wp-generator-tags.php'; // Disable WP Generator Tag feature file
    }

    // Enable REST API if checked
    if ( get_option( 'wp_fedora_enable_rest_api' ) ) {
        // REST API is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-rest-api.php'; // Disable REST API feature file
    }

    // Disable Meta Robots Settings if checked
    if ( get_option( 'wp_fedora_disable_meta_robots' ) ) {
        // Do nothing, Meta Robots is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/meta-robots-settings.php'; // Meta Robots Settings feature file
    }

    // Enable Darkmode UI if checked
    if ( get_option( 'wp_fedora_enable_darkmode_ui' ) ) {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/admin-dark-theme.php'; // Darkmode UI feature file
    }
}
add_action( 'muplugins_loaded', 'wp_fedora_load_files' );
