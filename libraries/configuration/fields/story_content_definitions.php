<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Story_content_definitions
{
    public static $fields = array(
        'audio_runby_date' => array(
            'field_name' => 'audio_runby_date',
            'field_label' => 'Audio Run-By Date',
            'field_instructions' => 'Date by which audio should be aired.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'byline' => array(
            'field_name' => 'byline',
            'field_label' => 'Byline',
            'field_instructions' => 'A comma-separated list of story contributors.',
            'field_type' => 'text',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        /**
         *  'npr_audio' => $audio_array --> fluid
         *  'npr_corrections' => $corrections,
         *   'npr_html_assets' => $html_assets,
         *   'npr_images' => $images --> grid? model after channel images
         *   'npr_keywords' => $story->keywords --> text
         *   'npr_lastModifiedDate' => $story->lastModifiedDate --> date
         *   'npr_miniTeaser' => $story->miniTeaser --> text/text-area
         *   'npr_organization' => $org_array
         *   'npr_permalink' => $permalink --> url
         *   'npr_priorityKeywords' => $story->priorityKeywords --> text
         *   'npr_pubDate' => $story->pubDate --> date
         *   'npr_pullquotes' => $pullquotes
         *   'npr_shortTitle' => $story->shortTitle --> text
         *   'npr_slug' => $story->slug --> text
         *   'npr_subtitle' => $story->subtitle --> text
         *   'npr_storyDate' => $story->storyDate --> date
         *   'npr_teaser' => $story->teaser --> textarea
         *   'npr_text' => $text --> textarea
         *   'npr_thumbnails' => $thumbnail_array --> see images
         *   'npr_toenails' => $toenail_array --> see images
         */
    );
}