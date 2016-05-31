<?php
add_shortcode('ph', 'placeholder_shortcode');

/**
 * The Gallery shortcode.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @param array $attr Attributes of the shortcode.
 * @return string HTML content to display gallery.
 */
function placeholder_shortcode($attr) {
    $post = get_post();

    static $index = 0;
    static $component_cache = null;


    if($component_cache === null) {
        $component_cache = array();
        $story_content = get_field('story_content', $post->ID);

        if($story_content !== false) {
            foreach($story_content as $content) {
                $layout = $content['acf_fc_layout'];
                $component = array('type' => $layout);
                $fun = 'component_' . $layout;

                if(function_exists($fun)) {
                    $component = call_user_func($fun, $content, $component);
                }

                $component_cache[] = $component;
            }
        }
    }

    $context = array();

    if($index < count($component_cache)) {
        $component = $component_cache[$index];
        if($component) {
            $context = $component;
        }
        $template = array('component-' . $component['type'] .'.twig', 'empty.twig');
    }
    else {
        $template = is_user_logged_in() ? 'component-extraneous.twig' : 'empty.twig';
    }

    // do not echo:
    ob_start();
    $output = Timber::render($template, $context);
    ob_end_clean();

    $index++;

    return $output;

}


function component_image($content, $component) {

    $component['image'] = new TimberImage($content['image']['id']);

    $component = array_merge($content['image'], $component);
    $component['credit'] = get_field('credit', $component['id']);
    $component['alt_text'] = get_field('_wp_attachment_image_alt', $component['id']);

    return $component;
}

function component_video($content, $component) {
    $component['video'] = new NationSwellVideo($content['video_url'], $content['title'], '', $content['credit']);
    $component['title'] = $content['title'];
    $component['credit'] = $content['credit'];

    return $component;
}


function component_pull_quote($content, $component) {
    $component['text'] = $content['text'];
    $component['credit'] = $content['credit'];

    return $component;
}

function component_related($content, $component) {
    $position = $content['position'];
    $sidebar = $position == 'sidebar';

    $component['position'] = $sidebar ? 'pull' : 'horizontal';

    $related_ids = $content['related'];

    if(count($related_ids) > 0) {
        $component['related_posts'] = Timber::get_posts($sidebar ? $related_ids : array_slice($related_ids, 0, 3));
        $post_count = count($component['related_posts']);

        $numbers = array(0 => '', 1 => 'one', 2 => 'two', 3 => 'three');
        $component['class'] = $sidebar ? '' : $numbers[$post_count];
    }

    return $component;
}

function component_list($content, $component) {
    $component['heading'] = $content['list_item_heading'];
    $component['body'] = $content['list_item_body'];
    $component['rank'] = $content['list_item_rank'];

    $component['mediaType'] = $content['list_item_media'][0]['acf_fc_layout'];

    if($component['mediaType'] == 'image') {
        $component['media'] = component_image($content['list_item_media'][0], $component);
    } elseif ($content['list_item_media'][0]['acf_fc_layout'] == 'video') {
        $component['media'] = component_video($content['list_item_media'][0], $component);
    }



    return $component;
}

function component_audio($content, $component){
    $component['audio_url'] = $content['audio'];

    return $component;
}
