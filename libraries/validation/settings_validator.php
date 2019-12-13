<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Validation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * Tools for validating NPR Story API form data.
 */
class Settings_validator {
    /**
     * Default validation rules for NPR Story API settings.
     */
    public const API_SETTINGS_RULES = array(
        'api_key' => 'required|maxLength[64]|alphaNumeric',
        'pull_url' => 'url|maxLength[64]',
        'push_url' => 'url|maxLength[64]',
        'org_id' => 'maxLength[10]|alphaNumeric',
        'npr_permissions' => 'maxLength[256]|alphaNumeric',
    );

    /**
     * Validate form values.
     *
     * @param  mixed $data Form data.
     * @param  mixed $rules Validation rules.
     *
     * @return mixed Validation object.
     */
    public function validate($data, $rules) {
        $results = ee('Validation')->make($rules)->validate($data);
        return $results;
    }
}