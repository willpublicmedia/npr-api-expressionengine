<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

$config['api_settings'] = array(
    'api_key' => '',
    'pull_url' => '',
    'push_url' => '',
    'org_id' => '',
    'npr_pull_post_type' => '',
    'npr_push_post_type' => '',
    'npr_permissions' => 'You have no Permission Groups defined with the NPR API.'
);

$config['field_mappings'] = array(
    'custom_settings' => array(
        'display_name' => 'Custom Settings',
        'value' => ''
    ),
    'media_agency_field' => array(
        'display_name' => 'Media Agency Name',
        'value' => ''
    ),
    'media_credit_field' => array(
        'display_name' => 'Media Credit Field',
        'value' => ''
    ),
    'story_title' => array(
        'display_name' => 'Story Title',
        'value' => ''
    ),
    'story_body' => array(
        'display_name' => 'Story Body',
        'value' => ''
    ),
    'story_byline' => array(
        'display_name' => 'Story Byline',
        'value' => ''
    )
);