<?php
get_header();
page_banner(array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of our past events.'
));
?>

<div class="container container--narrow page-section">
    <!-- Custom Query with Pagination -->
    <?php
    $today = date('Ymd');

    $pastEvents = new WP_Query(array(
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'compare' => '<',
                'value' => $today,
                'type' => 'numeric'
            )
        ),
        'paged' => get_query_var('paged', 1)
    ));

    while ($pastEvents->have_posts()) {
        $pastEvents->the_post();
        get_template_part('template-parts/content-event');
    }

    // Update the pagination logic since we're using a custom query and it is based on the default query.
    echo paginate_links(array(
        'total' => $pastEvents->max_num_pages
    ));
    ?>
</div>

<?php
get_footer();
?>