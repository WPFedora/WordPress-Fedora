<?php
/*
Module Name: Move Theme File Editor
Description: Moves the Theme File Editor to the last option under the Appearance menu.
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Move the Theme File Editor to the last position in the Appearance menu
function wp_fedora_move_theme_editor_menu() {
    remove_submenu_page( 'themes.php', 'theme-editor.php' );
    add_submenu_page( 'themes.php', 'Theme Editor', 'Theme Editor', 'edit_themes', 'theme-editor.php', '', 100 );
}
add_action('admin_menu', 'wp_fedora_move_theme_editor_menu', 999);
