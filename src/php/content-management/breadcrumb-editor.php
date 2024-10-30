<?php
/*
Plugin Name: WP Fedora Breadcrumbs
Description: A breadcrumbs module for WP Fedora that includes schema injection, customization options, and optional CSS class.
Version: 1.3
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add settings page under Tools
function wp_fedora_breadcrumbs_add_settings_page() {
    add_submenu_page(
        'tools.php',
        'Breadcrumb Editor',         // Page title
        'Breadcrumb Editor',         // Menu title
        'manage_options',            // Capability required
        'wp-fedora-breadcrumbs',     // Menu slug
        'wp_fedora_breadcrumbs_settings_page' // Callback function
    );
}
add_action('admin_menu', 'wp_fedora_breadcrumbs_add_settings_page');

// Display settings page
function wp_fedora_breadcrumbs_settings_page() {
    // Handle form submission
    if (isset($_POST['wp_fedora_breadcrumbs_save_settings'])) {
        update_option('wp_fedora_breadcrumbs_delimiter', sanitize_text_field($_POST['wp_fedora_breadcrumbs_delimiter']));
        update_option('wp_fedora_breadcrumbs_home_label', sanitize_text_field($_POST['wp_fedora_breadcrumbs_home_label']));
        update_option('wp_fedora_breadcrumbs_disable_json_ld', isset($_POST['wp_fedora_breadcrumbs_disable_json_ld']) ? 'yes' : 'no');
        update_option('wp_fedora_breadcrumbs_disable_urls', isset($_POST['wp_fedora_breadcrumbs_disable_urls']) ? 'yes' : 'no');
        update_option('wp_fedora_breadcrumbs_style_as', sanitize_text_field($_POST['wp_fedora_breadcrumbs_style_as']));
        update_option('wp_fedora_breadcrumbs_style_bold', isset($_POST['wp_fedora_breadcrumbs_style_bold']) ? 'yes' : 'no');
        update_option('wp_fedora_breadcrumbs_style_italic', isset($_POST['wp_fedora_breadcrumbs_style_italic']) ? 'yes' : 'no');
        update_option('wp_fedora_breadcrumbs_style_underline', isset($_POST['wp_fedora_breadcrumbs_style_underline']) ? 'yes' : 'no');
        update_option('wp_fedora_breadcrumbs_class', sanitize_text_field($_POST['wp_fedora_breadcrumbs_class']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    $delimiter = get_option('wp_fedora_breadcrumbs_delimiter', '>');
    $home_label = get_option('wp_fedora_breadcrumbs_home_label', get_bloginfo('name'));
    $disable_json_ld = get_option('wp_fedora_breadcrumbs_disable_json_ld', 'no');
    $disable_urls = get_option('wp_fedora_breadcrumbs_disable_urls', 'no');
    $style_as = get_option('wp_fedora_breadcrumbs_style_as', 'p');
    $style_bold = get_option('wp_fedora_breadcrumbs_style_bold', 'no');
    $style_italic = get_option('wp_fedora_breadcrumbs_style_italic', 'no');
    $style_underline = get_option('wp_fedora_breadcrumbs_style_underline', 'no');
    $custom_class = get_option('wp_fedora_breadcrumbs_class', '');
    
    ?>
    <div class="wrap">
        <h1>WP Fedora Breadcrumbs Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Delimiter</th>
                    <td>
                        <select name="wp_fedora_breadcrumbs_delimiter">
                            <option value="|" <?php selected($delimiter, '|'); ?>> | (Pipe)</option>
                            <option value="&gt;" <?php selected($delimiter, '>'); ?>> > (Greater Than)</option>
                            <option value="&raquo;" <?php selected($delimiter, '»'); ?>> » (Double Arrow)</option>
                            <option value="&bull;" <?php selected($delimiter, '•'); ?>> • (Bullet)</option>
                            <option value="-" <?php selected($delimiter, '-'); ?>> - (Dash)</option>
                            <option value="/" <?php selected($delimiter, '/'); ?>> / (Slash)</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Home Label</th>
                    <td><input type="text" name="wp_fedora_breadcrumbs_home_label" value="<?php echo esc_attr($home_label); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Disable JSON LD</th>
                    <td><input type="checkbox" name="wp_fedora_breadcrumbs_disable_json_ld" <?php checked($disable_json_ld, 'yes'); ?> /> Disable JSON LD</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Disable Breadcrumb URLs</th>
                    <td><input type="checkbox" name="wp_fedora_breadcrumbs_disable_urls" <?php checked($disable_urls, 'yes'); ?> /> Disable URLs</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Specify Class</th>
                    <td><input type="text" name="wp_fedora_breadcrumbs_class" value="<?php echo esc_attr($custom_class); ?>" placeholder="Optional CSS class" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Style As</th>
                    <td>
                        <select name="wp_fedora_breadcrumbs_style_as">
                            <option value="p" <?php selected($style_as, 'p'); ?>>Style as p</option>
                            <option value="h1" <?php selected($style_as, 'h1'); ?>>Style as h1</option>
                            <option value="h2" <?php selected($style_as, 'h2'); ?>>Style as h2</option>
                            <option value="h3" <?php selected($style_as, 'h3'); ?>>Style as h3</option>
                            <option value="h4" <?php selected($style_as, 'h4'); ?>>Style as h4</option>
                            <option value="h5" <?php selected($style_as, 'h5'); ?>>Style as h5</option>
                            <option value="h6" <?php selected($style_as, 'h6'); ?>>Style as h6</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Additional Styles</th>
                    <td>
                        <input type="checkbox" name="wp_fedora_breadcrumbs_style_bold" <?php checked($style_bold, 'yes'); ?> /> Bold<br>
                        <input type="checkbox" name="wp_fedora_breadcrumbs_style_italic" <?php checked($style_italic, 'yes'); ?> /> Italicized<br>
                        <input type="checkbox" name="wp_fedora_breadcrumbs_style_underline" <?php checked($style_underline, 'yes'); ?> /> Underlined
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'wp_fedora_breadcrumbs_save_settings'); ?>
        </form>
    </div>
    <?php
}

// Generate breadcrumbs with schema
function wp_fedora_breadcrumbs_display() {
    $delimiter = get_option('wp_fedora_breadcrumbs_delimiter', '>');
    $home_label = get_option('wp_fedora_breadcrumbs_home_label', get_bloginfo('name'));
    $disable_json_ld = get_option('wp_fedora_breadcrumbs_disable_json_ld', 'no');
    $disable_urls = get_option('wp_fedora_breadcrumbs_disable_urls', 'no');
    $style_as = get_option('wp_fedora_breadcrumbs_style_as', 'p');
    $style_bold = get_option('wp_fedora_breadcrumbs_style_bold', 'no') === 'yes';
    $style_italic = get_option('wp_fedora_breadcrumbs_style_italic', 'no') === 'yes';
    $style_underline = get_option('wp_fedora_breadcrumbs_style_underline', 'no') === 'yes';
    $custom_class = get_option('wp_fedora_breadcrumbs_class', '');

    $style_tags_open = '';
    $style_tags_close = '';
    if ($style_bold) { $style_tags_open .= '<strong>'; $style_tags_close = '</strong>' . $style_tags_close; }
    if ($style_italic) { $style_tags_open .= '<em>'; $style_tags_close = '</em>' . $style_tags_close; }
    if ($style_underline) { $style_tags_open .= '<u>'; $style_tags_close = '</u>' . $style_tags_close; }

    // Append custom class if provided
    $class_attribute = $custom_class ? ' class="wp-fedora-breadcrumbs ' . esc_attr($custom_class) . '"' : ' class="wp-fedora-breadcrumbs"';

    // Start building breadcrumbs with custom HTML tags and styles
    $breadcrumbs = "<{$style_as}{$class_attribute}>";
    if (!is_front_page()) {
        if ($disable_urls === 'yes') {
            $breadcrumbs .= $style_tags_open . esc_html($home_label) . $style_tags_close . ' ' . esc_html($delimiter) . ' ';
        } else {
            $breadcrumbs .= $style_tags_open . '<a href="' . home_url() . '">' . esc_html($home_label) . '</a>' . ' ' . esc_html($delimiter) . ' ' . $style_tags_close;
        }
        
        if (is_category() || is_single()) {
            $categories = get_the_category();
            if ($categories) {
                if ($disable_urls === 'yes') {
                    $breadcrumbs .= $style_tags_open . esc_html($categories[0]->name) . $style_tags_close . ' ' . esc_html($delimiter) . ' ';
                } else {
                    $breadcrumbs .= $style_tags_open . '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>' . ' ' . esc_html($delimiter) . ' ' . $style_tags_close;
                }
            }
            if (is_single()) {
                $breadcrumbs .= $style_tags_open . '<span>' . get_the_title() . '</span>' . $style_tags_close;
            }
        } elseif (is_page()) {
            global $post;
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                $crumbs = [];
                while ($parent_id) {
                    $page = get_page($parent_id);
                    if ($disable_urls === 'yes') {
                        $crumbs[] = $style_tags_open . esc_html(get_the_title($page->ID)) . $style_tags_close;
                    } else {
                        $crumbs[] = $style_tags_open . '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>' . $style_tags_close;
                    }
                    $parent_id = $page->post_parent;
                }
                $crumbs = array_reverse($crumbs);
                foreach ($crumbs as $crumb) {
                    $breadcrumbs .= $crumb . ' ' . esc_html($delimiter) . ' ';
                }
            }
            $breadcrumbs .= $style_tags_open . '<span>' . get_the_title() . '</span>' . $style_tags_close;
        }
    }
    $breadcrumbs .= "</{$style_as}>";

    // Inject JSON-LD schema if enabled
    if ($disable_json_ld !== 'yes') {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [],
        ];
        $position = 1;
        $schema['itemListElement'][] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => esc_html($home_label),
            "item" => home_url(),
        ];
        if (is_category() || is_single()) {
            $categories = get_the_category();
            if ($categories) {
                $schema['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $position++,
                    "name" => esc_html($categories[0]->name),
                    "item" => esc_url(get_category_link($categories[0]->term_id)),
                ];
            }
            if (is_single()) {
                $schema['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $position,
                    "name" => get_the_title(),
                    "item" => get_permalink(),
                ];
            }
        } elseif (is_page()) {
            global $post;
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $schema['itemListElement'][] = [
                        "@type" => "ListItem",
                        "position" => $position++,
                        "name" => get_the_title($page->ID),
                        "item" => get_permalink($page->ID),
                    ];
                    $parent_id = $page->post_parent;
                }
            }
            $schema['itemListElement'][] = [
              "@type" => "ListItem",
                "position" => $position,
                "name" => get_the_title(),
                "item" => get_permalink(),
            ];
        }  

        $breadcrumbs .= '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }

    return $breadcrumbs;
}
add_shortcode('fedora_breadcrumbs', 'wp_fedora_breadcrumbs_display');
