<?php

add_action('admin_menu', 'wp_book_register_settings_page');

function wp_book_register_settings_page() {
    add_submenu_page(
        'edit.php?post_type=book',
        'Book Settings',
        'Settings',
        'manage_options',
        'wp_book_settings',
        'wp_book_settings_page_html'
    );
}

function wp_book_settings_page_html() {
    ?>
    <div class="wrap">
        <h1>Book Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('wp_book_settings_group');  
                do_settings_sections('wp_book_settings');    
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'wp_book_register_settings');

function wp_book_register_settings() {
    register_setting('wp_book_settings_group', 'wp_book_currency');
    register_setting('wp_book_settings_group', 'wp_book_per_page');

    add_settings_section(
        'wp_book_main_section',
        'Main Settings',
        null,
        'wp_book_settings'
    );

    add_settings_field(
        'wp_book_currency',
        'Currency',
        'wp_book_currency_field_html',
        'wp_book_settings',
        'wp_book_main_section'
    );

    add_settings_field(
        'wp_book_per_page',
        'Books Per Page',
        'wp_book_per_page_field_html',
        'wp_book_settings',
        'wp_book_main_section'
    );
}

function wp_book_currency_field_html() {
    $value = get_option('wp_book_currency', 'USD');
    ?>
    <input type="text" name="wp_book_currency" value="<?php echo esc_attr($value); ?>" />
    <p class="description">Enter the currency code (e.g., USD, EUR).</p>
    <?php
}

function wp_book_per_page_field_html() {
    $value = get_option('wp_book_per_page', 10);
    ?>
    <input type="number" name="wp_book_per_page" value="<?php echo esc_attr($value); ?>" min="1" />
    <p class="description">Number of books to display per page.</p>
    <?php
}
