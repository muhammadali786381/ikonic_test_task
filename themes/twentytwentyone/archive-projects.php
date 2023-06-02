<?php
/**
 * Template Name: Projects Archive
 */
get_header();
?>
<style>

/* CSS */
.post-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-gap: 20px;
  border-radius:15px;
  margin:2%;
}

.post-item {
  background-color: #f1f1f1;
  padding: 20px;
}

.post-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
}

.post-content {
  font-size: 14px;
}

/* Responsive styles */
@media (max-width: 768px) {
  .post-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .post-grid {
    grid-template-columns: 1fr;
  }
}

</style>
<?php
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args = array(
    'post_type'      => 'projects',
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
    'posts_per_page' => 6
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    echo '<div class="post-grid">';

    while ( $query->have_posts() ) {
        $query->the_post();
        ?>
        <div class="post-item">
            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="post-content">
                <?php //the_content(); ?>
            </div>
        </div>
        <?php
    }
    
    echo '</div>';

    // Pagination
    $total_pages = $query->max_num_pages;

    if ( $total_pages > 1 ) {
        $current_page = max( 1, get_query_var( 'paged' ) );

        echo '<div class="pagination">';

        echo paginate_links( array(
            'base'      => get_pagenum_link( 1 ) . '%_%',
            'format'    => '/page/%#%',
            'current'   => $current_page,
            'total'     => $total_pages,
            'prev_text' => __( '&laquo; Previous' ),
            'next_text' => __( 'Next &raquo;' ),
        ) );

        echo '</div>';
    }
} else {
    // No posts found
    echo 'No posts found.';
}

get_footer();
?>