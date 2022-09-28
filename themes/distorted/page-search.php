<?php
get_header();

$parentPageId = wp_get_post_parent_id(get_the_ID());

while (have_posts()) {
    the_post();
    page_banner();
?>

    <div class="container container--narrow page-section">

        <!-- Child Page Breadcrumbs -->
        <?php
        if ($parentPageId) {
        ?>

            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_permalink($parentPageId); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($parentPageId); ?></a> <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>

        <?php
        }
        ?>

        <!-- Page Sidebar Navigation -->
        <?php
        $childrenPages = get_pages(array('child_of' => get_the_ID()));

        if ($parentPageId or $childrenPages) {
        ?>

            <div class="page-links">
                <h2 class="page-links__title"><a href="<?php echo get_permalink($parentPageId) ?>"><?php echo get_the_title($parentPageId)  ?></a></h2>
                <ul class="min-list">
                    <?php wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => $parentPageId ? $parentPageId : get_the_ID(),
                        'sort_column' => 'menu_order'
                    )); ?>
                </ul>
            </div>

        <?php
        }
        ?>

        <div class="generic-content">
            <?php get_search_form(); ?>
        </div>
    </div>

<?php
}

get_footer();
?>