<?php
namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../ext.npr_story_api.php');

class Extension_installer {
    private $required_extensions = array(
        'Npr_story_api_ext'
    );

    public function install() {
        foreach ($this->required_extensions as $name) {
            try {
                $class = '\\'.$name;
                $extension = new $class();
                $extension->activate_extension();
            } catch (Exception $err) {
                print_r($err);
            }
        }
    }

    public function uninstall() {
        foreach ($this->required_extensions as $name) {
            try {
                $class = '\\'.$name;
                $extension = new $class();
                $extension->disable_extension();
            } catch (Exception $err) {
                print_r($err);
            }
        }
    }
}