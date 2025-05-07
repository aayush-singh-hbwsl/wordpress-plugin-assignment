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
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/book-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/block-book-sidebar.php';


register_activation_hook(__FILE__, 'wp_book_activate');
function wp_book_activate() {
    wp_book_post_type_register();
    wp_book_hierarchical_taxonomy();
    wp_book_non_hierarchical_taxonomy();
    flush_rewrite_rules();
}


add_action('wp_dashboard_setup', 'wpb_register_dashboard_widget');

function wpb_register_dashboard_widget() {
    wp_add_dashboard_widget(
        'wpb_top_categories_widget', 
        'Top 5 Book Categories',     
        'wpb_display_top_categories'
    );
}

function wpb_display_top_categories() {
    $terms = get_terms([
        'taxonomy'   => 'book_category',  
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 5,
        'hide_empty' => true,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        echo '<p>No book categories found.</p>';
        return;
    }

    echo '<ul>';
    foreach ($terms as $term) {
        echo '<li><strong>' . esc_html($term->name) . '</strong> — ' . esc_html($term->count) . ' books</li>';
    }
    echo '</ul>';
}


function loadMyBlock() {
    wp_enqueue_script(
      'my-new-block',
      plugin_dir_url(__FILE__) . 'test-block.js',
      array('wp-blocks','wp-editor'),
      true
    );
  }
     
  add_action('enqueue_block_editor_assets', 'loadMyBlock');

