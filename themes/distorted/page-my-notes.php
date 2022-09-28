<?php
if (!is_user_logged_in()) {
    esc_url(wp_redirect(site_url('/')));
    exit;
}

get_header();

$parentPageId = wp_get_post_parent_id(get_the_ID());

while (have_posts()) {
    the_post();
    page_banner();
?>

    <div class="container container--narrow page-section">
        <ul class="min-list link-list" id="my-notes">
            <?php
            $userNotes = new WP_Query(array(
                'post_type' => 'note',
                'posts_per_page' => -1,
                'author' => get_current_user_id()
            ));

            while ($userNotes->have_posts()) {
                $userNotes->the_post();
            ?>
                <li>
                    <input class="note-title-field" value="<?php echo esc_attr(get_the_title()); ?>" type="text">
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                    <textarea class="note-body-field" name="" id="" cols="30" rows="10"><?php echo esc_attr(wp_strip_all_tags(get_the_content())); ?></textarea>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>

<?php
}

get_footer();
?>