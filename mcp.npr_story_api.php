<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Npr_story_api_mcp
{
    private $api_settings = array(
        'api_key' => '',
        'pull_url' => '',
        'push_url' => '',
        'org_id' => '',
        'npr_pull_post_type' => '',
        'npr_push_post_type' => '',
        'npr_permissions' => ''
    );

    private $base_url;

    public function __construct() {
        $this->base_url = ee('CP/URL')->make('addons/settings/npr_story_api');
        ee()->load->helper('form');
    }

    public function index() {
        $values = ee()->db->get('npr_story_api_settings');
        $settings = array(
            'settings' => $this->api_settings,
            'db_values' => $values
        );
        
        return ee('View')->make('npr_story_api:index')->render($settings);
    }

    private $post_types = array();
        
    private function validate_server($server) {
        return filter_var($server, FILTER_VALIDATE_URL);
    }
}