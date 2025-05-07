<?php

add_action('add_meta_boxes', 'wp_book_register_meta_box');

function wp_book_register_meta_box() {
    add_meta_box('wp_book_meta_box', 'Details for the Book', 'wp_book_render_meta_box', 'book', 'normal', 'high');
}

function wp_book_render_meta_box($post){
    wp_nonce_field('wp_book_save_meta_box', 'wp_book_meta_box_nonce');

    $author    = get_post_meta($post->ID, '_book_author', true);
    $price     = get_post_meta($post->ID, '_book_price', true);
    $publisher = get_post_meta($post->ID, '_book_publisher', true);
    $year      = get_post_meta($post->ID, '_book_year', true);
    $edition   = get_post_meta($post->ID, '_book_edition', true);
    $url       = get_post_meta($post->ID, '_book_url', true);
    ?>

    <p><label>Author Name:</label><br>
        <input type="text" name="book_author" value="<?php echo esc_attr($author); ?>" style="width:100%;">
    </p>

    <p><label>Price:</label><br>
        <input type="text" name="book_price" value="<?php echo esc_attr($price); ?>" style="width:100%;">
    </p>

    <p><label>Publisher:</label><br>
        <input type="text" name="book_publisher" value="<?php echo esc_attr($publisher); ?>" style="width:100%;">
    </p>

    <p><label>Year:</label><br>
        <input type="number" name="book_year" value="<?php echo esc_attr($year); ?>" style="width:100%;">
    </p>

    <p><label>Edition:</label><br>
        <input type="text" name="book_edition" value="<?php echo esc_attr($edition); ?>" style="width:100%;">
    </p>

    <p><label>Book URL:</label><br>
        <input type="url" name="book_url" value="<?php echo esc_attr($url); ?>" style="width:100%;">
    </p>

    <?php

}

add_action('save_post', 'wp_book_save_meta_box');

function wp_book_save_meta_box($post_id) {
    if (!isset($_POST['wp_book_meta_box_nonce']) || !wp_verify_nonce($_POST['wp_book_meta_box_nonce'], 'wp_book_save_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['book_author'])) {
        update_post_meta($post_id, '_book_author', sanitize_text_field($_POST['book_author']));
    }

    if (isset($_POST['book_price'])) {
        update_post_meta($post_id, '_book_price', sanitize_text_field($_POST['book_price']));
    }

    if (isset($_POST['book_publisher'])) {
        update_post_meta($post_id, '_book_publisher', sanitize_text_field($_POST['book_publisher']));
    }

    if (isset($_POST['book_year'])) {
        update_post_meta($post_id, '_book_year', intval($_POST['book_year']));
    }

    if (isset($_POST['book_edition'])) {
        update_post_meta($post_id, '_book_edition', sanitize_text_field($_POST['book_edition']));
    }

    if (isset($_POST['book_url'])) {
        update_post_meta($post_id, '_book_url', esc_url_raw($_POST['book_url']));
    }
}