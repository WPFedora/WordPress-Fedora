<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP Fedora
 * Plugin URI:        http://wpfedora.com
 * Description:       Core functionalities for WP Fedora, including bulk meta editing, disabling WP generator tag, disabling REST API, Meta Robots Settings, Darkmode UI, Admin Footer Customizer, Admin Bar Resources Disabler, Admin Bar Hover Transition, SVG Upload Support, Heartbeat Optimizer, Revisions Limit, Autosave Interval, Disable Emojis, Dashicons, OEmbed, and other SEO tools.
 * Version:           1.2.0
 * Author:            WP Fedora
 * Author URI:        wpfedora.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-fedora
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook into WordPress to generate the sitemap on request
function wp_fedora_sitemap_init() {
    add_action('template_redirect', 'wp_fedora_generate_sitemap');
}
add_action('init', 'wp_fedora_sitemap_init');

// Generate the sitemap.xml file in proper XML format with XSL reference
function wp_fedora_generate_sitemap() {
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/sitemap.xml') !== false) {
        header('Content-Type: application/xml; charset=UTF-8');
        try {
            echo wp_fedora_build_sitemap_xml();
        } catch (Exception $e) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<!-- Error: ' . esc_html($e->getMessage()) . ' -->';
            error_log('Sitemap generation error: ' . $e->getMessage());
        }
        exit();
    }
}

// Build the sitemap structure in XML format with XSL reference
function wp_fedora_build_sitemap_xml() {
    // Determine if the plugin is loaded as a mu-plugin or standard plugin
    if (defined('WPMU_PLUGIN_DIR') && strpos(__FILE__, WPMU_PLUGIN_DIR) !== false) {
        // mu-plugin path
        $stylesheet_path = home_url('wp-content/mu-plugins/wp-fedora/sitemap.xsl');
    } else {
        // Standard plugin path
        $stylesheet_path = home_url('/wp-content/plugins/wp-fedora/assets/sitemap.xsl');
    }

    $urlset = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $urlset .= '<?xml-stylesheet type="text/xsl" href="' . $stylesheet_path . '"?>' . PHP_EOL;
    $urlset .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // Include the home page
    $urlset .= wp_fedora_generate_url_element(get_home_url(), 'daily', '1.0');

    // Include posts, pages, custom post types based on settings
    if (!get_option('wp_fedora_disable_pages')) {
        $urlset .= wp_fedora_generate_post_type_urls('page');
    }

    if (!get_option('wp_fedora_disable_posts')) {
        $urlset .= wp_fedora_generate_post_type_urls('post');
    }

    if (!get_option('wp_fedora_disable_cpts')) {
        $post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
        foreach ($post_types as $post_type) {
            $urlset .= wp_fedora_generate_post_type_urls($post_type);
        }
    }

    // Check if categories are globally disabled before including them
    if (!get_option('wp_fedora_disable_categories')) {
        $urlset .= wp_fedora_generate_term_urls('category', '0.2');
    }

    if (!get_option('wp_fedora_disable_tags')) {
        $urlset .= wp_fedora_generate_term_urls('post_tag', '0.2');
    }

    if (!get_option('wp_fedora_disable_authors')) {
        $urlset .= wp_fedora_generate_author_urls('0.2');
    }

    $urlset .= '</urlset>';
    return $urlset;
}

// Generate a URL block for a specific post type
function wp_fedora_generate_post_type_urls($post_type) {
    $url_blocks = [];
    $posts = get_posts(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'numberposts' => -1,
    ));

    // Force the options to be arrays to avoid errors
    $excluded_posts = (array) get_option('wp_fedora_excluded_posts', []);
    $excluded_pages = (array) get_option('wp_fedora_excluded_pages', []);
    $excluded_cpts = (array) get_option('wp_fedora_excluded_cpts', []);

    foreach ($posts as $post) {
        // Skip excluded CPT posts
        if (in_array($post->ID, $excluded_cpts)) {
            continue;
        }

        if (
            ($post_type == 'post' && in_array($post->ID, $excluded_posts)) ||
            ($post_type == 'page' && in_array($post->ID, $excluded_pages))
        ) {
            continue; // Skip excluded posts/pages
        }

        // Check if the post belongs to a disabled category
        $categories = wp_get_post_categories($post->ID);
        $excluded_categories = (array) get_option('wp_fedora_excluded_categories', []);
        if (array_intersect($categories, $excluded_categories)) {
            continue; // Skip the post if it belongs to a disabled category
        }

        $permalink = get_permalink($post->ID);
        $lastmod = get_post_modified_time('Y-m-d\TH:i:sP', true, $post->ID);
        $url_blocks[] = wp_fedora_generate_url_element($permalink, 'weekly', '0.8', $lastmod);
    }

    return implode(PHP_EOL, $url_blocks); // Return as string, not array
}

// Generate a URL block for taxonomy terms (categories, tags) with custom priority
function wp_fedora_generate_term_urls($taxonomy, $priority = '0.2') {
    $url_blocks = [];
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ));

    // Force the options to be arrays to avoid errors
    $excluded_categories = (array) get_option('wp_fedora_excluded_categories', []);

    // Skip categories if globally disabled
    if ($taxonomy == 'category' && get_option('wp_fedora_disable_categories')) {
        return ''; // Skip all categories if globally disabled
    }

    foreach ($terms as $term) {
        // Check specific exclusion for categories
        if ($taxonomy == 'category' && in_array($term->term_id, $excluded_categories)) {
            continue; // Skip excluded terms
        }

        $permalink = get_term_link($term);
        $url_blocks[] = wp_fedora_generate_url_element($permalink, 'monthly', $priority);
    }

    return implode(PHP_EOL, $url_blocks);
}

// Generate a URL block for authors with custom priority
function wp_fedora_generate_author_urls($priority = '0.2') {
    $url_blocks = [];
    $authors = get_users(array(
        'who' => 'authors',
        'has_published_posts' => true,
    ));

    // Force the options to be arrays to avoid errors
    $excluded_authors = (array) get_option('wp_fedora_excluded_authors', []);

    foreach ($authors as $author) {
        if (in_array($author->ID, $excluded_authors)) {
            continue; // Skip excluded authors
        }

        $permalink = get_author_posts_url($author->ID);
        $url_blocks[] = wp_fedora_generate_url_element($permalink, 'monthly', $priority);
    }

    return implode(PHP_EOL, $url_blocks);
}

// Helper function to generate a <url> element for the sitemap
function wp_fedora_generate_url_element($url, $changefreq = 'monthly', $priority = '0.5', $lastmod = null) {
    $lastmod = $lastmod ?: date('Y-m-d\TH:i:sP');
    $url_element = '    <url>' . PHP_EOL;
    $url_element .= '        <loc>' . esc_url($url) . '</loc>' . PHP_EOL;
    $url_element .= '        <lastmod>' . esc_html($lastmod) . '</lastmod>' . PHP_EOL;
    $url_element .= '        <changefreq>' . esc_html($changefreq) . '</changefreq>' . PHP_EOL;
    $url_element .= '        <priority>' . esc_html($priority) . '</priority>' . PHP_EOL;
    $url_element .= '    </url>' . PHP_EOL;

    return $url_element;
}

// Register settings for the sitemap settings page
function wp_fedora_sitemap_register_settings() {
    // Global Taxonomy Disabling section
    add_settings_section(
        'wp_fedora_sitemap_global_section', 
        'Global Taxonomy Disabling', 
        'wp_fedora_sitemap_global_section_callback', 
        'wp_fedora_sitemap_settings'
    );

    // Add the checkbox fields for each global setting
    add_settings_field(
        'wp_fedora_disable_pages', 
        'Disable Pages from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_pages')
    );

    add_settings_field(
        'wp_fedora_disable_posts', 
        'Disable Posts from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_posts')
    );

    add_settings_field(
        'wp_fedora_disable_cpts', 
        'Disable Custom Post Types from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_cpts')
    );

    add_settings_field(
        'wp_fedora_disable_categories', 
        'Disable Categories from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_categories')
    );

    add_settings_field(
        'wp_fedora_disable_tags', 
        'Disable Tags from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_tags')
    );

    add_settings_field(
        'wp_fedora_disable_authors', 
        'Disable Authors from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_global_section',
        array('option_name' => 'wp_fedora_disable_authors')
    );

    // Register the global settings
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_pages');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_posts');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_cpts');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_categories');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_tags');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_disable_authors');
    
    // Register individual exclusion settings
    wp_fedora_sitemap_register_individual_settings();
}
add_action('admin_init', 'wp_fedora_sitemap_register_settings');

// Section explanation callback for global disabling
function wp_fedora_sitemap_global_section_callback() {
    echo '<p>Disable entire taxonomies from the sitemap. Select the items to be excluded.</p>';
}

// Checkbox field callback
function wp_fedora_sitemap_checkbox_callback($args) {
    $option = get_option($args['option_name']);
    echo '<input type="checkbox" name="' . esc_attr($args['option_name']) . '" value="1" ' . checked(1, $option, false) . ' />';
}

// Add a submenu page under Tools > Sitemap Settings
function wp_fedora_sitemap_add_settings_page() {
    add_submenu_page(
        'tools.php', // Parent slug
        'Sitemap Settings', // Page title
        'Sitemap Settings', // Menu title
        'manage_options', // Capability
        'wp-fedora-sitemap-settings', // Menu slug
        'wp_fedora_sitemap_settings_page' // Callback function
    );
}
add_action('admin_menu', 'wp_fedora_sitemap_add_settings_page');

// Settings page callback
function wp_fedora_sitemap_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP Fedora Sitemap Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_fedora_sitemap_settings_group');
            do_settings_sections('wp_fedora_sitemap_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register individual exclusion settings
function wp_fedora_sitemap_register_individual_settings() {
    // Register settings section for exclusions
    add_settings_section(
        'wp_fedora_sitemap_exclusions_section',
        'Exclude Specific Items',
        'wp_fedora_sitemap_exclusions_section_callback',
        'wp_fedora_sitemap_settings'
    );

    // Register fields for selecting specific posts/pages to exclude
    add_settings_field(
        'wp_fedora_excluded_posts',
        'Exclude Specific Posts',
        'wp_fedora_sitemap_excluded_posts_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );
    add_settings_field(
        'wp_fedora_excluded_pages',
        'Exclude Specific Pages',
        'wp_fedora_sitemap_excluded_pages_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );
    
    // Register fields for CPTs, tags, categories, and authors
    add_settings_field(
        'wp_fedora_excluded_cpts',
        'Exclude Specific Custom Post Type Posts',
        'wp_fedora_sitemap_excluded_cpts_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );
    add_settings_field(
        'wp_fedora_excluded_categories',
        'Exclude Specific Categories',
        'wp_fedora_sitemap_excluded_categories_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );
    add_settings_field(
        'wp_fedora_excluded_tags',
        'Exclude Specific Tags',
        'wp_fedora_sitemap_excluded_tags_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );
    add_settings_field(
        'wp_fedora_excluded_authors',
        'Exclude Specific Authors',
        'wp_fedora_sitemap_excluded_authors_callback',
        'wp_fedora_sitemap_settings',
        'wp_fedora_sitemap_exclusions_section'
    );

    // Register settings to save these fields
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_posts');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_pages');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_cpts');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_categories');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_tags');
    register_setting('wp_fedora_sitemap_settings_group', 'wp_fedora_excluded_authors');
}

// Section explanation callback for exclusions
function wp_fedora_sitemap_exclusions_section_callback() {
    echo '<p>Select specific items to exclude from the sitemap.</p>';
}

// Exclude specific posts
function wp_fedora_sitemap_excluded_posts_callback() {
    $excluded_posts = (array) get_option('wp_fedora_excluded_posts', []);
    $posts = get_posts(['numberposts' => -1, 'post_type' => 'post']);

    foreach ($posts as $post) {
        $checked = in_array($post->ID, $excluded_posts) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_fedora_excluded_posts[]" value="' . esc_attr($post->ID) . '" ' . $checked . ' />';
        echo '<label>' . esc_html($post->post_title) . '</label><br>';
    }
}

// Exclude specific pages
function wp_fedora_sitemap_excluded_pages_callback() {
    $excluded_pages = (array) get_option('wp_fedora_excluded_pages', []);
    $pages = get_posts(['numberposts' => -1, 'post_type' => 'page']);

    foreach ($pages as $page) {
        $checked = in_array($page->ID, $excluded_pages) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_fedora_excluded_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . ' />';
        echo '<label>' . esc_html($page->post_title) . '</label><br>';
    }
}

// Exclude specific CPT posts
function wp_fedora_sitemap_excluded_cpts_callback() {
    $excluded_cpts = (array) get_option('wp_fedora_excluded_cpts', []);
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

    foreach ($post_types as $post_type) {
        echo '<h3>' . esc_html($post_type->label) . '</h3>';
        $posts = get_posts(['numberposts' => -1, 'post_type' => $post_type->name]);

        foreach ($posts as $post) {
            $checked = in_array($post->ID, $excluded_cpts) ? 'checked' : '';
            echo '<input type="checkbox" name="wp_fedora_excluded_cpts[]" value="' . esc_attr($post->ID) . '" ' . $checked . ' />';
            echo '<label>' . esc_html($post->post_title) . '</label><br>';
        }
    }
}

// Exclude specific categories
function wp_fedora_sitemap_excluded_categories_callback() {
    $excluded_categories = (array) get_option('wp_fedora_excluded_categories', []);
    $categories = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);

    foreach ($categories as $category) {
        $checked = in_array($category->term_id, $excluded_categories) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_fedora_excluded_categories[]" value="' . esc_attr($category->term_id) . '" ' . $checked . ' />';
        echo '<label>' . esc_html($category->name) . '</label><br>';
    }
}

// Exclude specific tags
function wp_fedora_sitemap_excluded_tags_callback() {
    $excluded_tags = (array) get_option('wp_fedora_excluded_tags', []);
    $tags = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => false]);

    foreach ($tags as $tag) {
        $checked = in_array($tag->term_id, $excluded_tags) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_fedora_excluded_tags[]" value="' . esc_attr($tag->term_id) . '" ' . $checked . ' />';
        echo '<label>' . esc_html($tag->name) . '</label><br>';
    }
}

// Exclude specific authors
function wp_fedora_sitemap_excluded_authors_callback() {
    $excluded_authors = (array) get_option('wp_fedora_excluded_authors', []);
    $authors = get_users(['who' => 'authors', 'has_published_posts' => true]);

    foreach ($authors as $author) {
        $checked = in_array($author->ID, $excluded_authors) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_fedora_excluded_authors[]" value="' . esc_attr($author->ID) . '" ' . $checked . ' />';
        echo '<label>' . esc_html($author->display_name) . '</label><br>';
    }
}
