<?php
/*
Plugin Name: WP Fedora .htaccess Editor
Description: This module adds an .htaccess Editor under the Tools menu, allowing you to edit the .htaccess file live using WordPress admin UI. Only works if the server is Apache.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Check if the server is Apache before adding the menu
function wp_fedora_htaccess_editor_menu() {
    if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false) {
        add_management_page(
            '.htaccess Editor',       // Page title
            '.htaccess Editor',       // Menu title
            'manage_options',          // Capability
            'wp-fedora-htaccess-editor', // Menu slug
            'wp_fedora_htaccess_editor_page'  // Function that displays the page content
        );
    }
}
add_action('admin_menu', 'wp_fedora_htaccess_editor_menu');

// Display the .htaccess Editor Page
function wp_fedora_htaccess_editor_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Path to the .htaccess file
    $htaccess_file = ABSPATH . '.htaccess';

    // Handle form submission and save changes to the .htaccess file
    if (isset($_POST['wp_fedora_htaccess_content'])) {
        check_admin_referer('wp_fedora_htaccess_save', 'wp_fedora_htaccess_nonce');

        $htaccess_content = sanitize_textarea_field($_POST['wp_fedora_htaccess_content']);

        // Write the new content to the .htaccess file
        if (is_writable($htaccess_file)) {
            file_put_contents($htaccess_file, $htaccess_content);

            // Display an admin notice for successful save
            add_settings_error('wp_fedora_htaccess_messages', 'wp_fedora_htaccess_message', __('.htaccess updated successfully.', 'wp-fedora'), 'updated');
        } else {
            add_settings_error('wp_fedora_htaccess_messages', 'wp_fedora_htaccess_error', __('The .htaccess file is not writable.', 'wp-fedora'), 'error');
        }
    }

    // Get the current content of the .htaccess file
    $htaccess_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';

    // Display any saved messages (e.g., successful save or errors)
    settings_errors('wp_fedora_htaccess_messages');
    ?>
    <div class="wrap">
        <h1><?php _e('Edit .htaccess', 'wp-fedora'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('wp_fedora_htaccess_save', 'wp_fedora_htaccess_nonce'); ?>
            <textarea name="wp_fedora_htaccess_content" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($htaccess_content); ?></textarea>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wp-fedora'); ?>" />
            </p>
        </form>
    </div>
    <?php
}
