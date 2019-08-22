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
            'field_id' => $field->field_id,
            'grid' => $definition['field_settings']['grid']
        );

        // Loader strips trailing slashes. Use path relative to Loader class.
		ee()->load->library('../../EllisLab/Addons/grid/libraries/Grid_lib.php');
        ee()->grid_lib->apply_settings($settings);
    }

    private function create_field($definition)
    {
        $name = $definition['field_name'];
        $field = ee('Model')->get('ChannelField')->filter('field_name', '==', $definition['field_name'])->first();
        
        if ($field == null)
        {
            $field = ee('Model')->make('ChannelField');
        }

        if ($definition['field_type'] === 'rte')
        {
            $definition['field_type'] = $this->use_preferred_rte($this->preferred_wysiwyg_editor);
        }
        
        $field->site_id = ee()->config->item('site_id');
        foreach ($definition as $key => $val)
        {
            if ($key === 'grid')
            {
                continue;
            }

            $field->{$key} = $val;
        }

        $field_group = $this->custom_field_group;
        $field->ChannelFieldGroups->add($field_group);

        $validation_result = $field->validate();
        if ($validation_result->isNotValid())
        {
            throw new \Exception("Field definition error. Could not create $field->field_name.");
        }

        $field->save();

        if ($definition['field_type'] === 'grid')
        {
            $this->add_grid_columns($definition, $field);
        }
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

    private function use_preferred_rte($editor_type_name)
    {
        return ee('Addon')->installed($editor_type_name) ? $editor_type_name : 'rte';
    }
}