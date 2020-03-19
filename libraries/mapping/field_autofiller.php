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
        $audio_data = $this->field_utils->get_grid_values($entry, $field_name);
        throw new \Exception('not implemented');
    }

    public function autofill_image($field_name)
    {
        throw new \Exception('not implemented');
    }
}