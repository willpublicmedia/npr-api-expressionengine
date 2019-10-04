<?php

if (!defined('BASEPATH')) 
{
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/libraries/publishing/npr_api_expressionengine.php');
require_once(__DIR__ . '/libraries/mapping/nprml_mapper.php');
require_once(__DIR__ . '/libraries/mapping/publish_form_mapper.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Publishing\Npr_api_expressionengine;
use EllisLab\ExpressionEngine\Service\Validation\Result as ValidationResult;
use IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Nprml_mapper;
use IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Publish_form_mapper;

class Npr_story_api_ext 
{
    private $fields = array(
        'npr_story_id' => NULL,
        'channel_entry_source' => NULL,
        'overwrite_local_values' => NULL,
        'publish_to_npr' => NULL
    );
    
    private $required_extensions = array(
        'nprstory_api_delete' => array(
            'hook' => 'before_channel_entry_delete',
            'priority' => 10
        ),
        'push_to_api' => array(
            'hook' => 'before_channel_entry_save',
            'priority' => 15
        ),
        'query_api' => array(
            'hook' => 'before_channel_entry_save',
            'priority' => 10
        )
    );

    public $version;

    function __construct()
    {
        $addon = ee('Addon')->get('npr_story_api');
        $this->version = $addon->getVersion();
        $this->settings = $this->load_settings();
        $this->map_model_fields(array_keys($this->fields));
    }

    public function activate_extension()
    {
        if (ee('Model')->get('Extension')->filter('class', __CLASS__)->count() > 0)
        {
            return;
        }

        foreach ($this->required_extensions as $method => $settings)
        {
            $data = array(
                'class' => __CLASS__,
                'method' => $method,
                'hook' => $settings['hook'],
                'priority' => $settings['priority'],
                'version' => $this->version,
                'settings' => '',
                'enabled' => 'y'
            );
            
            ee('Model')->make('Extension', $data)->save();
        }
    }

    public function disable_extension()
    {
        ee('Model')->get('Extension')->filter('class', __CLASS__)->delete();
    }

    public function nprstory_api_delete($entry, $values)
    {
        $npr_story_id = $entry->{$this->fields['npr_story_id']};
        $source_field = $this->fields['channel_entry_source'];
        $was_pulled = $this->check_external_story_source($source_field);
        $was_pushed = !$was_pulled && $npr_story_id !== '';

        if ($was_pulled || !$was_pushed)
        {
            return;
        }

        $api = new Npr_api_expressionengine();
        $api->send_delete($npr_story_id);
    }

    public function push_to_api($entry, $values)
    {
        $push_field = $this->fields['publish_to_npr'];
        $push_story = $values[$push_field];

        if (!$push_story)
        {
            return;
        }

        $abort = false;
        $api_key = isset($this->settings['api_key']) ? $this->settings['api_key'] : '';
        if ($api_key === '')
        {
            $abort = true;
            ee('CP/Alert')->makeInline('story-push-api-key')
                ->asAlert()
                ->withTitle('NPR Stories')
                ->addToBody("No push url set. Can't push story.")
                ->defer();
        }

        $push_url = isset($this->settings['push_url']) ? $this->settings['push_url'] : null;
        if ($push_url === null)
        {
            $abort = true;
            ee('CP/Alert')->makeInline('story-push-push-url')
                ->asAlert()
                ->withTitle('NPR Stories')
                ->addToBody("No push url set. Can't push story.")
                ->defer();
        }

        if ($abort)
        {
            return;
        }

        // note pass-by-reference! entry date fields will be modified!
        $nprml = $this->create_nprml($entry, $values);
        
        $params = array(
            'orgId' => $this->settings['org_id'],
            'dateType' => 'story',
            'output' => 'NPRML',
            'apiKey' => $api_key,
            'body' => $nprml
        );

        // TODO: deduplicate request methods
        $api_service = new Npr_api_expressionengine();
        $api_service->request($params, 'story', $push_url, 'post');
        $npr_story_id = $api_service->process_push_response();

        // don't assign npr_story_id if entry already has one
        if ($entry->{$this->fields['npr_story_id']} === '')
        {
            $entry->{$this->fields['npr_story_id']} = $npr_story_id;
        }
        
        ee('CP/Alert')->makeInline('story-push')
            ->asSuccess()
            ->withTitle('NPR Stories')
            ->addToBody("Story pushed to NPR.")
            ->defer();
    }

    public function query_api($entry, $values)
    {
        $source_field = $this->fields['channel_entry_source'];
        $is_external_story = $this->check_external_story_source($values[$source_field]);
        $overwrite_field = $this->fields['overwrite_local_values'];
        $overwrite = $values[$overwrite_field];

        // WARNING: check for push stories!
        if (!$is_external_story || !$overwrite)
        {
            return;
        }

        $id_field = $this->fields['npr_story_id'];
        $npr_story_id = $values[$id_field];
        
        $result = $this->validate_story_id($entry, $values);
        if ($result instanceOf ValidationResult)
        {
            if ($result->isNotValid())
            {
                return $this->display_error($result);
            }
        }

        // WARNING: story pull executes loop. Story may be an array.
        $story = $this->pull_npr_story($npr_story_id);
        if (isset($story[0]))
        {
            $story = $story[0];
        }

        $objects = $this->map_story_values($entry, $values, $story);
        $story = $objects['story'];
        $values = $objects['values'];
        $entry = $objects['entry'];

        // Flip overwrite value
        $values[$overwrite_field] = FALSE;
        $entry->{$overwrite_field} = FALSE;

        $story->ChannelEntry = $entry;
        $story->save();
    }

    private function check_external_story_source($story_source)
    {
        if ($story_source == NULL || $story_source == 'local')
        {
            return FALSE;
        }

        return TRUE;
    }

    private function create_nprml($entry, $values)
    {
        $mapper = new Nprml_mapper();
        $nprml = $mapper->map($entry, $values);
        
        return $nprml;
    }

    private function display_error($errors)
    {
        foreach ($errors->getAllErrors() as $field => $results)
        {
            $alert = ee('CP/Alert')->makeInline('entries-form')
                ->asIssue()
                ->withTitle('NPR Story save error.');
            
            foreach ($results as $message)
            {
                $alert->addToBody($message);
            }
            
            $alert->defer();
        }
    }

    private function load_settings()
    {
        $settings = ee()->db->select('*')
            ->from('npr_story_api_settings')
            ->get()
            ->result_array();

        if (isset($settings[0]))
        {
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

    /**
     * @entry A ChannelEntry object.
     * @values Post values returned by the publish form.
     * @story An NPR Story object.
     */
    private function map_story_values($entry, $values, $story)
    {
        $mapper = new Publish_form_mapper();
        $objects = $mapper->map($entry, $values, $story);
        return $objects;
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

    private function pull_npr_story($npr_story_id)
    {
        $api_key = isset($this->settings['api_key']) ? $this->settings['api_key'] : '';
        if ($api_key === '')
        {
            throw new Configuration_exception('NPR API key not found. Configure key in NPR Story API module settings.');
        }

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
        foreach ($api_service->stories as $story)
        {
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

    private function validate_story_id($entry, $values)
    {
        $validator = ee('Validation')->make();
        $validator->defineRule('uniqueStoryId', function($key, $value, $parameters) use ($entry)
        {          
            $count = ee('Model')->get('npr_story_api:Npr_story')->filter('id', $value)->count();
            if ($count === 0)
            {
                return TRUE;
            }

            $owner_entry = ee()->db->select('entry_id')
                ->from('npr_story_api_stories')
                ->where('id', $value)
                ->limit(1)
                ->get()
                ->row('entry_id');

            if ($owner_entry === $entry->entry_id)
            {
                return TRUE;
            }

            return "An NPR story with ID $value has already been created. Content rejected.";
        });

        $validator->setRules(array(
            $this->fields['npr_story_id'] => 'uniqueStoryId'
        ));

        $result = $validator->validate($values);
        return $result;

        // $validator = ee('Validation')->make();
        // $validator->defineRule('uniqueStoryId', function($key, $value, $parameters)
        // {
        //     if (ee('Model')->get('npr_story_api:Npr_story')->filter('id', $value)->count() > 0)
        //     {
        //         return "An NPR story with ID $value has already been created. Content rejected.";
        //     }
        //     return TRUE;
        // });

        // $validator->setRules(array(
        //     $this->fields['npr_story_id'] => 'uniqueStoryId'
        // ));

        // $result = $validator->validate($values);
        // return $result;
    }
}