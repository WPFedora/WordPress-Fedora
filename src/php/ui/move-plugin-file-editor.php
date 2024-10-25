<?php
/*
Module Name: Move Plugin File Editor
Description: Moves the Plugin File Editor to the last option under the Plugins menu.
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Move the Plugin File Editor to the last position in the Plugins menu
function wp_fedora_move_plugin_editor_menu() {
    remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
    add_submenu_page( 'plugins.php', 'Plugin Editor', 'Plugin Editor', 'edit_plugins', 'plugin-editor.php', '', 100 );
}
add_action('admin_menu', 'wp_fedora_move_plugin_editor_menu', 999);
