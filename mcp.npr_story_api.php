<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/libraries/security/permissions-checker.php';
require_once __DIR__ . '/libraries/configuration/config_form_builder.php';
require_once __DIR__ . '/libraries/validation/settings_validator.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Security\Permissions_checker;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Config_form_builder;
use IllinoisPublicMedia\NprStoryApi\Libraries\Validation\Settings_validator;

/**
 * NPR Story API control panel.
 */
class Npr_story_api_mcp
{
    private $api_settings = array();

    private $base_url;

    
    /**
     * NPR Story API control panel constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $permissions = new Permissions_checker();
        $permissions->check_permissions();

        $this->load_settings();
        $this->base_url = ee('CP/URL')->make('addons/settings/npr_story_api');
        ee()->load->helper('form');
    }

    
    /**
     * NPR Story API settings index.
     *
     * @return void
     */
    public function index()
    {
        $validation_results = null;
        if ( ! empty($_POST))
		{
            $validation_results = $this->process_form_data($_POST);
            
            if ($validation_results->isValid()) {
                $this->save_settings($_POST, 'npr_story_api_settings');
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

        $raw = array_pop($results);
        $raw['mapped_channels'] = explode("|", $raw['mapped_channels']);
        $settings = $raw;

        $this->api_settings = $settings;
    }
    
    private function process_form_data($form_data) {
        $rules = Settings_validator::API_SETTINGS_RULES;
        $result = ee('Validation')->make($rules)->validate($form_data);
        return $result;
    }

    private function require_npr_channel($channel_array)
    {
        $npr_channel_id = ee('Model')->get('Channel')
            ->filter('channel_name', 'npr_stories')
            ->fields('channel_id')
            ->first()
            ->channel_id;
        
        if (!in_array($npr_channel_id, array_values($channel_array)))
        {
            $channel_array[] = "$npr_channel_id";
        }

        return $channel_array;
    }

    private function save_settings($form_data, $table_name) {
        $changed = FALSE;

        $form_data['mapped_channels'] = $this->require_npr_channel($form_data['mapped_channels']);
        $form_data['mapped_channels'] = implode('|', array_values($form_data['mapped_channels']));
        
        foreach ($form_data as $key => $value) {
            if ($this->api_settings[$key] != $value) {
                $changed = TRUE;
                break;
            }
        }

        if ($changed == FALSE) {
            return;
        }

        $query = ee()->db->
            get($table_name)->
            result_array();
        $old_settings = array_pop($query);

        ee()->db->update($table_name, $form_data, array('id' => $old_settings['id']));
    }
}