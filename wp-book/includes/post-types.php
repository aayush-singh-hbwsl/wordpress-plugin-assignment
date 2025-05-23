<?php

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

add_action('pre_get_posts', 'wp_book_modify_books_archive_query', 1);

function wp_book_modify_books_archive_query($query) {
    
    if (
        !is_admin() &&
        $query->is_main_query() &&
        is_post_type_archive('book')
    ) {
        // echo get_option('wp_book_per_page')  ;
        echo get_option('posts_per_page')  ;
        $per_page = get_option('wp_book_per_page', 10);

        // echo $per_page;
        // $query->set('posts_per_page', intval($per_page));
        $query->set('posts_per_page', 1);
        echo $query->get('posts_per_page');
        echo get_option('posts_per_page')  ;
    }
}