<?php

// Link includes files
require get_theme_file_path('/includes/search-route.php');

// Defining a custom REST API
function university_custom_rest()
{
    // Registering and returning data for rest key
    register_rest_field('post', 'author_name', array(
        'get_callback' => function () {
            return get_the_author();
        }
    ));

    // We can register additional REST fields following by repeating the same pattern.
}

// Defining a custom REST API
add_action('rest_api_init', 'university_custom_rest');

function page_banner($args = NULL)
{
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['image']) {
        if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
            $args['image'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['image'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }


?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['image']; ?>)">
        </div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php
}

function university_files()
{
    wp_enqueue_script('google-maps', '//maps.googleapis.com/maps/api/js?key=', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    // Ordering Matters! External -> External Dependencies -> Internal Main -> Internal Dependencies
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto:ital,wght@0,300;0,400;0,700;1,100;1,400;1,700&display=swap');
    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));


    // Allows us to output information to the html for use by exterior language APIs - see bottom of html in script tag outputs
    // We're using to make the paths in axios calls relative. 
    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url()
    ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
    // Register a user modifiable navigation menu.
    // register_nav_menu('headerMenuLocation', 'Header Menu Location');
    // register_nav_menu('footerLocationOne', 'Footer Menu Location 1');
    // register_nav_menu('footerLocationTwo', 'Footer Menu Location 2');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

// Register Custom Post Types
// See mu-plugins

// Override the default query for a specific domain.
function university_adjust_queries($query)
{
    if (!is_admin() and $query->is_main_query()) {
        if (is_post_type_archive('program')) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
        }
        // Pull in all posts for our campuses list. To ensure Google Maps captures all locations.
        if (is_post_type_archive('campus')) {
            $query->set('posts_per_page', -1);
        }
        if (is_post_type_archive('event')) {
            $today = date('Ymd');
            $query->set('meta_key', 'event_date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            $query->set('meta_query', array(
                array(
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric'
                )
            ));
        }
    }
}

// Attach to the getX() query hook pre-execution.
add_action('pre_get_posts', 'university_adjust_queries');


function university_map_key($api)
{
    $api['key'] = '';
    return $api;
}

add_filter('acf/fields/google_map/api', 'university_map_key');

// Redirect Subscriber accounts to Home Page on Login
function redirect_subscribers_to_home()
{
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
        wp_redirect(esc_url(site_url('/')));
        exit;
    }
}

add_action('admin_init', 'redirect_subscribers_to_home');

// Redirect Subscriber accounts to Home Page on Login
function no_admin_bar_subscriber()
{
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

add_action('wp_loaded', 'no_admin_bar_subscriber');

// Custom Login Screen
function university_header_url()
{
    return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'university_header_url');


// Override Login Page CSS Styles
function university_login_css()
{
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto:ital,wght@0,300;0,400;0,700;1,100;1,400;1,700&display=swap');
    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

add_action('login_enqueue_scripts', 'university_login_css');

// Override Login Page Header
function university_login_title()
{
    return get_bloginfo();
}

add_filter('login_headertitle', 'university_login_title');
