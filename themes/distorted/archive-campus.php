<?php
get_header();
page_banner(array(
    'title' => 'Our Campuses',
    'subtitle' => 'We have several conveniently located campuses.'
));
?>

<div class="container container--narrow page-section">
    <div class="acf-map">
        <?php
        while (have_posts()) {
            the_post();
            $mapData = get_field('map_location');
        ?>
            <div class="marker" data-lat="<?php echo $mapData['lat']; ?>" data-lng="<?php echo $mapData['lng']; ?>">

            </div>

        <?php
        }
        ?>
    </div>
</div>

<?php
get_footer();
?>