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
        )
    );
}