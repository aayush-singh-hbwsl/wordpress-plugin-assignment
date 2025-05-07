<?php
/**
 * Plugin Name: WP Book
 * Description: A plugin for custom post type - Book.
 * Version: 1.0
 * Author: Aayush Singh
 */

defined('ABSPATH') || exit;

register_activation_hook(__FILE__, 'wp_book_activate');
function wp_book_activate() {
    
    flush_rewrite_rules();
}



