<?php
/*
 * Plugin Name:       WP Fedora Core
 * Plugin URI:        https://wpfedora.com
 * Description:       Core functionalities for WP Fedora, including bulk meta editing, disabling WP generator tag, disabling REST API, Meta Robots Settings, Darkmode UI, Admin Footer Customizer, Admin Bar Resources Disabler, Admin Bar Hover Transition, SVG Upload Support, Heartbeat Optimizer, Revisions Limit, Autosave Interval, Disable Emojis, Dashicons, OEmbed, and other SEO tools.
 * Version:           1.3.1
 * Author:            WP Fedora
 * Author URI:        https://wpfedora.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-fedora-core
 * Requires PHP:      7.2
 * Requires at least: 5.0
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
    if ( get_option( 'wp_fedora_enable_comments' ) === false ) {
    update_option( 'wp_fedora_enable_comments', 0 ); // Comments disabled by default
    }
    if ( get_option( 'wp_fedora_move_plugin_editor_default' ) === false ) {
    update_option( 'wp_fedora_move_plugin_editor_default', 0 ); // Move Plugin Editor by default
    }
    if ( get_option( 'wp_fedora_move_theme_editor_default' ) === false ) {
    update_option( 'wp_fedora_move_theme_editor_default', 0 ); // Move Theme Editor by default
    }
    if ( get_option( 'wp_fedora_disable_cpt' ) === false ) {
        update_option( 'wp_fedora_disable_cpt', 0 ); // Custom Post Types enabled by default (not disabled)
    }
        if ( get_option( 'wp_fedora_disable_robots_editor' ) === false ) {
        update_option( 'wp_fedora_disable_robots_editor', 0 ); // Robots.txt Editor enabled by default
    }
    
    if (get_option('disable_404_monitor_log_') === false) {
        update_option('disable_404_monitor_log_', 0);  // 404 Monitor enabled by default
    }
    
    // Ensure the sitemap generator is enabled by default
    if ( get_option( 'wp_fedora_disable_sitemap_generator' ) === false ) {
        update_option( 'wp_fedora_disable_sitemap_generator', 0 ); // Sitemap enabled by default
    }
    
    if (get_option('wp_fedora_disable_htaccess_editor') === false) {
    update_option('wp_fedora_disable_htaccess_editor', 0); // .htaccess Editor enabled by default
    }
    
    if ( get_option( 'wp_fedora_disable_script_manager' ) === false ) {
    update_option( 'wp_fedora_disable_script_manager', 0 ); // Script Manager enabled by default
    }

    if ( get_option( 'wp_fedora_heartbeat_frequency' ) === false ) {
        update_option( 'wp_fedora_heartbeat_frequency', 15 ); // Heartbeat API frequency default to 15 seconds
    }
    if ( get_option( 'wp_fedora_revision_limit' ) === false ) {
        update_option( 'wp_fedora_revision_limit', -1 ); // Default WordPress behavior (-1, unlimited)
    }
    if ( get_option( 'wp_fedora_autosave_interval' ) === false ) {
        update_option( 'wp_fedora_autosave_interval', 60 ); // Default autosave interval in seconds
    }

}
add_action( 'init', 'wp_fedora_set_default_options' );

// Add settings section and fields to General Settings
function wp_fedora_register_settings() {
    register_setting( 'general', 'wp_fedora_enable_rss', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_gutenberg', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_meta_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_bulk_meta_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_wp_generator_tag', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_rest_api', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_meta_robots', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_darkmode_ui', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_light_mode', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_dashboard_widgets', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_admin_footer_customizer', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_admin_bar_resources', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_admin_bar_transition', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_svg_upload', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_heartbeat_frequency', ['type' => 'integer', 'sanitize_callback' => 'absint'] );
    register_setting( 'general', 'wp_fedora_revision_limit', ['type' => 'integer', 'sanitize_callback' => 'absint'] );
    register_setting( 'general', 'wp_fedora_autosave_interval', ['type' => 'integer', 'sanitize_callback' => 'absint'] );
    register_setting( 'general', 'wp_fedora_disable_dashicons', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_enable_comments', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_move_plugin_editor_default', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_move_theme_editor_default', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_cpt', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting( 'general', 'wp_fedora_disable_robots_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting('general', 'disable_404_monitor_log_', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting( 'general', 'wp_fedora_disable_sitemap_generator', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );
    register_setting('general', 'wp_fedora_disable_htaccess_editor', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']);
    register_setting( 'general', 'wp_fedora_disable_script_manager', ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'] );


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
    
    add_settings_field(
        'wp_fedora_disable_light_mode',
        'Disable Light Mode (Switch to Classic)',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_light_mode']
    );


    add_settings_field(
        'wp_fedora_enable_dashboard_widgets',
        'Enable Dashboard Widgets',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_dashboard_widgets']
    );

    add_settings_field(
        'wp_fedora_disable_admin_footer_customizer',
        'Disable Admin Footer Customizer',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_admin_footer_customizer']
    );

    add_settings_field(
        'wp_fedora_enable_admin_bar_resources',
        'Enable Admin Bar Resources',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_admin_bar_resources']
    );

    add_settings_field(
        'wp_fedora_disable_admin_bar_transition',
        'Disable Admin Bar Hover Transition',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_admin_bar_transition']
    );

    add_settings_field(
        'wp_fedora_disable_svg_upload',
        'Disable SVG Upload Support',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_svg_upload']
    );

    add_settings_field(
        'wp_fedora_disable_dashicons',               
        'Enable Dashicons on Frontend',              
        'wp_fedora_toggle_field_callback',          
        'general',                                  
        'wp_fedora_settings_section',               
        ['label_for' => 'wp_fedora_disable_dashicons'] 
    );

    add_settings_field(
        'wp_fedora_enable_comments',
        'Enable WordPress Comments',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_enable_comments']
    );
    
    add_settings_field(
        'wp_fedora_move_plugin_editor_default',
        'Move Plugin File Editor to WordPress Default Position',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_move_plugin_editor_default']
    );
    
    add_settings_field(
        'wp_fedora_move_theme_editor_default',
        'Move Theme File Editor to WordPress Default Position',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_move_theme_editor_default']
    );
    
    add_settings_field(
        'wp_fedora_disable_cpt',          
        'Disable Custom Post Types', 
        'wp_fedora_toggle_field_callback', 
        'general',                        
        'wp_fedora_settings_section',     
        ['label_for' => 'wp_fedora_disable_cpt'] 
    );
    
    add_settings_field(
        'wp_fedora_disable_robots_editor',
        'Disable Robots.txt Editor',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_robots_editor']
    );

    add_settings_field(
        'disable_404_monitor_log_', 
        'Disable 404 Monitor',      
        'wp_fedora_toggle_field_callback', 
        'general',                  
        'wp_fedora_settings_section', 
        ['label_for' => 'disable_404_monitor_log_'] 
    );
    add_settings_field(
        'wp_fedora_disable_sitemap_generator',          
        'Disable Sitemap Generator',                   
        'wp_fedora_toggle_field_callback',              
        'general',                                      
        'wp_fedora_settings_section',                   
        ['label_for' => 'wp_fedora_disable_sitemap_generator'] 
    );

    add_settings_field(
        'wp_fedora_disable_htaccess_editor',
        'Disable .htaccess Editor',
        'wp_fedora_toggle_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_disable_htaccess_editor']
    );

    add_settings_field(
        'wp_fedora_disable_script_manager',           
        'Disable Script Manager',                  
        'wp_fedora_toggle_field_callback',            
        'general',                                    
        'wp_fedora_settings_section',                 
        ['label_for' => 'wp_fedora_disable_script_manager'] 
    );

    add_settings_field(
        'wp_fedora_heartbeat_frequency',
        'Heartbeat API Frequency (seconds)',
        'wp_fedora_heartbeat_field_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_heartbeat_frequency']
    );

    add_settings_field(
        'wp_fedora_revision_limit',
        'Revision Limit',
        'wp_fedora_revision_limit_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_revision_limit']
    );

    add_settings_field(
        'wp_fedora_autosave_interval',
        'Autosave Interval',
        'wp_fedora_autosave_interval_callback',
        'general',
        'wp_fedora_settings_section',
        ['label_for' => 'wp_fedora_autosave_interval']
    );

}
add_action( 'admin_init', 'wp_fedora_register_settings' );

// Callback function to render a checkbox (unchecked by default)
function wp_fedora_toggle_field_callback( $args ) {
    $option_name = $args['label_for'];
    $checked = get_option( $option_name ) ? 'checked' : '';
    echo "<input type='checkbox' id='{$option_name}' name='{$option_name}' value='1' {$checked} />";
}

// Callback function to render the Heartbeat API input field
function wp_fedora_heartbeat_field_callback( $args ) {
    $option_name = $args['label_for'];
    $value = get_option( $option_name, 15 ); // Default value is 15 seconds
    echo "<input type='number' id='{$option_name}' name='{$option_name}' value='{$value}' min='5' max='300' />";
}

// Callback function to render the revision limit input field
function wp_fedora_revision_limit_callback( $args ) {
    $option_name = $args['label_for'];
    $value = get_option( $option_name, -1 ); // Default value is -1 (unlimited)
    echo "<input type='number' id='{$option_name}' name='{$option_name}' value='{$value}' min='-1' />"; // Allow -1 as minimum value
}

// Callback function to render the autosave interval input field
function wp_fedora_autosave_interval_callback( $args ) {
    $option_name = $args['label_for'];
    $value = get_option( $option_name, 60 ); // Default value is 60 seconds
    echo "<input type='number' id='{$option_name}' name='{$option_name}' value='{$value}' min='10' max='300' />";
}

// Load necessary feature and admin files based on user settings
function wp_fedora_load_files() {
    // ADMIN FOLDER
    
    // Disable Dashboard Widgets by default, enable if checked
    if ( get_option( 'wp_fedora_enable_dashboard_widgets' ) ) {
        // Do nothing, dashboard widgets are enabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/admin/remove-dashboard-widgets.php'; // Disable Dashboard Widgets feature file
    }
    
    // CONTENT MANAGEMENT FOLDER
    
    // Enable CPTs by default, disable if the checkbox is checked
    if ( get_option( 'wp_fedora_disable_cpt' ) ) {
        // CPTs are disabled because the checkbox is checked
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/content-management/custom-post-type.php'; // Load CPT module
    }
    
    // MEDIA FOLDER 
    
    // Disable SVG Upload by default, enable if checked
    if ( get_option( 'wp_fedora_disable_svg_upload' ) ) {
        // SVG Upload is disabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/media/enable-svg-upload.php'; // Enable SVG Upload feature file
    }
    
    // PERFORMANCE FOLDER
    
    // Disable Dashicons by default, enable if the checkbox is checked
    if ( get_option( 'wp_fedora_disable_dashicons' ) ) {
        // Dashicons are enabled because the checkbox is checked (i.e., do nothing to disable them)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-dashicons.php'; // Disable Dashicons feature file
    }
    // Enable Gutenberg if checked
    if ( get_option( 'wp_fedora_enable_gutenberg' ) ) {
        // Gutenberg is enabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-gutenberg.php';
    }
    // Enable REST API if checked
    if ( get_option( 'wp_fedora_enable_rest_api' ) ) {
        // REST API is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-rest-api.php'; // Disable REST API feature file
    }
    // Enable RSS feeds if checked
    if ( get_option( 'wp_fedora_enable_rss' ) ) {
        // RSS Feeds are enabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-rss-feeds.php';
    }
    // Enable WP Generator Tag if checked
    if ( get_option( 'wp_fedora_enable_wp_generator_tag' ) ) {
        // WP Generator Tag is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/disable-wp-generator-tags.php'; // Disable WP Generator Tag feature file
    }
    // Load Autosave Interval settings
    if ( get_option( 'wp_fedora_autosave_interval' ) != 60 ) {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/limit-autosave-intervals.php'; // Autosave Interval feature file
    }
    // Load Revisions Limit settings
    if ( get_option( 'wp_fedora_revision_limit' ) != -1 ) {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/limit-revisions.php'; // Revision Limit feature file
    }
    // Load Heartbeat Optimizer settings
    if ( get_option( 'wp_fedora_heartbeat_frequency' ) ) {
        require_once WP_FEDORA_DIR . 'wp-fedora/performance/wordpress-heartbeat-optimizer.php'; // Heartbeat Optimizer feature file
    }
    
    // SEO TOOLS FOLDER
    
    // 404 Monitor enabled by default, disable if the checkbox is checked
    if ( get_option( 'disable_404_monitor_log_' ) ) {
        // Do nothing, 404 Monitor is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/404-monitor.php'; // Load 404 Monitor feature file
    }
    // Disable Bulk Meta Editor if checked
    if ( get_option( 'wp_fedora_disable_bulk_meta_editor' ) ) {
        // Bulk Meta Editor is disabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/bulk-meta-editor.php';
    }
    if (get_option('wp_fedora_disable_htaccess_editor')) {
        // Do nothing, .htaccess editor is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/htaccess-file-editor.php'; // Load .htaccess Editor feature file
    }
    // Disable Meta Editor if checked
    if ( get_option( 'wp_fedora_disable_meta_editor' ) ) {
        // Meta Editor is disabled, so do nothing (or add related logic if needed)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/meta-editor.php';
    }
    // Disable Meta Robots Settings if checked
    if ( get_option( 'wp_fedora_disable_meta_robots' ) ) {
        // Do nothing, Meta Robots is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/meta-robots-settings.php'; // Meta Robots Settings feature file
    }
    // Robots.txt Editor enabled by default, disable if the checkbox is checked
    if ( get_option( 'wp_fedora_disable_robots_editor' ) ) {
        // Do nothing, Robots.txt Editor is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/robots-text-editor.php'; // Load Robots.txt Editor feature file
    }
    // Load Script Manager if not disabled
    if ( get_option( 'wp_fedora_disable_script_manager' ) ) {
        // Do nothing, Script Manager is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/seo-tools/script-manager.php'; // Load Script Manager module
    }
    
    // SITEMAP FOLDER
    
    // Load Sitemap Generator if not disabled
    if ( get_option( 'wp_fedora_disable_sitemap_generator' ) ) {
        // Do nothing, sitemap generator is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/sitemap/sitemap-generator.php'; // Load Sitemap Generator
    }

    // UI FOLDER 
    
    // Disable Admin Bar Hover Transition by default, enable if checked
    if ( get_option( 'wp_fedora_disable_admin_bar_transition' ) ) {
        // Admin Bar Hover Transition is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/admin-bar-transition.php'; // Disable Admin Bar Hover Transition feature file
    }
    // Enable Darkmode UI if checked
    if ( get_option( 'wp_fedora_enable_darkmode_ui' ) ) {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/admin-dark-theme.php'; // Darkmode UI feature file
    }    
    // Load Light mode UI if checked
    if ( get_option( 'wp_fedora_disable_light_mode' ) ) {
        wp_dequeue_style( 'wp-fedora-light-theme' );
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/admin-light-theme.php'; // Load Light Mode feature file
    }
    // Disable Admin Bar Resources by default, enable if checked
    if ( get_option( 'wp_fedora_enable_admin_bar_resources' ) ) {
        // Admin Bar Resources is enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/disable-admin-bar-resources.php'; // Disable Admin Bar Resources feature file
    }
    // Disable comments by default; enable only if the checkbox is checked
    if ( get_option( 'wp_fedora_enable_comments' ) ) {
        // Comments are enabled, so do nothing
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/disable-comments.php'; // Load the module to disable comments
    }
    // Enable Admin Footer Customizer by default, disable if checked
    if ( get_option( 'wp_fedora_disable_admin_footer_customizer' ) ) {
        // Do nothing, Admin Footer Customizer is disabled
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/footer-customizer.php'; // Admin Footer Customizer feature file
    }
    // Move Plugin File Editor to the last position by default; move it back to default if checkbox is checked
    if ( get_option( 'wp_fedora_move_plugin_editor_default' ) ) {
        // Leave Plugin File Editor in the default position (do nothing)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/move-plugin-file-editor.php'; // Move Plugin Editor to last position
    }
    // Move Theme File Editor to the last position by default; move it back to default if checkbox is checked
    if ( get_option( 'wp_fedora_move_theme_editor_default' ) ) {
    // Leave Theme File Editor in the default position (do nothing)
    } else {
        require_once WP_FEDORA_DIR . 'wp-fedora/ui/move-theme-file-editor.php'; // Move Theme Editor to last position
    }
}