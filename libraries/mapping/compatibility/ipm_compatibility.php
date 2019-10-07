<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Compatibility;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Ipm_compatibility
{
    public function apply_cdata($text)
    {
        $cdata_prefix = '&lt;![CDATA[';
        $cdata_suffix = ']]&gt;';
        
        return $cdata_prefix . $text . $cdata_suffix;
    }

    public function strip_tags($text)
    {
        return strip_tags($text);
    }
}