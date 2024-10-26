<?php
/*
Plugin Name: WP Fedora RSS Disabler
Description: Disables all RSS feeds and removes RSS feed links from the <head> section.
Version: 1.0
Author: WP Fedora
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Function to remove RSS feed links from the <head> section
function wp_fedora_disable_rss_feed_links() {
    // Remove RSS feed links
    remove_action( 'wp_head', 'feed_links', 2 ); // General feeds
    remove_action( 'wp_head', 'feed_links_extra', 3 ); // Extra feeds such as category feeds
}
add_action( 'init', 'wp_fedora_disable_rss_feed_links' );

// Function to disable the actual RSS feed requests
function wp_fedora_disable_rss_feeds() {
    wp_die( __( 'RSS feeds are disabled on this site. Please visit the <a href="' . esc_url( home_url( '/' ) ) . '">homepage</a>.' ) );
}

// Disable various types of feeds (RSS, Atom, etc.)
add_action( 'do_feed', 'wp_fedora_disable_rss_feeds', 1 );
add_action( 'do_feed_rdf', 'wp_fedora_disable_rss_feeds', 1 );
add_action( 'do_feed_rss', 'wp_fedora_disable_rss_feeds', 1 );
add_action( 'do_feed_rss2', 'wp_fedora_disable_rss_feeds', 1 );
add_action( 'do_feed_atom', 'wp_fedora_disable_rss_feeds', 1 );

// Also disable feeds for comments and other types
add_action( 'do_feed_rss2_comments', 'wp_fedora_disable_rss_feeds', 1 );
add_action( 'do_feed_atom_comments', 'wp_fedora_disable_rss_feeds', 1 );

// Optionally remove other feed-related headers (if any)
remove_action( 'wp_head', 'rsd_link' ); // RSD link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer