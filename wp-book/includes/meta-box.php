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
    global $wpdb;
    $table_name = $wpdb->prefix . 'bookmeta';
    
    $book_data = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE book_id = %d", $post->ID)
    );
    
    $author = isset($book_data->author) ? $book_data->author : '';
    $price = isset($book_data->price) ? $book_data->price : '';
    $publisher = isset($book_data->publisher) ? $book_data->publisher : '';

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
    <?php
}

add_action('save_post', 'wp_book_save_meta_box_data');


function wp_book_save_meta_box_data($post_id) {

    if (!isset($_POST['wp_book_meta_box_nonce']) || !wp_verify_nonce($_POST['wp_book_meta_box_nonce'], 'wp_book_save_meta_box_data')) {
        return $post_id;
    }


    if ('book' !== get_post_type($post_id)) {
        return $post_id;
    }

    if (isset($_POST['book_author']) && isset($_POST['book_price']) && isset($_POST['book_publisher'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookmeta';
        

        $author = sanitize_text_field($_POST['book_author']);
        $price = sanitize_text_field($_POST['book_price']);
        $publisher = sanitize_text_field($_POST['book_publisher']);

        $existing_data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE book_id = %d", $post_id)
        );
        
        if ($existing_data) {
            $wpdb->update(
                $table_name,
                array(
                    'author' => $author,
                    'price' => $price,
                    'publisher' => $publisher
                ),
                array('book_id' => $post_id),
                array('%s', '%s', '%s'),  
                array('%d')                
            );
        } else {

            $wpdb->insert(
                $table_name,
                array(
                    'book_id'  => $post_id,
                    'author'   => $author,
                    'price'    => $price,
                    'publisher'=> $publisher
                ),
                array('%d', '%s', '%s', '%s')  
            );
        }
    }

    return $post_id;
}
