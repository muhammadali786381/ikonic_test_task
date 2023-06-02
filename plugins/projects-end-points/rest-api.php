<?php
defined('ABSPATH') or die('Access lock');
// Register the custom endpoint
add_action( 'rest_api_init', 'projects_register_endpoint' );

function projects_register_endpoint()

 {
    register_rest_route(
        'projects-api/v1',
        '/architecture',
        array(
            'methods'  => 'GET',
            'callback' => 'architecture_projects_output',
        )
    );
}

function architecture_projects_output( $request ) {
    // Retrieve the projects
    $projects = retrieve_architecture_projects();

    // Prepare the JSON response
    $response = array(
        'success' => true,
        'data'    => $projects,
    );

    // Return the JSON response
    return new WP_REST_Response( $response, 200 );
}

// Retrieve the architecture projects
function retrieve_architecture_projects() {
    // Check if user is logged in
    $user = wp_get_current_user();
    $is_logged_in = $user->exists();

    // Set the number of projects to retrieve based on user login status
    $user = wp_get_current_user();
    $is_logged_in = $user->exists();
    $number_of_projects=0;

    // Check if user is logged in
    if($is_logged_in){
        $number_of_projects=6;
    }else{
        $number_of_projects=3;
    }
   

    // Set the project type and query parameters
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

    return $projects;
}
