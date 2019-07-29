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
        // WARNING: story pull executes loop. Title may be an array.
        $title = $this->pull_npr_story($npr_story_id);
        $this->change_entry_title($title);
    }

    private function change_entry_title($queried_title) {
        if (isset($queried_title[0])) {
            $queried_title = $queried_title[0];
        }
    
        // $entry_model = $this->model_post_data();
        $posted = array();
        foreach (array_keys($_POST) as $key)
        {
            $posted[$key] = ee()->input->post($key); 
        }

        $posted['title'] = $queried_title;

        // save npr story id as class field
        // check story already used
        // fetch or create model
        // check model title
        // update title
        // save model
        
        // $entry_model = ee('Model')->make('ChannelEntry', $posted);
        // $entry_model->title = $queried_title;
        // $entry_model->save();
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

    private function model_post_data()
    {
        $posted = array();
        foreach (array_keys($_POST) as $key)
        {
            $posted[$key] = ee()->input->post($key); 
        }

        return ee('Model')->make('ChannelEntry', $posted);
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
        
        $titles = array();
        foreach ($api_service->stories as $story) {
            $titles[] = $api_service->save_clean_response($story);
        }

        return $titles;

        // if (empty($api_service->message) || $api_service->message->level != 'warning') {
        //     $post_id = $api_service->update_posts_from_stories(/*entry_status*/);
        // }
        // if ( empty( $api->message ) || $api->message->level != 'warning') {
        //     $post_id = $api->update_posts_from_stories($publish);
        //     if ( ! empty( $post_id ) ) {
        //         //redirect to the edit page if we just updated one story
        //         $post_link = admin_url( 'post.php?action=edit&post=' . $post_id );
        //         wp_redirect( $post_link );
        //     }
        // } else {
        //     if ( empty($story) ) {
        //         $xml = simplexml_load_string( $api->xml );
        //         nprstory_show_message('Error retrieving story for id = ' . $story_id . '<br> API error ='.$api->message->id . '<br> API Message ='. $xml->message->text , TRUE);
        //         error_log('Not going to save the return from query for story_id='. $story_id .', we got an error='.$api->message->id. ' from the NPR Story API'); // debug use
        //         return;
        //     }
        // }
    }
}