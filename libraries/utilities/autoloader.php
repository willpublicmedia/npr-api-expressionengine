<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Utilities;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Autoloader
{
    public function load_dir($directory)
    {
        foreach(glob($directory . '/*.php') as $file) {
            require_once($file);
        }
    }
}