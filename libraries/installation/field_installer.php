<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once(__DIR__ . '/../configuration/fields/story_source_definitions.php');
require_once(__DIR__ . '/../configuration/fields/story_content_definitions.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields\Story_source_definitions as Story_source_definitions;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields\Story_content_definitions as Story_content_definitions;

class Field_installer {
    const DEFAULT_FIELD_GROUP_NAME = 'npr_story_api_fields';

    private $field_definitions;

    private $custom_field_group;

    private $preferred_wysiwyg_editor = 'wygwam';

    public function __construct()
    {
        $this->field_definitions = array(
            'source' => Story_source_definitions::$fields,
            'content' => Story_content_definitions::$fields
        );
    }

    public function install($field_group = self::DEFAULT_FIELD_GROUP_NAME) 
    {
        $this->custom_field_group = $this->load_field_group($field_group);

        foreach ($this->field_definitions as $type => $fields)
        {
            foreach ($fields as $name => $definition)
            {
                if (ee('Model')->get('ChannelField')->filter('field_name', $name)->count() > 0)
                {
                    $model = ee('Model')->get('ChannelField')->filter('field_name', $name)->first();
                    $this->custom_field_group->ChannelFields->add($model);
                    $this->custom_field_group->save();
                    continue;
                }
                
                $this->create_field($definition);
            }
        }
    }

    public function uninstall()
    {
        foreach ($this->field_definitions as $type => $fields)
        {
            foreach ($fields as $name => $definition)
            {
                $model = ee('Model')->get('ChannelField')->filter('field_name', '==', $name)->first();
                if ($model != null)
                {
                    $model->delete();
                }
            }
        }
    }

    private function add_grid_columns($definition, $field)
    {
        $settings = array(
            'content_type' => 'channel',
            'field_id' => $field->field_id,
            'grid' => $definition['field_settings']['grid']
        );

        $this->load_grid_lib($settings);
        ee()->grid_lib->apply_settings($settings);
    }

    private function assign_field_group($field)
    {
        $this->custom_field_group->ChannelFields->getAssociation()->add($field);
        $this->custom_field_group->save();
    }

    private function create_field($definition)
    {
        $name = $definition['field_name'];
        $field = ee('Model')->get('ChannelField')->filter('field_name', '==', $definition['field_name'])->first();

        if ($field != null)
        {
            $this->assign_field_group($field);
            return;
        }
            
        $field = ee('Model')->make('ChannelField');
        $field->site_id = ee()->config->item('site_id');
        
        if ($definition['field_type'] === 'rte')
        {
            $definition['field_type'] = $this->use_preferred_rte($this->preferred_wysiwyg_editor);
        }
        
        foreach ($definition as $key => $value)
        {
            if ($key === 'grid')
            {
                continue;
            }

            $field->{$key} = $value;
        }

        $validation_result = $field->validate();
        if ($validation_result->isNotValid())
        {
            throw new \Exception("Field definition error. Could not create $field->field_name.");
        }
        
        $field->save();
        $this->assign_field_group($field);
        
        // if ($definition['field_type'] === 'grid')
        // {
        //     $field->save();
        //     $this->add_grid_columns($definition, $field);
        //     $field = ee('Model')->get('ChannelField')->filter('field_id', $field->field_id)->first();
        // }
    }
    
    private function load_field_group($group_name)
    {
        $group = ee('Model')->get('ChannelFieldGroup')->filter('group_name', '==', $group_name)->first();
        if ($group == null)
        {
            $group = ee('Model')->make('ChannelFieldGroup');
            $group->group_name = $group_name;
            $group->site_id = ee()->config->item('site_id');
            $group->save();
        }
        
        return $group;
    }

    /**
	 * Loads Grid library and assigns relevant field information to it
	 */
	private function load_grid_lib($settings)
	{
        // Loader strips trailing slashes. Use path relative to Loader class.
        ee()->load->library('../../EllisLab/Addons/grid/libraries/Grid_lib.php');

		// Attempt to get an entry ID first
		$entry_id = (isset($settings['entry_id']))
            ? $settings['entry_id'] : 
            ee()->input->get_post('entry_id');

        // ee()->grid_lib->entry_id = ($this->content_id() == NULL) ? $entry_id : $this->content_id();
        ee()->grid_lib->entry_id = $entry_id;
		ee()->grid_lib->field_id = $settings['field_id'];
		ee()->grid_lib->field_name = $settings['field_name'];
		ee()->grid_lib->content_type = $settings['content_type'];
		ee()->grid_lib->fluid_field_data_id = (isset($settings['fluid_field_data_id'])) ? $settings['fluid_field_data_id'] : 0;
		ee()->grid_lib->in_modal_context = FALSE;
		ee()->grid_lib->settings_form_field_name = 'grid';
	}

    private function use_preferred_rte($editor_type_name)
    {
        return ee('Addon')->installed($editor_type_name) ? $editor_type_name : 'rte';
    }
}