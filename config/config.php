<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

$config['api_settings'] = array(
    'api_key' => array(
        'display_name' => 'API Key',
        'value' => '',
    ),
    'pull_url' => array(
        'display_name' => 'Pull URL',
        'value' => '',
    ),
    'push_url' => array(
        'display_name' => 'Push URL',
        'value' => '',
    ),
    'org_id' => array(
        'display_name' => 'Org ID',
        'value' => '',
    ),
    'npr_pull_post_type' => array(
        'display_name' => 'NPR Pull Post Type',
        'value' => '',
    ),
    'npr_push_post_type' => array(
        'display_name' => 'NPR Push Post Type',
        'value' => '',
    ),
    'npr_permissions' => array(
        'display_name' => 'NPR Permissions',
        'value' => 'You have no Permission Groups defined with the NPR API.',
    ),
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