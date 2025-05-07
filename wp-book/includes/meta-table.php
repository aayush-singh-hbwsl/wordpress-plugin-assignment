<?php
// Hook to create custom meta table when the plugin is activated
register_activation_hook(__FILE__, 'wp_book_create_custom_meta_table');

function wp_book_create_custom_meta_table() {
    global $wpdb;
    
    $table = $wpdb->prefix . 'bookmeta';
    $charset_collate = $wpdb->get_charset_collate();

    // $sql = "CREATE TABLE $table (
    //     meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    //     book_id BIGINT(20) UNSIGNED NOT NULL,
    //     meta_key VARCHAR(255),
    //     meta_value LONGTEXT,
    //     author VARCHAR(255),
    //     price VARCHAR(255),
    //     publisher VARCHAR(255),
    //     PRIMARY KEY  (meta_id),
    //     KEY book_id (book_id),
    //     KEY meta_key (meta_key)
    // ) $charset_collate;";

    $sql = "CREATE TABLE $table (
        meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        book_id BIGINT(20) UNSIGNED NOT NULL,
        meta_key VARCHAR(255),
        meta_value LONGTEXT,
        author VARCHAR(255),
        price VARCHAR(255),
        publisher VARCHAR(255),
        year VARCHAR(20),
        edition VARCHAR(50),
        url TEXT,
        PRIMARY KEY  (meta_id),
        KEY book_id (book_id),
        KEY meta_key (meta_key)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Metadata filter functions
add_filter('get_metadata', 'wp_book_get_metadata', 10, 4);
add_filter('add_metadata', 'wp_book_add_metadata', 10, 5);
add_filter('update_metadata', 'wp_book_update_metadata', 10, 5);
add_filter('delete_metadata', 'wp_book_delete_metadata', 10, 4);

function wp_book_get_metadata($value, $object_id, $meta_key, $single) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookmeta';
    $query = $wpdb->prepare("SELECT meta_value FROM $table WHERE book_id = %d AND meta_key = %s", $object_id, $meta_key);
    $results = $wpdb->get_col($query);
    if ($single) {
        return isset($results[0]) ? maybe_unserialize($results[0]) : false;
    }
    return array_map('maybe_unserialize', $results);
}

function wp_book_add_metadata($check, $object_id, $meta_key, $meta_value, $unique) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookmeta';
    if ($unique) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE book_id = %d AND meta_key = %s",
            $object_id, $meta_key
        ));
        if ($exists) return false;
    }
    $wpdb->insert($table, [
        'book_id' => $object_id,
        'meta_key' => $meta_key,
        'meta_value' => maybe_serialize($meta_value)
    ]);
    return $wpdb->insert_id;
}

function wp_book_update_metadata($check, $object_id, $meta_key, $meta_value, $prev_value) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookmeta';
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE book_id = %d AND meta_key = %s",
        $object_id, $meta_key
    ));
    if ($exists) {
        return $wpdb->update(
            $table,
            ['meta_value' => maybe_serialize($meta_value)],
            ['book_id' => $object_id, 'meta_key' => $meta_key]
        );
    } else {
        return $wpdb->insert(
            $table,
            ['book_id' => $object_id, 'meta_key' => $meta_key, 'meta_value' => maybe_serialize($meta_value)]
        );
    }
}

function wp_book_delete_metadata($check, $object_id, $meta_key, $meta_value) {
    global $wpdb;
    $table = $wpdb->prefix . 'bookmeta';
    if (!empty($meta_value)) {
        return $wpdb->delete($table, [
            'book_id' => $object_id,
            'meta_key' => $meta_key,
            'meta_value' => maybe_serialize($meta_value)
        ]);
    }
    return $wpdb->delete($table, [
        'book_id' => $object_id,
        'meta_key' => $meta_key
    ]);
}
