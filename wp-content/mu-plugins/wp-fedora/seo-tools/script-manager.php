<?php
/*
Plugin Name: WP Fedora Script Manager
Description: A WP Fedora module to manage scripts on a per-page/post basis with the ability to create, retrieve, and delete scripts. Supports pages, posts, custom post types, categories, tags, and authors.
Version: 1.2
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add the Script Manager menu under Tools
function wp_fedora_script_manager_menu() {
    add_management_page(
        'Script Manager',              // Page title
        'Script Manager',              // Menu title
        'manage_options',              // Capability
        'wp-fedora-script-manager',    // Menu slug
        'wp_fedora_render_script_manager_page'  // Function to display the page
    );
}
add_action('admin_menu', 'wp_fedora_script_manager_menu');

// Ensure form submission processing before output is sent
add_action('admin_init', 'wp_fedora_check_form_submission');

// Handle form submission
function wp_fedora_check_form_submission() {
    if (isset($_POST['action']) && $_POST['action'] == 'wp_fedora_add_script') {
        wp_fedora_add_script();
    }
}

// Render the Script Manager page
// Render the Script Manager page with the added "Script Content" column
function wp_fedora_render_script_manager_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_fedora_scripts';  // Custom table name for scripts

    // Handle delete row action (for scripts)
    if (isset($_POST['delete_row'])) {
        $row_id = intval($_POST['delete_row']);
        $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
    }

    // Handle create table action
    if (isset($_POST['create_script_table'])) {
        wp_fedora_create_script_table();
    }

    // Handle delete table action
    if (isset($_POST['delete_script_table'])) {
        wp_fedora_delete_script_table();
    }

    // Check if the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        // If the table does not exist, show the "Create Script Database" button
        echo '<div class="wrap"><h1>' . __('Script Manager', 'wp-fedora') . '</h1>';
        echo '<form method="post"><input type="submit" name="create_script_table" class="button-primary" value="Create Script Database" /></form></div>';
    } else {
        // If the table exists, display the form and existing scripts
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
        ?>
        <div class="wrap">
            <h1><?php _e('Manage Scripts', 'wp-fedora'); ?></h1>
            
            <h2>Add New Script</h2>
            <?php wp_fedora_render_script_form(); ?>
            
            <h2><?php _e('Existing Scripts', 'wp-fedora'); ?></h2>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php _e('Script Name', 'wp-fedora'); ?></th>
                        <th><?php _e('Location', 'wp-fedora'); ?></th>
                        <th><?php _e('Target Pages/Posts', 'wp-fedora'); ?></th>
                        <th><?php _e('Script Content', 'wp-fedora'); ?></th>
                        <th><?php _e('Actions', 'wp-fedora'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results) : ?>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->script_name); ?></td>
                                <td><?php echo esc_html($row->script_location); ?></td>
                                <td><?php echo wp_fedora_convert_ids_to_titles($row->target_pages); ?></td>
                                <td>
                                    <?php 
                                    // Shorten script content for display purposes if it exceeds 100 characters
                                    $script_display = (strlen($row->script_text) > 100) ? substr($row->script_text, 0, 100) . '...' : $row->script_text;
                                    echo '<pre>' . esc_html($script_display) . '</pre>'; 
                                    ?>
                                </td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="delete_row" value="<?php echo esc_attr($row->id); ?>">
                                        <input type="submit" class="button-secondary" value="Delete">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php _e('No scripts added yet.', 'wp-fedora'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <form method="post" style="margin-top: 20px;">
                <input type="submit" name="delete_script_table" class="button-secondary" value="Delete Table" />
            </form>
        </div>
        <?php
    }
}


// Create the table for storing scripts
function wp_fedora_create_script_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_fedora_scripts';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        script_name varchar(255) NOT NULL,
        script_text longtext NOT NULL,
        script_location varchar(255) NOT NULL,
        target_pages longtext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Delete the scripts table
function wp_fedora_delete_script_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_fedora_scripts';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Render the form to add new scripts
function wp_fedora_render_script_form() {
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="action" value="wp_fedora_add_script">';  // Add action input for form submission
    echo '<label for="script_name">Script Name:</label><br>';
    echo '<input type="text" id="script_name" name="script_name" required><br><br>';
    
    echo '<label for="script_text">Script:</label><br>';
    echo '<textarea id="script_text" name="script_text" rows="5" cols="50" required></textarea><br><br>';
    
    echo '<label for="script_location">Location:</label><br>';
    echo '<select id="script_location" name="script_location">';
    echo '<option value="head">Head</option>';
    echo '<option value="footer">Footer</option>';
    echo '</select><br><br>';
    
    echo '<label for="target_pages">Target Pages/Posts:</label><br>';
    echo '<select id="target_pages" name="target_pages[]" multiple>';
    wp_fedora_render_target_pages_options(); // Pull in all post types, authors, and taxonomies here
    echo '</select><br><br>';
    
    echo '<input type="submit" name="submit_script" class="button-primary" value="Add Script">';
    echo '</form>';
}

// Render all pages/posts/custom post types/taxonomies/authors for the multi-select input
function wp_fedora_render_target_pages_options() {
    // 1. Pull Pages
    $pages = get_pages();
    foreach ($pages as $page) {
        echo '<option value="page-' . $page->ID . '">' . $page->post_title . ' (Page)</option>';
    }

    // 2. Pull Posts
    $posts = get_posts(array('numberposts' => -1));
    foreach ($posts as $post) {
        echo '<option value="post-' . $post->ID . '">' . $post->post_title . ' (Post)</option>';
    }

    // 3. Pull Custom Post Types
    $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
    foreach ($custom_post_types as $post_type) {
        $custom_posts = get_posts(array('post_type' => $post_type->name, 'numberposts' => -1));
        foreach ($custom_posts as $custom_post) {
            echo '<option value="custom-' . $custom_post->ID . '">' . $custom_post->post_title . ' (' . $post_type->labels->singular_name . ')</option>';
        }
    }

    // 4. Pull Categories
    $categories = get_categories();
    foreach ($categories as $category) {
        echo '<option value="category-' . $category->term_id . '">' . $category->name . ' (Category)</option>';
    }

    // 5. Pull Tags
    $tags = get_tags();
    foreach ($tags as $tag) {
        echo '<option value="tag-' . $tag->term_id . '">' . $tag->name . ' (Tag)</option>';
    }

    // 6. Pull Authors
    $authors = get_users(array('role__in' => array('administrator', 'editor', 'author')));
    foreach ($authors as $author) {
        echo '<option value="author-' . $author->ID . '">' . $author->display_name . ' (Author)</option>';
    }
}

function wp_fedora_add_script() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_fedora_scripts';

    // Define allowed HTML tags and attributes for sanitization
    $allowed_html = [
        'a' => ['href' => true, 'title' => true, 'target' => true, 'rel' => true],
        'abbr' => ['title' => true, 'class' => true, 'id' => true, 'style' => true],
        'address' => ['class' => true, 'id' => true, 'style' => true],
        'bdi' => ['class' => true, 'id' => true],
        'br' => [],
        'button' => ['class' => true, 'id' => true, 'style' => true, 'type' => true, 'onclick' => true],
        'div' => ['class' => true, 'id' => true, 'style' => true],
        'iframe' => ['src' => true, 'height' => true, 'width' => true, 'style' => true, 'allow' => true, 'allowfullscreen' => true],
        'img' => ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'class' => true, 'id' => true, 'style' => true],
        'li' => ['class' => true, 'id' => true, 'style' => true],
        'meta' => ['name' => true, 'content' => true],
        'noscript' => [],
        'ol' => ['class' => true, 'id' => true, 'style' => true],
        'p' => ['class' => true, 'id' => true, 'style' => true],
        'script' => ['src' => true, 'type' => true, 'async' => true, 'defer' => true],
        'span' => ['class' => true, 'id' => true, 'style' => true],
        'style' => ['src' => true, 'type' => true],
        'ul' => ['class' => true, 'id' => true, 'style' => true],
        'chat-widget' => ['location-id' => true, 'style' => true, 'sub-heading' => true, 'prompt-avatar' => true, 'agency-name' => true, 'agency-website' => true, 'locale' => true, 'send-label' => true, 'primary-color' => true]
    ];

    // Validate and sanitize inputs
    $script_name = sanitize_text_field($_POST['script_name']);
    $script_text = wp_kses(wp_unslash($_POST['script_text']), $allowed_html);  // Sanitize script content
    $script_location = sanitize_text_field($_POST['script_location']);
    $target_pages = implode(',', array_map('sanitize_text_field', $_POST['target_pages']));

    // Insert the new script into the database
    $wpdb->insert(
        $table_name,
        array(
            'script_name' => $script_name,
            'script_text' => $script_text,
            'script_location' => $script_location,
            'target_pages' => $target_pages
        ),
        array('%s', '%s', '%s', '%s')
    );

    // Ensure no output has been sent before redirection
    if (!headers_sent()) {
        wp_redirect(admin_url('tools.php?page=wp-fedora-script-manager'));  // Redirect back to the script manager page
        exit();
    } else {
        echo "<p>Headers already sent. Cannot redirect, but the script has been added.</p>";
    }
}


// Convert IDs to titles for the "Target Pages/Posts" column
function wp_fedora_convert_ids_to_titles($target_pages) {
    $target_pages_array = explode(',', $target_pages);
    $titles = array();

    foreach ($target_pages_array as $target) {
        if (strpos($target, 'page-') !== false) {
            $page_id = str_replace('page-', '', $target);
            $titles[] = get_the_title($page_id) . ' (Page)';
        } elseif (strpos($target, 'post-') !== false) {
            $post_id = str_replace('post-', '', $target);
            $titles[] = get_the_title($post_id) . ' (Post)';
        } elseif (strpos($target, 'custom-') !== false) {
            $custom_id = str_replace('custom-', '', $target);
            $titles[] = get_the_title($custom_id) . ' (Custom Post)';
        } elseif (strpos($target, 'category-') !== false) {
            $category_id = str_replace('category-', '', $target);
            $category = get_category($category_id);
            $titles[] = $category->name . ' (Category)';
        } elseif (strpos($target, 'tag-') !== false) {
            $tag_id = str_replace('tag-', '', $target);
            $tag = get_tag($tag_id);
            $titles[] = $tag->name . ' (Tag)';
        } elseif (strpos($target, 'author-') !== false) {
            $author_id = str_replace('author-', '', $target);
            $author = get_user_by('id', $author_id);
            $titles[] = $author->display_name . ' (Author)';
        }
    }

    return implode(', ', $titles);
}

// Hook to inject scripts in the head or footer of specified pages
add_action('wp_head', 'wp_fedora_inject_scripts_in_head');
add_action('wp_footer', 'wp_fedora_inject_scripts_in_footer');

function wp_fedora_inject_scripts_in_head() {
    wp_fedora_inject_scripts('head');
}

function wp_fedora_inject_scripts_in_footer() {
    wp_fedora_inject_scripts('footer');
}

// Inject scripts based on location and target pages
function wp_fedora_inject_scripts($location) {
    global $wpdb;
    $current_page_id = get_queried_object_id();
    $current_post_type = get_post_type();
    $current_term_id = (is_category() || is_tag()) ? get_queried_object()->term_id : null;
    $current_user_id = get_the_author_meta('ID');

    $table_name = $wpdb->prefix . 'wp_fedora_scripts';

    // Get all scripts for the current location (head/footer)
    $scripts = $wpdb->get_results("SELECT * FROM $table_name WHERE script_location = '$location'");

    foreach ($scripts as $script) {
        $target_pages = explode(',', $script->target_pages);
        
        // Check if the current page, post, category, tag, or author is in the target pages
        if (
            in_array('page-' . $current_page_id, $target_pages) ||
            in_array('post-' . $current_page_id, $target_pages) ||
            in_array('custom-' . $current_page_id, $target_pages) ||
            in_array('category-' . $current_term_id, $target_pages) ||
            in_array('tag-' . $current_term_id, $target_pages) ||
            in_array('author-' . $current_user_id, $target_pages)
        ) {
            echo $script->script_text;
        }
    }
}
