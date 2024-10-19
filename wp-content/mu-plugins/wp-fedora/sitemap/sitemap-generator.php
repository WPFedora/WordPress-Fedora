<?php
/*
Plugin Name: WP Fedora Sitemap Generator
Description: A sitemap generator module for WP Fedora that outputs an XML sitemap with XSL stylesheets and excludes Divi and Elementor-related URLs.
Version: 1.2
Author: WP Fedora
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
    $stylesheet_path = home_url('wp-content/mu-plugins/wp-fedora/sitemap/sitemap.xsl'); 
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

    foreach ($posts as $post) {
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

    foreach ($terms as $term) {
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

    foreach ($authors as $author) {
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

// Register settings in Reading Settings
function wp_fedora_sitemap_register_settings() {
    add_settings_section(
        'wp_fedora_sitemap_settings_section', 
        'WP Fedora Sitemap Settings', 
        'wp_fedora_sitemap_section_callback', 
        'reading' 
    );

    // Add the checkbox fields for each sitemap aspect
    add_settings_field(
        'wp_fedora_disable_pages', 
        'Disable Pages from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_pages')
    );

    add_settings_field(
        'wp_fedora_disable_posts', 
        'Disable Posts from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_posts')
    );

    add_settings_field(
        'wp_fedora_disable_cpts', 
        'Disable Custom Post Types from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_cpts')
    );

    add_settings_field(
        'wp_fedora_disable_categories', 
        'Disable Categories from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_categories')
    );

    add_settings_field(
        'wp_fedora_disable_tags', 
        'Disable Tags from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_tags')
    );

    add_settings_field(
        'wp_fedora_disable_authors', 
        'Disable Authors from Sitemap',
        'wp_fedora_sitemap_checkbox_callback',
        'reading',
        'wp_fedora_sitemap_settings_section',
        array('option_name' => 'wp_fedora_disable_authors')
    );

    // Register the settings
    register_setting('reading', 'wp_fedora_disable_pages');
    register_setting('reading', 'wp_fedora_disable_posts');
    register_setting('reading', 'wp_fedora_disable_cpts');
    register_setting('reading', 'wp_fedora_disable_categories');
    register_setting('reading', 'wp_fedora_disable_tags');
    register_setting('reading', 'wp_fedora_disable_authors');
}
add_action('admin_init', 'wp_fedora_sitemap_register_settings');

// Section explanation callback
function wp_fedora_sitemap_section_callback() {
    echo '<p>Manage which aspects of your sitemap are included.</p>';
}

// Checkbox field callback
function wp_fedora_sitemap_checkbox_callback($args) {
    $option = get_option($args['option_name']);
    echo '<input type="checkbox" name="' . esc_attr($args['option_name']) . '" value="1" ' . checked(1, $option, false) . ' />';
}

// Add a rewrite rule for the sitemap URL (root.com/sitemap.xml)
function wp_fedora_add_sitemap_rewrite_rule() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?sitemap=xml', 'top');
}
add_action('init', 'wp_fedora_add_sitemap_rewrite_rule');

// Flush rewrite rules for activation (since it's a mu-plugin, do this manually)
function wp_fedora_flush_rewrite_rules() {
    wp_fedora_add_sitemap_rewrite_rule();
    flush_rewrite_rules();
}
add_action('wp_loaded', 'wp_fedora_flush_rewrite_rules');

// Redirect default WordPress sitemap to the custom WP Fedora sitemap
function wp_fedora_redirect_default_sitemap() {
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-sitemap.xml') !== false) {
        wp_redirect(home_url('/sitemap.xml'), 301);
        exit;
    }
}
add_action('template_redirect', 'wp_fedora_redirect_default_sitemap');
