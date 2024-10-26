<?php
/*
Module Name: WP Fedora Custom Post Types
Description: This module allows users to create and manage custom post types (CPTs) natively within WP Fedora.
Author: WP Fedora
Version: 1.5
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Create a menu item under Tools
function wp_fedora_add_menu() {
    add_submenu_page(
        'tools.php',              // Parent slug
        'Custom Post Types',       // Page title
        'Custom Post Types',       // Menu title
        'manage_options',          // Capability
        'wp-fedora-cpt',           // Menu slug
        'wp_fedora_cpt_admin_page' // Callback function
    );
}
add_action('admin_menu', 'wp_fedora_add_menu');

// Admin page for managing CPTs
function wp_fedora_cpt_admin_page() {
    // Handle form submissions
    if (isset($_POST['wp_fedora_save_cpt'])) {
        wp_fedora_save_cpt();
        // Add success message using native WordPress method
        add_settings_error('wp_fedora_messages', 'cpt_saved', 'Custom Post Type saved successfully.', 'success');
    }
    
    if (isset($_GET['delete_cpt'])) {
        wp_fedora_delete_cpt(sanitize_text_field($_GET['delete_cpt']));
        // Add success message for deletion
        add_settings_error('wp_fedora_messages', 'cpt_deleted', 'Custom Post Type deleted successfully.', 'success');
    }

    // Display success or error messages
    settings_errors('wp_fedora_messages');

    // Display CPT management UI
    ?>
    <div class="wrap">
        <h1>Custom Post Types</h1>
        
        <!-- Add CPT Form -->
        <h2>Add New Custom Post Type</h2>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="cpt_name">CPT Name (slug)</label></th>
                    <td><input type="text" id="cpt_name" name="cpt_name" required></td>
                </tr>
                <tr>
                    <th><label for="cpt_label">CPT Label</label></th>
                    <td><input type="text" id="cpt_label" name="cpt_label" required></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="wp_fedora_save_cpt" class="button-primary" value="Save Custom Post Type"></p>
        </form>
        
        <!-- Display existing CPTs -->
        <h2>Manage Custom Post Types</h2>
        <ul>
            <?php 
            $cpts = get_option('wp_fedora_cpts', []);
            foreach ($cpts as $cpt): ?>
                <li>
                    <strong><?php echo esc_html($cpt['label']); ?></strong> (<?php echo esc_html($cpt['slug']); ?>)
                    <a href="?page=wp-fedora-cpt&delete_cpt=<?php echo esc_attr($cpt['slug']); ?>" class="button-link-delete">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

// Save new CPT
function wp_fedora_save_cpt() {
    if (isset($_POST['cpt_name']) && isset($_POST['cpt_label'])) {
        $cpts = get_option('wp_fedora_cpts', []);
        $slug = sanitize_text_field($_POST['cpt_name']);
        $label = sanitize_text_field($_POST['cpt_label']);
        $cpts[] = ['slug' => $slug, 'label' => $label];
        update_option('wp_fedora_cpts', $cpts);
        
        // Register the CPT immediately
        wp_fedora_register_custom_post_type($slug, $label);

        // Flush rewrite rules after adding a new CPT to avoid 404 errors
        flush_rewrite_rules();
    }
}

// Delete CPT
function wp_fedora_delete_cpt($slug) {
    $cpts = get_option('wp_fedora_cpts', []);
    foreach ($cpts as $key => $cpt) {
        if ($cpt['slug'] == $slug) {
            unset($cpts[$key]);
            update_option('wp_fedora_cpts', $cpts);
        }
    }

    // Flush rewrite rules after deleting a CPT
    flush_rewrite_rules();
}

// Register all custom post types on init
function wp_fedora_register_all_cpts() {
    $cpts = get_option('wp_fedora_cpts', []);
    foreach ($cpts as $cpt) {
        wp_fedora_register_custom_post_type($cpt['slug'], $cpt['label']);
    }
}
add_action('init', 'wp_fedora_register_all_cpts');

// Register a single custom post type
function wp_fedora_register_custom_post_type($slug, $label) {
    $args = array(
        'label'             => $label,
        'public'            => true,
        'has_archive'       => true,
        'show_in_menu'      => true,
        'supports'          => array('title', 'editor', 'thumbnail'),
        'menu_position'     => 25,
        'menu_icon'         => 'dashicons-admin-post',
    );
    register_post_type($slug, $args);
}

// Flush rewrite rules on activation
function wp_fedora_flush_rewrite_rules_on_activation() {
    wp_fedora_register_all_cpts();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wp_fedora_flush_rewrite_rules_on_activation');

// Optionally, flush rewrite rules on deactivation
function wp_fedora_flush_rewrite_rules_on_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wp_fedora_flush_rewrite_rules_on_deactivation');
?>
