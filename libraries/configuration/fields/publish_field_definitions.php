<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Publish_field_definitions
{
    public static $fields = array(
        'channel_entry_source' => array(
            'field_name' => 'channel_entry_source',
            'field_label' => 'Story Source',
            'field_instructions' => 'Import a story from NPR or create a story for export.',
            'field_type' => 'radio',
            'field_list_items' => '',
            'field_settings' => array(
                'value_label_pairs' => array(
                    'local' => 'Local',
                    'npr' => 'NPR'
                    )
                ),
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'npr_story_id' => array(
            'field_name' => 'npr_story_id',
            'field_label' => 'NPR Story ID',
            'field_instructions' => 'Enter an NPR story ID as found in https://api.npr.org.',
            'field_type' => 'text',
            'field_maxl' => '64',
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
        'publish_to_npr' => array(
            'field_name' => 'publish_to_npr',
            'field_label' => 'Publish to NPR',
            'field_instructions' => 'Enable to publish the story on the NPR API.',
            'field_type' => 'toggle',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_default_value' => 0
            )
        )
    );
}