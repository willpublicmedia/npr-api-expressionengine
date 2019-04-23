<?php

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Publishing\Npr_api_expressionengine;

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
        $this->settings = $this->load_settings();
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

    public function query_api() {
        $is_external_story = $this->check_external_story_source();

        // WARNING: check for push stories!
        if (!$is_external_story) {
            return;
        }

        $npr_story_id = $this->get_npr_story_id();
        $this->pull_npr_story($npr_story_id);
    }

    private function check_external_story_source() {
        $field_id = ee('Model')->get('ChannelField')
            ->filter('field_name', 'channel_entry_source')
            ->first()
            ->field_id;
        
        $story_source = ee()->input->post("field_id_{$field_id}");
        if ($story_source == NULL || $story_source == 'local') {
            return FALSE;
        }

        return TRUE;
    }

    private function get_npr_story_id() {
        $field_id = ee('Model')->get('ChannelField')
        ->filter('field_name', 'npr_story_id')
        ->first()
        ->field_id;
    
        $npr_story_id = ee()->input->post("field_id_{$field_id}", TRUE);

        return $npr_story_id;
    }

    private function load_settings() {
        $settings = ee()->db->select('*')
            ->from('npr_story_api_settings')
            ->get()
            ->result_array();

        if (isset($settings[0])) {
            $settings = $settings[0];
        }
        
        return $settings;
    }

    private function pull_npr_story($npr_story_id) {
        $api_key = isset($this->settings['api_key']) ? $this->settings['api_key'] : '';
        $params = array(
            'id' => $npr_story_id,
            'dateType' => 'story',
            'output' => 'NPRML',
            'apiKey' => $api_key
        );
        
        $pull_url = isset($this->settings['pull_url']) ? $this->settings['pull_url'] : null;
        
        $api_service = new Npr_api_expressionengine();
        $api_service->request($params, 'query', $pull_url);
        $api_service->parse();
        $print_r($api_service);
    }
}