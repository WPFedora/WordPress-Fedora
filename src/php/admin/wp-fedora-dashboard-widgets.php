<?php
/**
 * Plugin Name: WP Fedora Dashboard Widgets
 * Description: Adds two dashboard widgets for WP Fedora: one for update information and one for randomizing last modified dates within a specified date range.
 */

// Security check: exit if accessed directly outside of WordPress.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants for GitHub repository API URL and current plugin version
define('WP_FEDORA_REPO_URL', 'https://api.github.com/repos/WPFedora/WordPress-Fedora');
define('WP_FEDORA_CURRENT_VERSION', '1.3.2'); // Update this to the current version of your plugin

// Add the Dashboard Widgets
add_action('wp_dashboard_setup', 'wpfedora_add_dashboard_widgets');
function wpfedora_add_dashboard_widgets() {
    // Add WP Fedora Information widget
    wp_add_dashboard_widget('wpfedora_dashboard_widget', 'WP Fedora Information', 'wpfedora_dashboard_widget_display');

    // Add Date Range Randomizer widget
    wp_add_dashboard_widget('wpfedora_random_date_widget', 'Randomize Last Modified Dates', 'wpfedora_random_date_widget_display');
    
    // New Search and Replace Widget
    wp_add_dashboard_widget('wpfedora_search_replace_widget', 'Search and Replace', 'wpfedora_search_replace_widget_display');
    
}

// Display Function for WP Fedora Information Widget
function wpfedora_dashboard_widget_display() {
    // Start the widget container with custom styling
    echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';

    // Main title
    echo '<h3 style="font-size: 18px; margin-top: 0; color: #2e3c52;">Welcome to WP Fedora!</h3>';
    echo '<p style="font-size: 14px; color: #2e3c52; line-height: 1.5;">This plugin provides advanced WordPress features including custom SEO settings, performance enhancements, and developer tools.</p>';

    // Refresh icon for manual update check
    echo '<form method="post" style="display: inline-block; margin-bottom: 10px;">';
    echo '<button type="submit" name="wpfedora_manual_refresh" title="Manually refresh for updates" style="border: none; background: none; cursor: pointer;">';
    echo '<span class="dashicons dashicons-update" style="font-size: 20px; color: #007cba; vertical-align: middle;"></span>';
    echo '</button>';
    echo '</form>';

    // Informational note about update checks
    echo '<p style="font-size: 12px; color: #555; margin-top: 5px; line-height: 1.4;">This plugin automatically checks for updates every 24 hours. You can also manually refresh by clicking the icon above.</p>';
    echo '<hr style="margin: 10px 0; border-top: 1px solid #e2e4e7;">';

    // Check for updates and display the status message styled as a button
    $update_status = wpfedora_check_for_updates();
    echo '<p style="font-size: 14px; color: #444;"><strong>Status:</strong> ';
    echo '<span style="display: inline-block; padding: 6px 12px; background-color: #3c344f; color: #fff; border-radius: 3px; font-weight: bold;">';
    echo $update_status;
    echo '</span></p>';

    // Show an "Update" button using WordPress's default button style if an update is available
    if ($update_status === 'Update available!') {
        echo '<form method="post">';
        echo '<button type="submit" name="wpfedora_update_plugin" class="button button-primary" style="margin-top: 10px;">Update WP Fedora</button>';
        echo '</form>';
    }

    // Process manual refresh or update request
    if (isset($_POST['wpfedora_manual_refresh'])) {
        // Manual refresh result message styled as regular text
        $update_status = wpfedora_check_for_updates();
        echo '<p style="font-size: 12px; color: #555; margin-top: 8px;">Manual refresh: ' . $update_status . '</p>';
    } elseif (isset($_POST['wpfedora_update_plugin'])) {
        // Show the update result message after the update process is triggered
        $update_result = wpfedora_update_plugin_from_github();
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">' . $update_result . '</p>';
    }

    // End of the widget container
    echo '</div>';
}

// Display Function for Randomize Last Modified Dates Widget
function wpfedora_random_date_widget_display() {
    echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';
    echo '<h3 style="font-size: 18px; margin-top: 0; color: #2e3c52;">Randomize Last Modified Dates</h3>';
    echo '<p style="font-size: 14px; color: #2e3c52; line-height: 1.5;">Select a date range, and click "Go" to randomly assign modified dates within that range to all posts and pages.</p>';

    echo '<form method="post">';
    echo '<label for="start_date" style="display: block; font-size: 14px; color: #2e3c52; margin-top: 10px;">Start Date:</label>';
    echo '<input type="date" id="start_date" name="start_date" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">';

    echo '<label for="end_date" style="display: block; font-size: 14px; color: #2e3c52; margin-top: 10px;">End Date:</label>';
    echo '<input type="date" id="end_date" name="end_date" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">';

    echo '<button type="submit" name="randomize_dates" class="button button-primary" style="margin-top: 10px;">Go</button>';
    echo '</form>';

    if (isset($_POST['randomize_dates']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $randomized_count = wpfedora_randomize_last_modified_dates($start_date, $end_date);
        echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Successfully updated the last modified dates for ' . $randomized_count . ' posts/pages.</p>';
    }

    echo '</div>';
}

// Function to Randomize Last Modified Dates
function wpfedora_randomize_last_modified_dates($start_date, $end_date) {
    global $wpdb;
    $post_types = ['post', 'page'];

    // Convert start and end dates to timestamps
    $start_timestamp = strtotime($start_date . ' 00:00:00');
    $end_timestamp = strtotime($end_date . ' 23:59:59');
    $updated_count = 0;

    // Fetch all posts and pages
    $posts = $wpdb->get_results("
        SELECT ID FROM {$wpdb->posts}
        WHERE post_type IN ('" . implode("','", $post_types) . "') AND post_status = 'publish'
    ");

    foreach ($posts as $post) {
        // Generate a random timestamp between start and end date
        $random_timestamp = mt_rand($start_timestamp, $end_timestamp);
        $random_date = date('Y-m-d H:i:s', $random_timestamp);

        // Update post modified and post modified GMT
        $wpdb->update(
            $wpdb->posts,
            [
                'post_modified' => $random_date, 
                'post_modified_gmt' => gmdate('Y-m-d H:i:s', $random_timestamp)
            ],
            ['ID' => $post->ID]
        );
        $updated_count++;
    }

    return $updated_count;
}

// Display function for the Search and Replace Widget
function wpfedora_search_replace_widget_display() {
    echo '<div style="padding: 15px; border: 1px solid #e2e4e7; border-radius: 8px; background-color: #f9fafb;">';
    echo '<h3 style="font-size: 18px; margin-top: 0; color: #2e3c52;">Search and Replace</h3>';
    echo '<p style="font-size: 14px; color: #2e3c52; line-height: 1.5;">Specify the search term, replace term, and select one or more tables where you want to perform the replacement.</p>';

    // Warning message about database backup
    echo '<p style="color: #3c344f; font-weight: bold; margin-top: 10px;">⚠️ Warning: Please make sure you have a full database backup before running this operation.</p>';
    
    // Form for input fields
    echo '<form method="post">';
    echo '<label for="search_term">Search Term:</label>';
    echo '<input type="text" id="search_term" name="search_term" required style="width: 100%; padding: 8px; margin-top: 5px;">';

    echo '<label for="replace_term" style="margin-top: 10px;">Replace Term:</label>';
    echo '<input type="text" id="replace_term" name="replace_term" required style="width: 100%; padding: 8px; margin-top: 5px;">';

    // Multi-select dropdown for tables
    echo '<label for="table" style="margin-top: 10px;">Select Tables:</label>';
    echo '<select id="table" name="tables[]" multiple style="width: 100%; padding: 8px; margin-top: 5px; height: 120px;">';
    global $wpdb;
    $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
    foreach ($tables as $table) {
        echo '<option value="' . esc_attr($table[0]) . '">' . esc_html($table[0]) . '</option>';
    }
    echo '</select>';

    echo '<button type="submit" name="perform_replace" class="button button-primary" style="margin-top: 10px;">Replace</button>';
    echo '</form>';

    // Perform search and replace if form is submitted
    if (isset($_POST['perform_replace'])) {
        $search_term = sanitize_text_field($_POST['search_term']);
        $replace_term = sanitize_text_field($_POST['replace_term']);
        $tables = $_POST['tables'] ?? [];

        if ($search_term && $replace_term && !empty($tables)) {
            $total_replaced_count = 0;
            foreach ($tables as $table) {
                $table = sanitize_text_field($table);
                $replaced_count = wpfedora_search_replace($search_term, $replace_term, $table);
                $total_replaced_count += $replaced_count;
            }
            echo '<p style="font-size: 14px; color: #444; margin-top: 10px;">Replaced ' . $total_replaced_count . ' occurrences of "' . esc_html($search_term) . '" with "' . esc_html($replace_term) . '" across selected tables.</p>';
        } else {
            echo '<p style="color: red;">Please fill out all fields and select at least one table.</p>';
        }
    }

    echo '</div>';
}


// Function to Perform Search and Replace
function wpfedora_search_replace($search_term, $replace_term, $table) {
    global $wpdb;
    $fields = $wpdb->get_results("SHOW COLUMNS FROM $table", ARRAY_A);
    $replaced_count = 0;

    foreach ($fields as $field) {
        $field_name = $field['Field'];
        // Update table where field matches search term
        $query = $wpdb->prepare(
            "UPDATE $table SET $field_name = REPLACE($field_name, %s, %s) WHERE $field_name LIKE %s",
            $search_term,
            $replace_term,
            '%' . $wpdb->esc_like($search_term) . '%'
        );
        $replaced_count += $wpdb->query($query);
    }
    return $replaced_count;
}

// Get Latest Release URL from GitHub
function wpfedora_get_latest_release_url() {
    $response = wp_remote_get(WP_FEDORA_REPO_URL . '/releases/latest');
    if (is_wp_error($response)) {
        return false;
    }
    $release_data = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($release_data['tag_name'])) {
        $latest_tag = $release_data['tag_name'];
        return "https://github.com/WPFedora/WordPress-Fedora/releases/download/{$latest_tag}/wp-fedora-core.zip";
    }
    return false;
}

// Check for Updates
function wpfedora_check_for_updates() {
    $latest_release_url = wpfedora_get_latest_release_url();
    if (!$latest_release_url) {
        return 'Could not fetch update information.';
    }
    $response = wp_remote_get(WP_FEDORA_REPO_URL . '/releases/latest');
    if (is_wp_error($response)) {
        return 'Could not fetch update information.';
    }
    $release_data = json_decode(wp_remote_retrieve_body($response), true);
    $latest_version = $release_data['tag_name'];
    if (version_compare($latest_version, WP_FEDORA_CURRENT_VERSION, '>')) {
        return 'Update available!';
    } else {
        return 'WP Fedora is up-to-date.';
    }
}

// Download and Install Update from GitHub
function wpfedora_update_plugin_from_github() {
    $download_url = wpfedora_get_latest_release_url();
    if (!$download_url) {
        return 'Error fetching the update package URL.';
    }
    $zip_file = download_url($download_url);
    if (is_wp_error($zip_file)) {
        return 'Error downloading update package.';
    }
    $plugin_dir = plugin_dir_path(__FILE__);
    $result = unzip_file($zip_file, $plugin_dir);
    unlink($zip_file);
    if (is_wp_error($result)) {
        return 'Error updating plugin files.';
    }
    return 'Plugin updated successfully!';
}

// Schedule a 24-Hour Update Check on Plugin Activation
register_activation_hook(__FILE__, 'wpfedora_schedule_24hr_update_check');
function wpfedora_schedule_24hr_update_check() {
    if (!wp_next_scheduled('wpfedora_24hr_update_check')) {
        wp_schedule_single_event(time() + 86400, 'wpfedora_24hr_update_check'); // 86400 seconds = 24 hours
    }
}

// Hook the scheduled update check function to the event
add_action('wpfedora_24hr_update_check', 'wpfedora_scheduled_update_check');

// Define the function for the scheduled 24-hour update check
function wpfedora_scheduled_update_check() {
    $update_status = wpfedora_check_for_updates();
    if ($update_status === 'Update available!') {
        error_log('WP Fedora: A new update is available.');
    }
    // Re-schedule the next check for 24 hours later
    wp_schedule_single_event(time() + 86400, 'wpfedora_24hr_update_check');
}

// Clear the Scheduled Event on Plugin Deactivation
register_deactivation_hook(__FILE__, 'wpfedora_clear_24hr_update_check');
function wpfedora_clear_24hr_update_check() {
    wp_clear_scheduled_hook('wpfedora_24hr_update_check');
}