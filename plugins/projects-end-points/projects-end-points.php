<?php
/*
Plugin Name: Projects End Point
Description: Retrieves architecture projects using Ajax.
Version: 1.0
Author: Muhammad Ali
*/

defined('ABSPATH') or die('Access lock');

// Enqueue JavaScript file
function projects_enqueue_scripts() {
    wp_enqueue_script( 'projects-script', plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'projects-script', 'architectureProjects', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'action'  => 'projects_ajax_endpoint',
    ) );
}


// Ajax endpoint to retrieve projects
add_action( 'wp_ajax_projects_ajax_endpoint', 'projects_ajax_endpoint' );
add_action( 'wp_ajax_nopriv_projects_ajax_endpoint', 'projects_ajax_endpoint' );

function projects_ajax_endpoint() {
    // Check if user is logged in
    $user = wp_get_current_user();
    $is_logged_in = $user->exists();
    $number_of_projects=0;
    // Set the number of projects to retrieve based on user login status
    if($is_logged_in){
        $number_of_projects=6;
    }else{
        $number_of_projects=3;
    }
   

    // Set the projects type and query parameters
    $project_type = 'architecture';
    $args = array(
        'post_type'      => 'projects',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => $number_of_projects,
        'tax_query'      => array(
            array(
                'taxonomy' => 'project_type',
                'field'    => 'slug',
                'terms'    => $project_type,
            ),
        ),
    );

    // Retrieve the projects
    $query = new WP_Query( $args );

    $projects = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $project_id = get_the_ID();
            $project_title = get_the_title();
            $project_link = get_permalink();

            $projects[] = array(
                'id'    => $project_id,
                'title' => $project_title,
                'link'  => $project_link,
            );
        }
    }

    // Reset post data
    wp_reset_postdata();

    // Prepare the response
    $response = array(
        'success' => true,
        'data'    => $projects,
    );

    // Return the JSON response
    wp_send_json( $response );
}


// Shortcode for displaying the plugin output
function architecture_projects_shortcode( $atts ) {
    ob_start(); 

    ?>
    <div id="architecture-projects-output">
    <code>
        <?php
        projects_ajax_endpoint();
        ?>
    <code>
    </div>
    <?php

    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'architecture_projects_json', 'architecture_projects_shortcode' );



// Admin menu hook
function architecture_projects_plugin_admin_menu() {
    add_menu_page(
        'Architecture Projects json',
        'Architecture Projects',
        'manage_options',
        'architecture-projects',
        'architecture_projects_admin_page',
        'dashicons-rest-api',
        80
    );
}
add_action( 'admin_menu', 'architecture_projects_plugin_admin_menu' );

// Admin page callback
function architecture_projects_admin_page() {
    ?>
    <div class="wrap">
        <h1>Projects Architecture Json</h1>
        <p>Please Copy and Paste Below short to display output</p>
        <code> [architecture_projects_json] </code>
        <br><br>
        <p>End Point For Json Data</p>
        <code><?php echo get_site_url();?>/wp-json/projects-api/v1/architecture</code>
    </div>
    <?php
}


// load rest-api 
require_once plugin_dir_path( __FILE__ ) . 'rest-api.php';
