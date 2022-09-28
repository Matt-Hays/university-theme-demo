<?php
get_header();

while (have_posts()) {
    the_post();
    page_banner();
?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> All Campuses
                </a>
                <span class="metabox__main">
                    <?php the_title(); ?>
                </span>
            </p>
        </div>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
        <?php $mapData = get_field('map_location'); ?>
            <div class="acf-map">
            <div class="marker" data-lat="<?php echo $mapData['lat']; ?>" data-lng="<?php echo $mapData['lng']; ?>">
                <h3>
                    <?php the_title(); ?>
                </h3>

                <?php echo $mapData['address']; ?>
            </div>
        </div>

        <?php
        $relatedProgramsQuery = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => 'program',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'related_campus',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"',
                )
            )
        ));

        if ($relatedProgramsQuery->have_posts()) {
        ?>

            <hr class="section-break">
            <h2 class="headline headline--medium">Programs Available at this Campus</h2>
            <ul class="link-list min-list">

                <?php

                while ($relatedProgramsQuery->have_posts()) {
                    $relatedProgramsQuery->the_post();
                ?>

                    <li>
                        <a href="<?php the_permalink() ?>">
                            <?php the_title(); ?>
                        </a>
                    </li>

                <?php
                }
                ?>

            </ul>
        <?php
        }
        // Allow for a second use of a custom query. 
        // Resets all data, e.g., the_post(); the_title(); the_permalink(); etc.
        wp_reset_postdata();
        ?>
    </div>
<?php
}

get_footer();
?>