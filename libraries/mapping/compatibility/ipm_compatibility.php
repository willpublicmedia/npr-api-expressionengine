<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Compatibility;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Ipm_compatibility
{
    public function apply_cdata($text)
    {
        throw new \Exception('not implemented');
    }

    public function strip_tags($text)
    {
        return strip_tags($text);
    }
}