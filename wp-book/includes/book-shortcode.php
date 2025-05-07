<?php
add_shortcode('book', 'wp_book_shortcode_handler');

function wp_book_shortcode_handler($atts) {
    $atts = shortcode_atts(array(
        'id'           => '',
        'author_name'  => '',
        'year'         => '',
        'category'     => '',
        'tag'          => '',
        'publisher'    => '',
    ), $atts, 'book');

    $args = array(
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    if (!empty($atts['id'])) {
        $args['p'] = intval($atts['id']);
    }

    $tax_query = array();

    if (!empty($atts['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['category']),
        );
    }

    if (!empty($atts['tag'])) {
        $tax_query[] = array(
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['tag']),
        );
    }

    if (!empty($atts['publisher'])) {
        $tax_query[] = array(
            'taxonomy' => 'publisher',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['publisher']),
        );
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $meta_query = array();

    if (!empty($atts['author_name'])) {
        $meta_query[] = array(
            'key'     => 'author',
            'value'   => sanitize_text_field($atts['author_name']),
            'compare' => 'LIKE',
        );
    }

    if (!empty($atts['year'])) {
        $meta_query[] = array(
            'key'     => 'year',
            'value'   => sanitize_text_field($atts['year']),
            'compare' => '=',
        );
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        global $wpdb;
        $table = $wpdb->prefix . 'bookmeta';

        $output .= '<div class="wp-book-shortcode-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $book_id = get_the_ID();

            $book_data = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table WHERE book_id = %d", $book_id)
            );
            // $year = $wpdb->get_var(
            //     $wpdb->prepare("SELECT meta_value FROM $table WHERE book_id = %d AND meta_key = %s", $book_id, 'year')
            // );

            $output .= '<div class="wp-book-item">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            // $output .= '<p>' . get_the_excerpt() . '</p>';
            $output .= '<p><strong>Author : </strong> ' . esc_html($book_data->author ?? '') . '</p>';
            $output .= '<p><strong>Year : </strong> ' . esc_html($book_data->year ?? '') . '</p>';
            $output .= '<p><strong>Price : </strong> ' . esc_html($book_data->price ?? '') . '</p>';
            $output .= '<p><strong>Publisher : </strong> ' . esc_html($book_data->publisher ?? '') . '</p>';
            $output .= '<p><strong>Edition : </strong> ' . esc_html($book_data->edition ?? '') . '</p>';
            $output .= '<p><strong>URL : </strong> ' . esc_html($book_data->url ?? '') . '</p>';
            $output .= '<hr>';
            $output .= '</div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output .= '<p>No books found.</p>';
    }

    return $output;
}
