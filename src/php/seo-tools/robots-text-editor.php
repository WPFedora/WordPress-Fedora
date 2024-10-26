<?php
/*
Plugin Name: WP Fedora Robots.txt Editor
Description: This module adds a Robots Editor under the Tools menu, allowing you to edit the robots.txt file live using WordPress admin UI.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Add Robots Editor Menu under Tools
function wp_fedora_robots_editor_menu() {
    add_management_page(
        'Robots.txt Editor',       // Page title
        'Robots.txt Editor',       // Menu title
        'manage_options',          // Capability
        'wp-fedora-robots-editor', // Menu slug
        'wp_fedora_robots_editor_page'  // Function that displays the page content
    );
}
add_action('admin_menu', 'wp_fedora_robots_editor_menu');

// Display the Robots.txt Editor Page
function wp_fedora_robots_editor_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission and save changes to the robots.txt file
    if (isset($_POST['wp_fedora_robots_content'])) {
        check_admin_referer('wp_fedora_robots_save', 'wp_fedora_robots_nonce');

        $robots_content = sanitize_textarea_field($_POST['wp_fedora_robots_content']);
        $robots_file = ABSPATH . 'robots.txt';

        // Write the new content to the robots.txt file
        file_put_contents($robots_file, $robots_content);

        // Display an admin notice for successful save
        add_settings_error('wp_fedora_robots_messages', 'wp_fedora_robots_message', __('Robots.txt updated successfully.', 'wp-fedora'), 'updated');
    }

    // Get the current content of the robots.txt file
    $robots_file = ABSPATH . 'robots.txt';
    $robots_content = file_exists($robots_file) ? file_get_contents($robots_file) : '';

    // Display any saved messages (e.g., successful save)
    settings_errors('wp_fedora_robots_messages');
    ?>
    <div class="wrap">
        <h1><?php _e('Edit Robots.txt', 'wp-fedora'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('wp_fedora_robots_save', 'wp_fedora_robots_nonce'); ?>
            <textarea name="wp_fedora_robots_content" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($robots_content); ?></textarea>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wp-fedora'); ?>" />
            </p>
        </form>
    </div>
    <?php
}
