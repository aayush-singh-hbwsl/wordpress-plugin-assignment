<?php
add_action('add_meta_boxes', 'wp_book_add_meta_box');

function wp_book_add_meta_box() {
    add_meta_box(
        'book_meta_box',
        'Book Details',
        'wp_book_meta_box_html',
        'book',
        'normal',
        'high'
    );
}

function wp_book_meta_box_html($post) {
    // Get metadata
    $author    = get_metadata('book', $post->ID, 'author', true);
    $price     = get_metadata('book', $post->ID, 'price', true);
    $publisher = get_metadata('book', $post->ID, 'publisher', true);
    $year      = get_metadata('book', $post->ID, 'year', true);
    $edition   = get_metadata('book', $post->ID, 'edition', true);
    $url       = get_metadata('book', $post->ID, 'url', true);

    wp_nonce_field('wp_book_save_meta_box_data', 'wp_book_meta_box_nonce');
    ?>
    <p>
        <label for="book_author">Author:</label>
        <input type="text" id="book_author" name="book_author" value="<?php echo esc_attr($author); ?>" class="widefat" />
    </p>
    <p>
        <label for="book_price">Price:</label>
        <input type="text" id="book_price" name="book_price" value="<?php echo esc_attr($price); ?>" class="widefat" />
    </p>
    <p>
        <label for="book_publisher">Publisher:</label>
        <input type="text" id="book_publisher" name="book_publisher" value="<?php echo esc_attr($publisher); ?>" class="widefat" />
    </p>
    <p>
        <label for="book_year">Year:</label>
        <input type="text" id="book_year" name="book_year" value="<?php echo esc_attr($year); ?>" class="widefat" />
    </p>
    <p>
        <label for="book_edition">Edition:</label>
        <input type="text" id="book_edition" name="book_edition" value="<?php echo esc_attr($edition); ?>" class="widefat" />
    </p>
    <p>
        <label for="book_url">URL:</label>
        <input type="url" id="book_url" name="book_url" value="<?php echo esc_attr($url); ?>" class="widefat" />
    </p>
    <?php
}

add_action('save_post', 'wp_book_save_meta_box_data');

function wp_book_save_meta_box_data($post_id) {
    // if (!isset($_POST['wp_book_meta_box_nonce']) || !wp_verify_nonce($_POST['wp_book_meta_box_nonce'], 'wp_book_save_meta_box_data')) {
    //     return;
    // }

    // if ('book' !== get_post_type($post_id)) {
    //     return;
    // }

    // // Update metadata
    // if (isset($_POST['book_author'])) {
    //     update_metadata('book', $post_id, 'author', sanitize_text_field($_POST['book_author']));
    // }

    // if (isset($_POST['book_price'])) {
    //     update_metadata('book', $post_id, 'price', sanitize_text_field($_POST['book_price']));
    // }

    // if (isset($_POST['book_publisher'])) {
    //     update_metadata('book', $post_id, 'publisher', sanitize_text_field($_POST['book_publisher']));
    // }

    // if (isset($_POST['book_year'])) {
    //     update_metadata('book', $post_id, 'year', sanitize_text_field($_POST['book_year']));
    // }

    // if (isset($_POST['book_edition'])) {
    //     update_metadata('book', $post_id, 'edition', sanitize_text_field($_POST['book_edition']));
    // }

    // if (isset($_POST['book_url'])) {
    //     update_metadata('book', $post_id, 'url', esc_url_raw($_POST['book_url']));
    // }
    if (!isset($_POST['wp_book_meta_box_nonce']) || !wp_verify_nonce($_POST['wp_book_meta_box_nonce'], 'wp_book_save_meta_box_data')) {
        return $post_id;
    }


    if ('book' !== get_post_type($post_id)) {
        return $post_id;
    }

    if (isset($_POST['book_author']) && isset($_POST['book_price']) && isset($_POST['book_publisher']) && isset($_POST['book_year']) && isset($_POST['book_edition']) && isset($_POST['book_url'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookmeta';
        

        $author = sanitize_text_field($_POST['book_author']);
        $price = sanitize_text_field($_POST['book_price']);
        $publisher = sanitize_text_field($_POST['book_publisher']);
        $year = sanitize_text_field($_POST['book_year']);
        $edition = sanitize_text_field($_POST['book_edition']);
        $url = sanitize_text_field($_POST['book_url']);

        $existing_data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE book_id = %d", $post_id)
        );
        
        if ($existing_data) {
            $wpdb->update(
                $table_name,
                array(
                    'author' => $author,
                    'price' => $price,
                    'publisher' => $publisher,
                    'year' => $year,
                    'edition' => $edition,
                    'url' => $url
                ),
                array('book_id' => $post_id),
                array('%s', '%s', '%s', '%s', '%s', '%s'),  
                array('%d')                
            );
        } else {

            $wpdb->insert(
                $table_name,
                array(
                    'book_id'  => $post_id,
                    'author'   => $author,
                    'price'    => $price,
                    'publisher'=> $publisher,
                    'year' => $year,
                    'edition' => $edition,
                    'url' => $url
                ),
                array('%d', '%s', '%s', '%s', '%s', '%s', '%s')  
            );
        }
    }

    return $post_id;
}
