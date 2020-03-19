<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH'))
{
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../utilities/field_utils.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;

class Field_autofiller
{
    private $field_utils;

    public function __construct()
    {
        $this->field_utils = new Field_utils();
    }

    public function autofill_audio($field_name, $entry)
    {
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $audio_data = $this->field_utils->get_grid_values($entry, $field_name);

        foreach ($audio_data as $item)
        {
            $audio_data['audio_type'] = '';
            $audio_data['audio_duration'] = '';
            $audio_data['audio_filesize'] = '';
            $audio_data['audio_description'] = '';
            $audio_data['audio_format'] = '';
            $audio_data['audio_url'] = '';
            $audio_data['audio_rights'] = '';
            $audio_data['audio_permissions'] = '';
            $audio_data['audio_title'] = '';
            $audio_data['audio_region'] = '';
            $audio_data['audio_rightsholder'] = '';
        }

        
        throw new \Exception('not implemented');
    }

    public function autofill_image($field_name)
    {
        throw new \Exception('not implemented');
    }
}