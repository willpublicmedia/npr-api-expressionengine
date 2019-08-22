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
        'corrections' => array(
            'field_name' => 'corrections',
            'field_label' => 'Corrections',
            'field_instructions' => 'Information about corrections to the story. Not present if there are no corrections.',
            'field_type' => 'grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '',
                'allow_reorder' => 'y'
            )
        ),
        'keywords' => array(
            'field_name' => 'keywords',
            'field_label' => 'Keywords',
            'field_instructions' => 'A comma-delimited list of key terms describing the returned story. This field is seldom used for NPR.org.',
            'field_type' => 'text',
            'field_pre_populate' => 'n',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'last_modified_date' => array(
            'field_name' => 'last_modified_date',
            'field_label' => 'Last Modified Date',
            'field_instructions' => 'Date that a pulled story was last modified.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'mini_teaser' => array(
            'field_name' => 'mini_teaser',
            'field_label' => 'Mini Teaser',
            'field_instructions' => 'An abbreviated abstract for the returned story, describing what the story is about.',
            'field_type' => 'text',
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
        'permalink' => array(
            'field_name' => 'permalink',
            'field_label' => 'Permalink',
            'field_instructions' => "Permanent link to story on the story's original website",
            'field_type' => 'text',
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
        'priority_keywords' => array(
            'field_name' => 'priority_keywords',
            'field_label' => 'Priority Keywords',
            'field_instructions' => 'A comma-delimited list of key terms that are very closely tied to the returned story.',
            'field_type' => 'text',
            'field_pre_populate' => 'n',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'short_title' => array(
            'field_name' => 'short_title',
            'field_label' => 'Short Title',
            'field_instructions' => 'An abbreviated title, not to exceed 30 characters.',
            'field_type' => 'text',
            'field_maxl' => '30',
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
        'pub_date' => array(
            'field_name' => 'pub_date',
            'field_label' => 'Publication Date',
            'field_instructions' => 'Date that a pulled story was originally published.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'slug' => array(
            'field_name' => 'slug',
            'field_label' => 'Slug',
            'field_instructions' => "The main association for story, whether it is to a topic, series, column or some other list in the system.",
            'field_type' => 'text',
            'field_maxl' => '128',
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
        'subtitle' => array(
            'field_name' => 'subtitle',
            'field_label' => 'Subtitle',
            'field_instructions' => 'A short, sentence-like description of the story.',
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
        'story_date' => array(
            'field_name' => 'story_date',
            'field_label' => 'Story Date',
            'field_instructions' => "The primary date/time associated with the story's publication to NPR.org.",
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'teaser' => array(
            'field_name' => 'teaser',
            'field_label' => 'Teaser',
            'field_instructions' => 'The main abstract for the returned story, describing what the story is about.',
            'field_type' => 'textarea',
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
        'text' => array(
            'field_name' => 'text',
            'field_label' => 'Text',
            'field_instructions' => 'The full text of the story, complete with markup.',
            'field_type' => 'rte',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        /**
         *  'npr_audio' => $audio_array --> fluid
         *  'npr_corrections' => $corrections,
         *   'npr_html_assets' => $html_assets,
         *   'npr_images' => $images --> grid? model after channel images
         *   'npr_organization' => $org_array
         *   'npr_pullquotes' => $pullquotes
         *   'npr_thumbnails' => $thumbnail_array --> see images
         *   'npr_toenails' => $toenail_array --> see images
         */
    );
}