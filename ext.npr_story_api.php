<?php

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Npr_story_api_ext {
    private $query_extension = array(
        'class' => __CLASS__,
        'method' => 'query_api',
        'hook' => 'before_channel_entry_save',
        'priority' => 10,
        'version' => NULL,
        'settings' => '',
        'enabled' => 'y'
    );

    private $required_extensions = array(
        'query_extension'
    );

    public $version;

    function __construct() {
        $addon = ee('Addon')->get('npr_story_api');
        $this->version = $addon->getVersion();
    }

    public function activate_extension() {
        if (ee('Model')->get('Extension')->filter('class', __CLASS__)->count() > 0) {
            return;
        }

        foreach ($this->required_extensions as $name) {
            $data = $this->{$name};
            $data['version'] = $this->version;
            ee('Model')->make('Extension', $this->{$name})->save();
        }
    }

    public function disable_extension() {
        ee('Model')->get('Extension')->filter('class', __CLASS__)->delete();
    }

    public function query_api($data) {
        print_r("I'm querying!");
    }
}