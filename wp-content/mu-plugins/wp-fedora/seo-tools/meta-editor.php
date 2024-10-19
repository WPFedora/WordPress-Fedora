<?php
/*
Plugin Name: WP Fedora Meta Manager
Description: Adds a meta box for custom meta titles, descriptions, and OG tags. Outputs these meta tags in the <head> of the page. Supports any CPT.
Version: 1.9
Author: WP Fedora
*/

// Prevent direct access to the file
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Add meta box to posts, pages, and CPTs for custom meta title and meta description
function wp_fedora_add_meta_box_to_cpts() {
    $post_types = get_post_types(['public' => true], 'names'); // Get all public post types
    foreach ($post_types as $post_type) {
        add_meta_box(
            'wp_fedora_meta_box', // ID
            'Meta Information', // Title
            'wp_fedora_render_meta_box', // Callback function
            $post_type, // Post types (pages, posts, CPTs)
            'normal', // Context
            'high' // Priority
        );
    }
}
add_action( 'add_meta_boxes', 'wp_fedora_add_meta_box_to_cpts' );

// Render the meta box fields with WordPress styling
function wp_fedora_render_meta_box( $post ) {
    // Retrieve existing meta values
    $meta_title = get_post_meta( $post->ID, '_wp_fedora_meta_title', true );
    $meta_description = get_post_meta( $post->ID, '_wp_fedora_meta_description', true );
    $og_meta_title = get_post_meta( $post->ID, '_wp_fedora_og_meta_title', true );
    $og_meta_description = get_post_meta( $post->ID, '_wp_fedora_og_meta_description', true );

    // Output the meta box fields using consistent WordPress styling with .widefat class
    ?>
    <p>
        <label for="wp_fedora_meta_title"><strong>Meta Title:</strong></label>
        <input type="text" id="wp_fedora_meta_title" name="wp_fedora_meta_title" value="<?php echo esc_attr( $meta_title ); ?>" class="widefat" />
        <small><span id="meta_title_count"><?php echo strlen( $meta_title ); ?></span> characters</small>
    </p>
    <p>
        <label for="wp_fedora_meta_description"><strong>Meta Description:</strong></label>
        <textarea id="wp_fedora_meta_description" name="wp_fedora_meta_description" class="widefat" rows="3"><?php echo esc_textarea( $meta_description ); ?></textarea>
        <small><span id="meta_description_count"><?php echo strlen( $meta_description ); ?></span> characters</small>
    </p>
    <p>
        <label for="wp_fedora_og_meta_title"><strong>OG Meta Title (Optional):</strong></label>
        <input type="text" id="wp_fedora_og_meta_title" name="wp_fedora_og_meta_title" value="<?php echo esc_attr( $og_meta_title ); ?>" class="widefat" />
        <small><span id="og_meta_title_count"><?php echo strlen( $og_meta_title ); ?></span> characters</small>
    </p>
    <p>
        <label for="wp_fedora_og_meta_description"><strong>OG Meta Description (Optional):</strong></label>
        <textarea id="wp_fedora_og_meta_description" name="wp_fedora_og_meta_description" class="widefat" rows="3"><?php echo esc_textarea( $og_meta_description ); ?></textarea>
        <small><span id="og_meta_description_count"><?php echo strlen( $og_meta_description ); ?></span> characters</small>
    </p>
    <?php
}

// Save meta box data when the post is saved
function wp_fedora_save_meta_box_data( $post_id ) {
    // Check if the user has permission to save the data
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( !current_user_can( 'edit_post', $post_id ) ) return;

    // Sanitize and save meta title
    if ( isset( $_POST['wp_fedora_meta_title'] ) ) {
        update_post_meta( $post_id, '_wp_fedora_meta_title', sanitize_text_field( $_POST['wp_fedora_meta_title'] ) );
    }

    // Sanitize and save meta description
    if ( isset( $_POST['wp_fedora_meta_description'] ) ) {
        update_post_meta( $post_id, '_wp_fedora_meta_description', sanitize_textarea_field( $_POST['wp_fedora_meta_description'] ) );
    }

    // Sanitize and save OG meta title
    if ( isset( $_POST['wp_fedora_og_meta_title'] ) ) {
        update_post_meta( $post_id, '_wp_fedora_og_meta_title', sanitize_text_field( $_POST['wp_fedora_og_meta_title'] ) );
    }

    // Sanitize and save OG meta description
    if ( isset( $_POST['wp_fedora_og_meta_description'] ) ) {
        update_post_meta( $post_id, '_wp_fedora_og_meta_description', sanitize_textarea_field( $_POST['wp_fedora_og_meta_description'] ) );
    }
}
add_action( 'save_post', 'wp_fedora_save_meta_box_data' );

// Enqueue script to handle dynamic character count from external JS file
function wp_fedora_meta_box_scripts($hook) {
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        wp_enqueue_script('wp-fedora-meta-editor', plugin_dir_url(__FILE__) . 'meta-editor.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'wp_fedora_meta_box_scripts');

// Filter to replace the document title with the custom meta title
function wp_fedora_custom_title( $title ) {
    if ( is_singular() ) {
        global $post;

        // Get custom meta title from post meta
        $meta_title = get_post_meta( $post->ID, '_wp_fedora_meta_title', true );

        // Replace title with meta title if it exists
        if ( !empty( $meta_title ) ) {
            $title['title'] = esc_html( $meta_title );

            // Prevent site name and separator from being added
            unset($title['site'], $title['tagline']);
        }
    }
    return $title;
}
add_filter( 'document_title_parts', 'wp_fedora_custom_title', 10, 1 );

// Output custom meta tags and OG tags in the <head> section
function wp_fedora_output_meta_tags() {
    if ( is_singular() ) {
        global $post;

        // Get meta values from post meta
        $meta_title = get_post_meta( $post->ID, '_wp_fedora_meta_title', true );
        $meta_description = get_post_meta( $post->ID, '_wp_fedora_meta_description', true );
        $og_meta_title = get_post_meta( $post->ID, '_wp_fedora_og_meta_title', true );
        $og_meta_description = get_post_meta( $post->ID, '_wp_fedora_og_meta_description', true );

        // Fall back to post title if no meta title is set
        $meta_title = !empty( $meta_title ) ? $meta_title : get_the_title( $post->ID );

        // Fall back to an excerpt or description if no meta description is set
        $meta_description = !empty( $meta_description ) ? $meta_description : wp_trim_words( get_the_excerpt( $post->ID ), 30 );

        // Output custom meta tags in the <head> section
        echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";

        // Output OG meta tags if available
        if ( !empty( $og_meta_title ) ) {
            echo '<meta property="og:title" content="' . esc_attr( $og_meta_title ) . '">' . "\n";
        } else {
            echo '<meta property="og:title" content="' . esc_attr( $meta_title ) . '">' . "\n";
        }

        if ( !empty( $og_meta_description ) ) {
            echo '<meta property="og:description" content="' . esc_attr( $og_meta_description ) . '">' . "\n";
        } else {
            echo '<meta property="og:description" content="' . esc_attr( $meta_description ) . '">' . "\n";
        }
    }
}
add_action( 'wp_head', 'wp_fedora_output_meta_tags', 5 ); // Slightly lower priority
