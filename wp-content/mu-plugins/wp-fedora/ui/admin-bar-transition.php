<?php
/*
Plugin Name: WP Fedora - Admin Bar Hover Transition (Screen Options and Help)
Description: Adds a fade-in and fade-out transition effect to the WordPress admin bar's "Screen Options" and "Help" buttons. They will be 100% invisible by default and will fade in on hover.
Version: 1.1
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Inline CSS for Screen Options and Help hover effect
function wp_fedora_admin_bar_transition_css() {
    if ( is_admin_bar_showing() ) {
        echo '<style>
            /* Target only the Screen Options and Help buttons */
            #screen-options-link-wrap, 
            #contextual-help-link-wrap {
                opacity: 0;
                transition: opacity 0.5s ease-in-out;
            }

            #screen-options-link-wrap:hover,
            #contextual-help-link-wrap:hover {
                opacity: 1;
            }

            /* Ensure the entire bar isn\'t affected, only the Screen Options and Help buttons */
            #wpadminbar {
                opacity: 1 !important;
            }
        </style>';
    }
}
add_action( 'admin_head', 'wp_fedora_admin_bar_transition_css' );
add_action( 'wp_head', 'wp_fedora_admin_bar_transition_css' );