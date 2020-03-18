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
        $this->field_utils = new Field_utilities();
    }
}