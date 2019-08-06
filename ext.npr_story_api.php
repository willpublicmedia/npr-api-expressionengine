<?php

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Publishing\Npr_api_expressionengine;

class Npr_story_api_ext {
    private $fields = array(
        'npr_story_id' => NULL,
        'channel_entry_source' => NULL
    );
    
    private $required_extensions = array(
        'query_api' => 'before_channel_entry_save',
    );

    public $version;

    function __construct() {
        $addon = ee('Addon')->get('npr_story_api');
        $this->version = $addon->getVersion();
        $this->settings = $this->load_settings();
        $this->map_model_fields(array_keys($this->fields));
    }

    public function activate_extension() {
        if (ee('Model')->get('Extension')->filter('class', __CLASS__)->count() > 0) {
            return;
        }

        foreach ($this->required_extensions as $method => $hook) {
            $data = array(
                'class' => __CLASS__,
                'method' => $method,
                'hook' => $hook,
                'priority' => 10,
                'version' => $this->version,
                'settings' => '',
                'enabled' => 'y'
            );
            
            ee('Model')->make('Extension', $data)->save();
        }
    }

    public function disable_extension() {
        ee('Model')->get('Extension')->filter('class', __CLASS__)->delete();
    }

    public function query_api($entry, $values) {
        $source_field = $this->fields['channel_entry_source'];
        $is_external_story = $this->check_external_story_source($values[$source_field]);

        // WARNING: check for push stories!
        if (!$is_external_story) {
            return;
        }

        $id_field = $this->fields['npr_story_id'];
        $npr_story_id = $values[$id_field];
        
        // WARNING: story pull executes loop. Story may be an array.
        $story = $this->pull_npr_story($npr_story_id);
        if (isset($story[0])) {
            $story = $story[0];
        }

        $values = $this->change_entry_title($story->title, $values);

        $entry->title = $values['title'];
        $entry->url_title = $values['url_title'];
        $story->ChannelEntry = $entry;
        $story->save();
    }

    private function change_entry_title($queried_title, $entry_values) {   
        if ($entry_values['title'] != $queried_title)
        {
            $entry_values['title'] = $queried_title;
            $entry_values['url_title'] = (string) ee('Format')->make('Text', $queried_title)->urlSlug();
        }
        
        return $entry_values;
    }

    private function check_external_story_source($story_source) {
        if ($story_source == NULL || $story_source == 'local') {
            return FALSE;
        }

        return TRUE;
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

    private function map_model_fields($field_array)
    {
        $field_names = array();
        foreach ($field_array as $model_field)
        {
            $field = ee('Model')->get('ChannelField')
            ->filter('field_name', $model_field)
            ->first();

            if ($field === NULL)
            {
                continue;
            }

            $field_id = $field->field_id;
            $field_names[$model_field] = "field_id_{$field_id}";
        }

        $this->fields = $field_names;
    }

    private function model_post_data()
    {
        $posted = array();
        foreach (array_keys($_POST) as $key)
        {
            $posted[$key] = ee()->input->post($key); 
        }

        $uri = explode("/", uri_string());
        $page = end($uri);
        reset($uri);

        $model;
        if (in_array("edit", $uri) && is_numeric($page))
        {
            $model = ee('Model')->get('ChannelEntry')
                ->filter('entry_id', $page)
                ->first();
        }
        else
        {
            $model = ee('Model')->make('ChannelEntry', $posted);
        }

        return $model;
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
        
        $stories = array();
        foreach ($api_service->stories as $story) {
            $stories[] = $api_service->save_clean_response($story);
        }

        return $stories;

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