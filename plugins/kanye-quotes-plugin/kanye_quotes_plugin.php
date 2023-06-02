<?php
/*
Plugin Name: Kanye Quotes Plugin
Description: Displays Kanye West quotes using a shortcode.
Version: 1.0
Author: Muhammad Ali
*/

defined('ABSPATH') or die('Access lock');

// Register shortcode
function kanye_quotes_shortcode() {
    wp_enqueue_style('kanye-quotes-style', plugin_dir_url(__FILE__) . 'css/style.css');

    $output = '<div class="kanye-quotes-container">';
    $output .= '<ol>';

    for ($i = 0; $i < 5; $i++) {
        // Make an API request
        $response = wp_remote_get('https://api.kanye.rest/');

        if (is_wp_error($response)) {
            $output .= '<li class="kanye-quotes-item">Failed to fetch Kanye quotes.</li>';
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);

            if (is_null($data)) {
                $output .= '<li class="kanye-quotes-item">Failed to parse Kanye quotes.</li>';
            } else {
                $quote = $data->quote;
                $output .= '<li class="kanye-quotes-item">' . $quote . '</li>';
            }
        }
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
}


add_shortcode('kanye_quotes', 'kanye_quotes_shortcode');

// Add options page to the admin menu
function kanye_quotes_options_page() {
    add_menu_page(
        'Kanye Quotes Plugin Settings',
        'Kanye Quotes',
        'manage_options',
        'kanye-quotes-settings',
        'kanye_quotes_settings_page',
        'dashicons-format-quote'
    );
}
add_action('admin_menu', 'kanye_quotes_options_page');

// Render the options page
function kanye_quotes_settings_page() {
    ?>
    <div class="wrap">
        <h1>Kanye Quotes Plugin Settings</h1>
        <p>Use the shortcode <code>[kanye_quotes]</code> to display 5 quotes.</p>
    </div>
    <?php
}
