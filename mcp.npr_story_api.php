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
            'base_url' => $this->base_url,
            'cp_page_title' => 'NPR Story API Settings',
            'save_btn_text' => 'Save Settings',
            'save_btn_text_working' => 'Saving...',
            'sections' => array(
                array(
                    array(
                        'title' => 'API Key',
                        'fields' => array(
                            'api_key' => array(
                                'type' => 'text',
                                'value' => '',
                                'required' => TRUE
                            )
                        )
                    ),
                    array(
                        'title' => 'Pull URL',
                        'fields' => array(
                            'pull_url' => array(
                                'type' => 'text',
                                'value' => ''
                            )
                        )
                    ),
                    array(
                        'title' => 'Push URL',
                        'fields' => array(
                            'push_url' => array(
                                'type' => 'text',
                                'value' => ''
                            )
                        )
                    ),
                    array(
                        'title' => 'Org ID',
                        'fields' => array(
                            'org_id' => array(
                                'type' => 'text',
                                'value' => ''
                            )
                        )
                    ),
                    array(
                        'title' => 'NPR Pull Post Type',
                        'fields' => array(
                            'npr_pull_post_type' => array(
                                'type' => 'text',
                                'value' => ''
                            )
                        )
                    ),
                    array(
                        'title' => 'NPR Push Post Type',
                        'fields' => array(
                            'npr_push_post_type' => array(
                                'type' => 'text',
                                'value' => ''
                            )
                        )
                    )
                )
            )
        );

        return ee('View')->make('ee:_shared/form')->render($data);
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