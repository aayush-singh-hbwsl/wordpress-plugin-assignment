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
    wp_book_post_type_register();
    wp_book_hierarchical_taxonomy();
    flush_rewrite_rules();
}

add_action('init', 'wp_book_post_type_register');

function wp_book_post_type_register(){
    $labels = array(
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'menu_name'          => 'Books',
        'name_admin_bar'     => 'Book',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Book',
        'new_item'           => 'New Book',
        'edit_item'          => 'Edit Book',
        'view_item'          => 'View Book',
        'all_items'          => 'All Books',
        'search_items'       => 'Search Books',
        'not_found'          => 'No books found.',
        'not_found_in_trash' => 'No books found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'books'),
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon'          => 'dashicons-book',
    );

    register_post_type('book', $args);
}

add_action('init', 'wp_book_hierarchical_taxonomy');

function wp_book_hierarchical_taxonomy(){
    $labels = array(
        'name'              => 'Book Categories',
        'singular_name'     => 'Book Category',
        'search_items'      => 'Search Book Categories',
        'all_items'         => 'All Book Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Book Category',
        'update_item'       => 'Update Book Category',
        'add_new_item'      => 'Add New Book Category',
        'new_item_name'     => 'New Book Category Name',
        'menu_name'         => 'Book Categories',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_in_rest'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'book-category'),
    );

    register_taxonomy('book_category', 'book', $args);
}







