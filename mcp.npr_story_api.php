<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/libraries/security/permissions-checker.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Security\Permissions_checker;

class Npr_story_api_mcp
{
    private $api_settings = array();

    private $base_url;

    public function __construct()
    {
        $permissions = new Permissions_checker();
        $permissions->check_permissions();

        $this->load_settings();
        $this->base_url = ee('CP/URL')->make('addons/settings/npr_story_api');
        ee()->load->helper('form');
    }

    public function index()
    {
        $data = array(
            'settings' => $this->api_settings
        );

        return ee('View')->make('npr_story_api:index')->render($data);
    }

    private function load_settings()
    {
        $settings = ee('Config')->get("npr_story_api:config.api_settings");
        $this->api_settings = $settings;
    }

    private function validate_server($server)
    {
        return filter_var($server, FILTER_VALIDATE_URL);
    }
}