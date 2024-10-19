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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WPFedora
{
  public $plugin;

  function __construct() {
    $this->plugin = plugin_basename(__FILE__);
  }

  function register() {
    add_action('admin_menu', array($this, 'add_admin_page'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
  }

  public function settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=wp_fedora">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }

  function enqueue_assets() {
    wp_enqueue_style( "$this->plugin-css", plugins_url('/assets/styles.css', __FILE__) );
    wp_enqueue_script( "$this->plugin-js", plugins_url('/assets/main.js', __FILE__), null, null, true );
    wp_enqueue_script( "$this->plugin-js", plugins_url('/assets/scripts.js', __FILE__), null, null, true );
  }

  public function add_admin_page() {
    add_menu_page("Vue WordPress", 'Vue WordPress', 'manage_options', 'wp_fedora', array($this, 'admin_index'), '');
  }

  public function admin_index() {
    require_once plugin_dir_path(__FILE__) . 'admin/index.php';
  }
}

if ( class_exists('WPFedora') ) {
  $WPFedora = new WPFedora();
  $WPFedora->register();
}

// Activation
require_once plugin_dir_path(__FILE__)  . 'includes/wp-fedora-activate.php';
register_activation_hook( __FILE__, array( 'WPFedoraActivate', 'activate' ) );

// Deactivation
require_once plugin_dir_path(__FILE__)  . 'includes/wp-fedora-deactivate.php';
register_deactivation_hook( __FILE__, array( 'WPFedoraDeactivate', 'deactivate' ) );