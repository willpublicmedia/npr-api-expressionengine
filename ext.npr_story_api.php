<?php

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Publishing\Npr_api_expressionengine;

class Npr_story_api_ext {
    private $field_ids = array(
        'npr_story_id' => NULL,
        'channel_entry_source' => NULL
    );

    private $field_data_tables = array();
    
    private $fields = array();

    private $required_extensions = array(
        'query_api' => 'before_channel_entry_save',
        'delete_story' => 'before_channel_entry_delete'
    );

    public $version;

    function __construct() {
        $addon = ee('Addon')->get('npr_story_api');
        $this->version = $addon->getVersion();
        $this->settings = $this->load_settings();
        $this->map_model_fields(array_keys($this->field_ids));
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

    public function delete_story($entry, $values) {
        if (ee()->input->post('bulk_action') !== 'remove')
        {
            return;
        }

        $entry_source = $this->load_entry_source($entry->entry_id);
        $is_external_story = $this->check_external_story_source($entry_source);

        if (!$is_external_story)
        {
            return;
        }

        $npr_story_id = $this->get_npr_story_id($entry->entry_id);
        $this->delete_npr_story($npr_story_id);
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

        // WARNING: story pull executes loop. Title may be an array.
        $title = $this->pull_npr_story($npr_story_id);
        $values = $this->change_entry_title($title, $values);

        $entry->title = $values['title'];
        $entry->url_title = $values['url_title'];
        
        // TODO: use correct inverse model
        // Save twice to store entry id.
        $story = ee('Model')->get('npr_story_api:Npr_story')->filter('id', $npr_story_id)->first();
        $story->entry_id = $entry->entry_id;
        $story->save();
    }

    private function change_entry_title($queried_title, $entry_values) {
        if (isset($queried_title[0])) {
            $queried_title = $queried_title[0];
        }
    
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

    private function delete_npr_story($npr_story_id)
    {
        $story = ee('Model')->get('npr_story_api:Npr_story')
            ->filter('id', $npr_story_id)
            ->first();
        
        if ($story === NULL)
        {
            return;
        }

        $story->delete();
    }

    private function get_npr_story_id($entry_id)
    {
        $npr_story_id = ee('Model')->get('npr_story_api:Npr_story')
            ->filter('entry_id', $entry_id)
            ->fields('id')
            ->first()
            ->id;

        // $id_field = $this->fields['npr_story_id'];
        // // field query should work as load_entry_source(), but doesn't.
        // $data = ee()->db->select($this->fields['npr_story_id'])
        //     ->from($this->field_data_tables['npr_story_id'])
        //     ->where('entry_id', $entry_id)
        //     ->limit(1)
        //     ->get()
        //     ->result_array();
        
        // $npr_story_id = isset($data[0]) ? $data[0][$id_field] : NULL;
        
        return $npr_story_id;
    }

    private function load_entry_source($entry_id)
    {
        $source = ee('Model')->get('ChannelEntry')
            ->filter('entry_id', $entry_id)
            ->fields($this->fields['channel_entry_source'])
            ->first();
        
        return $source;
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
        $field_ids = array();
        $field_names = array();
        $field_data_tables = array();
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

            $field_ids[$model_field] = $field_id;
            $field_names[$model_field] = "field_id_{$field_id}";
            $field_data_tables[$model_field] = "channel_data_field_{$field_id}";
        }

        $this->field_ids = $field_ids;
        $this->fields = $field_names;
        $this->field_data_tables = $field_data_tables;
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