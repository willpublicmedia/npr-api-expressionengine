<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../vendor/autoload.php');
use \NPRMLElement;

class Nprml_mapper
{
    public function map($entry, $values)
    {
        throw new \Exception('not implemented');
    }
}