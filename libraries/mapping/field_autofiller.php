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
        $cols = $this->field_utils->get_grid_column_names($field_id);
        $audio = $entry->field_id_45;
        throw new \Exception('not implemented');
    }

    public function autofill_image($field_name)
    {
        throw new \Exception('not implemented');
    }

    
}