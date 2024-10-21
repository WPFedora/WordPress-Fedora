<?php
/*
Plugin Name: WP Fedora Meta Manager
Description: Adds a meta box for custom meta titles, descriptions, and OG tags. Outputs these meta tags in the <head> of the page. Supports any CPT, Categories, Tags, and Authors.
Version: 3.0
Author: WP Fedora
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ---------------------
// Add Meta Box for Pages, Posts, CPTs, and WooCommerce Products
// ---------------------

function wp_fedora_add_meta_box_to_cpts()
{
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
add_action('add_meta_boxes', 'wp_fedora_add_meta_box_to_cpts');

// Render the meta box fields with WordPress styling
function wp_fedora_render_meta_box($post)
{
    $meta_title = get_post_meta($post->ID, '_wp_fedora_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_wp_fedora_meta_description', true);
    $og_meta_title = get_post_meta($post->ID, '_wp_fedora_og_meta_title', true);
    $og_meta_description = get_post_meta($post->ID, '_wp_fedora_og_meta_description', true);

    // Meta box fields with character count
    ?>
    <p>
        <label for="wp_fedora_meta_title"><strong>Meta Title:</strong></label>
        <input type="text" id="wp_fedora_meta_title" name="wp_fedora_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
        <p style="font-size: 14px;"><small><span id="meta_title_count"><?php echo strlen($meta_title); ?></span> characters</small></p>
    </p>
    <p>
        <label for="wp_fedora_meta_description"><strong>Meta Description:</strong></label>
        <textarea id="wp_fedora_meta_description" name="wp_fedora_meta_description" class="widefat" rows="3"><?php echo esc_textarea($meta_description); ?></textarea>
        <p style="font-size: 14px;"><small><span id="meta_description_count"><?php echo strlen($meta_description); ?></span> characters</small></p>
    </p>
    <p>
        <label for="wp_fedora_og_meta_title"><strong>OG Meta Title (Optional):</strong></label>
        <input type="text" id="wp_fedora_og_meta_title" name="wp_fedora_og_meta_title" value="<?php echo esc_attr($og_meta_title); ?>" class="widefat" />
        <p style="font-size: 14px;"><small><span id="og_meta_title_count"><?php echo strlen($og_meta_title); ?></span> characters</small></p>
    </p>
    <p>
        <label for="wp_fedora_og_meta_description"><strong>OG Meta Description (Optional):</strong></label>
        <textarea id="wp_fedora_og_meta_description" name="wp_fedora_og_meta_description" class="widefat" rows="3"><?php echo esc_textarea($og_meta_description); ?></textarea>
        <p style="font-size: 14px;"><small><span id="og_meta_description_count"><?php echo strlen($og_meta_description); ?></span> characters</small></p>
    </p>
    <?php
}

// Ensure the meta box data is saved when the post is saved
function wp_fedora_save_meta_box_data($post_id)
{
    // Check if this is an autosave routine or if the user doesn't have permission to edit the post
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Verify the nonce to ensure it's a valid request (optional but recommended)
    if (!isset($_POST['wp_fedora_meta_title']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_id)) return;

    // Save Meta Title
    if (isset($_POST['wp_fedora_meta_title'])) {
        update_post_meta($post_id, '_wp_fedora_meta_title', sanitize_text_field($_POST['wp_fedora_meta_title']));
    }

    // Save Meta Description
    if (isset($_POST['wp_fedora_meta_description'])) {
        update_post_meta($post_id, '_wp_fedora_meta_description', sanitize_textarea_field($_POST['wp_fedora_meta_description']));
    }

    // Save OG Meta Title
    if (isset($_POST['wp_fedora_og_meta_title'])) {
        update_post_meta($post_id, '_wp_fedora_og_meta_title', sanitize_text_field($_POST['wp_fedora_og_meta_title']));
    }

    // Save OG Meta Description
    if (isset($_POST['wp_fedora_og_meta_description'])) {
        update_post_meta($post_id, '_wp_fedora_og_meta_description', sanitize_textarea_field($_POST['wp_fedora_og_meta_description']));
    }
}
add_action('save_post', 'wp_fedora_save_meta_box_data');

// ---------------------
// Meta Fields for Categories and Tags
// ---------------------

function wp_fedora_add_meta_fields_to_taxonomies($term)
{
    $meta_title = get_term_meta($term->term_id, '_wp_fedora_meta_title', true);
    $meta_description = get_term_meta($term->term_id, '_wp_fedora_meta_description', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wp_fedora_meta_title_tax">Meta Title</label></th>
        <td>
            <input type="text" name="wp_fedora_meta_title" id="wp_fedora_meta_title_tax" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
            <p style="font-size: 14px;"><small><span id="meta_title_count_tax"><?php echo strlen($meta_title); ?></span> characters</small></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wp_fedora_meta_description_tax">Meta Description</label></th>
        <td>
            <textarea name="wp_fedora_meta_description" id="wp_fedora_meta_description_tax" rows="5" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
            <p style="font-size: 14px;"><small><span id="meta_description_count_tax"><?php echo strlen($meta_description); ?></span> characters</small></p>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'wp_fedora_add_meta_fields_to_taxonomies');
add_action('post_tag_edit_form_fields', 'wp_fedora_add_meta_fields_to_taxonomies');

// **Ensure Saving of Taxonomy Fields**
function wp_fedora_save_taxonomy_meta($term_id)
{
    if (isset($_POST['wp_fedora_meta_title'])) {
        update_term_meta($term_id, '_wp_fedora_meta_title', sanitize_text_field($_POST['wp_fedora_meta_title']));
    }
    if (isset($_POST['wp_fedora_meta_description'])) {
        update_term_meta($term_id, '_wp_fedora_meta_description', sanitize_textarea_field($_POST['wp_fedora_meta_description']));
    }
}
add_action('edited_category', 'wp_fedora_save_taxonomy_meta');
add_action('edited_post_tag', 'wp_fedora_save_taxonomy_meta');
add_action('create_category', 'wp_fedora_save_taxonomy_meta');
add_action('create_post_tag', 'wp_fedora_save_taxonomy_meta');

// ---------------------
// Meta Fields for Authors
// ---------------------

function wp_fedora_add_meta_fields_to_authors($user)
{
    $meta_title = get_user_meta($user->ID, '_wp_fedora_meta_title', true);
    $meta_description = get_user_meta($user->ID, '_wp_fedora_meta_description', true);
    ?>
    <h3>Meta Information for Author Archive</h3>
    <table class="form-table">
        <tr>
            <th><label for="wp_fedora_meta_title_author">Meta Title</label></th>
            <td>
                <input type="text" name="wp_fedora_meta_title" id="wp_fedora_meta_title_author" value="<?php echo esc_attr($meta_title); ?>" class="widefat" />
                <p style="font-size: 14px;"><small><span id="meta_title_count_author"><?php echo strlen($meta_title); ?></span> characters</small></p>
            </td>
        </tr>
        <tr>
            <th><label for="wp_fedora_meta_description_author">Meta Description</label></th>
            <td><textarea name="wp_fedora_meta_description" id="wp_fedora_meta_description_author" rows="5" class="widefat"><?php echo esc_textarea($meta_description); ?></textarea>
                <p style="font-size: 14px;"><small><span id="meta_description_count_author"><?php echo strlen($meta_description); ?></span> characters</small></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'wp_fedora_add_meta_fields_to_authors');
add_action('edit_user_profile', 'wp_fedora_add_meta_fields_to_authors');

// **Ensure Saving of Author Meta Fields**
function wp_fedora_save_author_meta($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        if (isset($_POST['wp_fedora_meta_title'])) {
            update_user_meta($user_id, '_wp_fedora_meta_title', sanitize_text_field($_POST['wp_fedora_meta_title']));
        }
        if (isset($_POST['wp_fedora_meta_description'])) {
            update_user_meta($user_id, '_wp_fedora_meta_description', sanitize_textarea_field($_POST['wp_fedora_meta_description']));
        }
    }
}
add_action('personal_options_update', 'wp_fedora_save_author_meta');
add_action('edit_user_profile_update', 'wp_fedora_save_author_meta');

// ---------------------
// Enqueue Script for Character Count
// ---------------------

function wp_fedora_meta_box_scripts($hook)
{
    if ($hook == 'term.php' || $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'profile.php' || $hook == 'user-edit.php') {
        wp_enqueue_script('wp-fedora-meta-editor', plugin_dir_url(__FILE__) . 'meta-editor.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'wp_fedora_meta_box_scripts');

// ---------------------
// Output Meta Tags and OG Tags in the <head> section
// ---------------------

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
    } elseif ( is_category() || is_tag() ) {
        $term_id = get_queried_object()->term_id;
        $meta_description = get_term_meta( $term_id, '_wp_fedora_meta_description', true );
        echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
    } elseif ( is_author() ) {
        $user_id = get_queried_object()->ID;
        $meta_description = get_user_meta( $user_id, '_wp_fedora_meta_description', true );
        echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'wp_fedora_output_meta_tags', 5 ); // Slightly lower priority

// ---------------------
// Override Default Titles for Taxonomies, Authors, and Singulars
// ---------------------

// Override the title for singular pages (pages/posts/CPTs)
function wp_fedora_custom_title( $title_parts ) {
    if ( is_singular() ) {
        global $post;

        // Get custom meta title from post meta
        $meta_title = get_post_meta( $post->ID, '_wp_fedora_meta_title', true );

        // Replace title with meta title if it exists
        if ( !empty( $meta_title ) ) {
            $title_parts['title'] = esc_html( $meta_title );
            
            // Remove site name and separator
            unset($title_parts['site'], $title_parts['tagline']);
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'wp_fedora_custom_title' );

// Override the title for taxonomy pages (categories/tags)
function wp_fedora_filter_taxonomy_title($title_parts)
{
    if (is_category() || is_tag()) {
        $term_id = get_queried_object()->term_id;
        $meta_title = get_term_meta($term_id, '_wp_fedora_meta_title', true);

        if (!empty($meta_title)) {
            $title_parts['title'] = esc_html($meta_title);
            
            // Remove site name and separator
            unset($title_parts['site'], $title_parts['tagline']);
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'wp_fedora_filter_taxonomy_title' );

// Override the title for author archive pages
function wp_fedora_filter_author_title($title_parts)
{
    if (is_author()) {
        $user_id = get_queried_object()->ID;
        $meta_title = get_user_meta($user_id, '_wp_fedora_meta_title', true);

        if (!empty($meta_title)) {
            $title_parts['title'] = esc_html($meta_title);
            
            // Remove site name and separator
            unset($title_parts['site'], $title_parts['tagline']);
        }
    }
    return $title_parts;
}
add_filter('document_title_parts', 'wp_fedora_filter_author_title');
