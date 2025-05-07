<?php
/**
 * Plugin Name: WP Book
 * Description: A plugin for custom post type - Book.
 * Version: 1.0
 * Author: Aayush Singh
 */

defined('ABSPATH') || exit;


require_once plugin_dir_path(__FILE__) . 'includes/post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-table.php';


register_activation_hook(__FILE__, 'wp_book_activate');
function wp_book_activate() {
    wp_book_post_type_register();
    wp_book_hierarchical_taxonomy();
    wp_book_non_hierarchical_taxonomy();
    flush_rewrite_rules();
}

