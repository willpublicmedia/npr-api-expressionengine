<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/libraries/security/permissions-checker.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Security\Permissions_checker;

require_once __DIR__ . '/libraries/configuration/config_form_builder.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Config_form_builder;

require_once __DIR__ . '/libraries/validation/settings_validator.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Validation\Settings_validator;

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
        $validation_results = null;
        if ( ! empty($_POST))
		{
            $validation_results = $this->process_form_data($_POST);
            
            if ($validation_results->isValid()) {
                $this->save_settings($_POST);
            }
        }
        
        $builder = new Config_form_builder();
        $form_fields = $builder->build_api_settings_form($this->api_settings);
        $data = array(
            'base_url' => $this->base_url,
            'cp_page_title' => 'NPR Story API Settings',
            'errors' => $validation_results,
            'save_btn_text' => 'Save Settings',
            'save_btn_text_working' => 'Saving...',
            'sections' => $form_fields
        );

        return ee('View')->make('ee:_shared/form')->render($data);
    }

    private function load_settings()
    {
        $results = ee()->db->
            select('*')->
            from('npr_story_api_settings')->
            get()->
            result_array();

        $settings = array_pop($results);

        $this->api_settings = $settings;
    }
    
    private function process_form_data($form_data) {
        $rules = Settings_validator::API_SETTINGS_RULES;
        $result = ee('Validation')->make($rules)->validate($form_data);
        return $result;
    }

    private function save_settings($form_data) {
        $changed = FALSE;
        foreach ($form_data as $key => $value) {
            if ($this->api_settings[$key] != $value) {
                // ee('Config')->getFile('npr_story_api')->set("api_settings.{$key}", $value, TRUE);
                $changed = TRUE;
                break;
            }
        }

        // if ($changed) {
            // ee()->config->load('npr_story_api', TRUE);
            ee()->config->set_item('meat', 'pineapple');

            ee('Config')->getFile('npr_story_api')->set('pineapple', 'meat', TRUE);
            // ee()->config->set_item('api_settings', $form_data);
        // }
    }
}