<?php
/*
Plugin Name: Bulk Meta Editor
Description: Adds meta title and description fields to the post/page/CPT list screen, allowing inline editing and saving.
Version: 1.1
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Add columns to any CPT list table
function bulk_meta_editor_add_columns($columns) {
    $columns['meta_title'] = __('Meta Title', 'bulk-meta-editor');
    $columns['meta_description'] = __('Meta Description', 'bulk-meta-editor');
    return $columns;
}

function bulk_meta_editor_add_columns_to_cpts() {
    $post_types = get_post_types(['public' => true], 'names'); // Get all public post types
    foreach ($post_types as $post_type) {
        add_filter("manage_{$post_type}_posts_columns", 'bulk_meta_editor_add_columns');
    }
}
add_action('admin_init', 'bulk_meta_editor_add_columns_to_cpts');

// Populate the meta fields in the columns
function bulk_meta_editor_custom_column($column, $post_id) {
    if ($column == 'meta_title') {
        $meta_title = get_post_meta($post_id, '_wp_fedora_meta_title', true);
        echo '<input type="text" class="meta-title-field" value="' . esc_attr($meta_title) . '" data-post-id="' . $post_id . '">';
    }
    if ($column == 'meta_description') {
        $meta_description = get_post_meta($post_id, '_wp_fedora_meta_description', true);
        echo '<input type="text" class="meta-description-field" value="' . esc_attr($meta_description) . '" data-post-id="' . $post_id . '">';
    }
}

function bulk_meta_editor_custom_column_for_cpts() {
    $post_types = get_post_types(['public' => true], 'names'); // Get all public post types
    foreach ($post_types as $post_type) {
        add_action("manage_{$post_type}_posts_custom_column", 'bulk_meta_editor_custom_column', 10, 2);
    }
}
add_action('admin_init', 'bulk_meta_editor_custom_column_for_cpts');

// Save meta via AJAX
function bulk_meta_editor_save_meta() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bulk_meta_editor_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $post_id = intval($_POST['post_id']);
    $meta_title = sanitize_text_field($_POST['meta_title']);
    $meta_description = sanitize_textarea_field($_POST['meta_description']);

    update_post_meta($post_id, '_wp_fedora_meta_title', $meta_title);
    update_post_meta($post_id, '_wp_fedora_meta_description', $meta_description);

    wp_send_json_success();
}
add_action('wp_ajax_bulk_meta_editor_save_meta', 'bulk_meta_editor_save_meta');

// Enqueue JS for inline editing and saving
function bulk_meta_editor_enqueue_scripts($hook) {
    if ($hook != 'edit.php') return; // Only load on post/page/CPT list screens
    wp_enqueue_script('bulk-meta-editor-js', plugin_dir_url(__FILE__) . '../assets/js/bulk-meta-editor.js', ['jquery'], '1.1', true);

    // Localize script with nonce and ajax_url
    wp_localize_script('bulk-meta-editor-js', 'bulkMetaEditor', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bulk_meta_editor_nonce'),
    ]);
}
add_action('admin_enqueue_scripts', 'bulk_meta_editor_enqueue_scripts');
