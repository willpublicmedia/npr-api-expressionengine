<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Config_installer {
    private $central_config_dir = SYSPATH . '/user/config';

    private $filename = 'npr_story_api';

    public function install() {
        $this->ensure_core_file();
    }

    public function remove_config() {
        $config_dest = "{$this->central_config_dir}/{$this->filename}.php";
        if (file_exists($config_dest)) {
            unlink($config_dest);
        }
    }

    private function ensure_core_file() {
        $config_source = PATH_THIRD . '/npr_story_api/config/config.php';
        $config_dest = "{$this->central_config_dir}/{$this->filename}.php";
        
        if (!file_exists($config_dest)) {
            copy($config_source, $config_dest);
        }
    }
}