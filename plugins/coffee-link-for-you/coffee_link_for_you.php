<?php
/*
Plugin Name: Coffee Link for you
Description: Retrieves a direct link to a cup of coffee using the Random Coffee API.
Version: 1.0
Author: Muhammad Ali
*/

defined('ABSPATH') or die('Access lock');

// Enqueue plugin stylesheet
function coffee_plugin_enqueue_styles() {
    wp_enqueue_style('coffee-plugin-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'coffee_plugin_enqueue_styles');

// Register shortcode
function coffee_shortcode() {
    $coffee_link = hs_give_me_coffee();
    //get current page link for refresh
    $current_page = $_SERVER['REQUEST_URI'];

    $output= "";
    $output .= '<div class="coffee-container">';
    $output .= '<a href="' . $coffee_link . '"class="coffee-link" target="_blank">Get Coffee</a> <br><br>';
    $output .= '<a href="' . $current_page . '"class="refresh-button">Refresh to Get New</a>';
    $output .= '</div>';
    return $output;
}
add_shortcode('coffee', 'coffee_shortcode');

// Function to retrieve coffee link
function hs_give_me_coffee() {
    // Make an API request
    $response = wp_remote_get('https://coffee.alexflipnote.dev/random.json');

    if (is_wp_error($response)) {
        return 'Failed to fetch.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (is_null($data)) {
        return 'Failed to parse.';
    }

    $coffee_link = $data->file;

    return $coffee_link;
}


// Add options page to the admin menu
function coffee_options_page() {
    add_menu_page(
        'Coffee Plugin Settings',
        'Coffee Plugin',
        'manage_options',
        'coffee-plugin-settings',
        'coffee_settings_page',
        'dashicons-coffee'
    );
}
add_action('admin_menu', 'coffee_options_page');

// Render the options page
function coffee_settings_page() {
    ?>
    <div class="wrap">
        <h1>Coffee Plugin Settings</h1>
        <p>Use the shortcode <code>[coffee]</code> to display a link</p>
    </div>
    <?php
}
