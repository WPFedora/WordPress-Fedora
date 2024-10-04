<?php
/*
Plugin Name: WP Fedora Meta Robots Settings
Description: Adds a meta box for robots "No Index" and "No Follow" settings in posts and pages, disables WordPress default robots tag, and adds a custom robots tag.
Version: 1.2
Author: WP Fedora
*/

// Disable WordPress default robots meta tag
remove_action('wp_head', 'wp_robots', 1);

// Add the Meta Robots Settings meta box to posts/pages
function wp_fedora_add_robots_meta_box() {
    add_meta_box(
        'wp_fedora_meta_robots', // ID
        'Meta Robots Settings',  // Title
        'wp_fedora_render_robots_meta_box', // Callback function
        ['post', 'page'],  // Post types
        'normal',  // Context
        'high'  // Priority
    );
}
add_action( 'add_meta_boxes', 'wp_fedora_add_robots_meta_box' );

// Render the Meta Robots Settings meta box fields
function wp_fedora_render_robots_meta_box( $post ) {
    // Retrieve existing meta values
    $no_index = get_post_meta( $post->ID, '_wp_fedora_no_index', true );
    $no_follow = get_post_meta( $post->ID, '_wp_fedora_no_follow', true );

    // Output the fields for No Index and No Follow checkboxes
    ?>
    <p>
        <label>
            <input type="checkbox" name="wp_fedora_no_index" <?php checked( $no_index, '1' ); ?> />
            No Index
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" name="wp_fedora_no_follow" <?php checked( $no_follow, '1' ); ?> />
            No Follow
        </label>
    </p>
    <?php
}

// Save the meta box data when the post is saved
function wp_fedora_save_robots_meta_box_data( $post_id ) {
    // Check if the user has permission to save the data
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( !current_user_can( 'edit_post', $post_id ) ) return;

    // Save No Index setting
    $no_index_value = isset( $_POST['wp_fedora_no_index'] ) ? '1' : '';
    update_post_meta( $post_id, '_wp_fedora_no_index', sanitize_text_field( $no_index_value ) );

    // Save No Follow setting
    $no_follow_value = isset( $_POST['wp_fedora_no_follow'] ) ? '1' : '';
    update_post_meta( $post_id, '_wp_fedora_no_follow', sanitize_text_field( $no_follow_value ) );
}
add_action( 'save_post', 'wp_fedora_save_robots_meta_box_data' );

// Output custom robots meta tag with noindex, nofollow, and max-image-preview:large
function wp_fedora_output_robots_meta_tags() {
    if ( is_singular() ) {
        global $post;

        // Get the No Index and No Follow values
        $no_index = get_post_meta( $post->ID, '_wp_fedora_no_index', true );
        $no_follow = get_post_meta( $post->ID, '_wp_fedora_no_follow', true );

        // Initialize an array to hold the meta tag content
        $meta_content = [];

        // Add No Index/No Follow values if set
        if ( $no_index ) {
            $meta_content[] = 'noindex';
        } else {
            $meta_content[] = 'index'; // Default to "index" if No Index is not selected
        }

        if ( $no_follow ) {
            $meta_content[] = 'nofollow';
        } else {
            $meta_content[] = 'follow'; // Default to "follow" if No Follow is not selected
        }

        // Always include max-image-preview:large
        $meta_content[] = 'max-image-preview:large';

        // Output the final robots meta tag
        echo '<meta name="robots" content="' . esc_attr( implode( ', ', $meta_content ) ) . '">' . "\n";
    }
}

// Use the same priority as other meta tags (5) or slightly higher
add_action( 'wp_head', 'wp_fedora_output_robots_meta_tags', 5 );
