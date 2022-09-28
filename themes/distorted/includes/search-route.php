<?php

add_action('rest_api_init', 'university_register_search');

// Defining a custom REST route
function university_register_search()
{
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'university_search_results'
    ));
}

// Custom Queries for Search Function
function university_search_results($data)
{
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'general_info' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();

        switch (get_post_type()) {
            case 'post':
            case 'page':
                array_push($results['general_info'], array(
                    'title' =>  get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'author_name' => get_the_author()
                ));
                break;

            case 'professor':
                array_push($results['professors'], array(
                    'title' =>  get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'image_url' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
                break;

            case 'program':
                $relatedCampuses = get_field('related_campus');
                if ($relatedCampuses) {
                    foreach ($relatedCampuses as $relatedCampus) {
                        array_push($results['campuses'], array(
                            'title' => get_the_title($relatedCampus),
                            'permalink' => get_the_permalink($relatedCampus),
                        ));
                    }
                }
                array_push($results['programs'], array(
                    'title' =>  get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'id' => get_the_ID()
                ));
                break;

            case 'campus':
                array_push($results['campuses'], array(
                    'title' =>  get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type()
                ));
                break;

            case 'event':
                $eventDate = new DateTime(get_field('event_date'));
                $eventDescr = null;
                has_excerpt() ? $eventDescr = get_the_excerpt() : $eventDescr = wp_trim_words(get_the_content(), 18);
                array_push($results['events'], array(
                    'title' =>  get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $eventDescr
                ));
                break;
        }
    }

    wp_reset_postdata();

    $metaQueryParams = array('relation' => 'OR');

    foreach ($results['programs'] as $result) {
        array_push($metaQueryParams, array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"' . $result['id'] . '"'
        ));
    }

    if ($results['programs']) {
        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $metaQueryParams
        ));

        while ($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            switch (get_post_type()) {
                case 'professor':
                    array_push($results['professors'], array(
                        'title' =>  get_the_title(),
                        'permalink' => get_the_permalink(),
                        'post_type' => get_post_type(),
                        'image_url' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                    break;

                case 'event':
                    $eventDate = new DateTime(get_field('event_date'));
                    $eventDescr = null;
                    has_excerpt() ? $eventDescr = get_the_excerpt() : $eventDescr = wp_trim_words(get_the_content(), 18);
                    array_push($results['events'], array(
                        'title' =>  get_the_title(),
                        'permalink' => get_the_permalink(),
                        'post_type' => get_post_type(),
                        'month' => $eventDate->format('M'),
                        'day' => $eventDate->format('d'),
                        'description' => $eventDescr
                    ));
                    break;
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    return $results;
}
