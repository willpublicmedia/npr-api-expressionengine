<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Validation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Settings_validator {
    public const API_SETTINGS_RULES = array(
        'api_key' => 'required|maxLength[64]|alphaNumeric',
        'pull_url' => 'url|maxLength[64]',
        'push_url' => 'url|maxLength[64]',
        'org_id' => 'maxLength[10]|alphaNumeric',
        'npr_permissions' => 'maxLength[256]|alphaNumeric',
        'npr_pull_post_type' => 'alpha|maxLength[64]',
        'npr_push_post_type' => 'alpha|maxLength[64]'
    );

    public function validate($data, $rules) {
        $results = ee('Validation')->make($rules)->validate($data);
        return $results;
    }
}