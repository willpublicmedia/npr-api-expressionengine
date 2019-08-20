<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once(__DIR__ . '/../configuration/fields/story_source_definitions.php');
require_once(__DIR__ . '/../configuration/fields/story_content_definitions.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields\Story_source_definitions;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields\Story_content_definitions;

class Field_installer {
    const DEFAULT_FIELD_GROUP_NAME = 'addon_fields';

    private $field_definitions;

    private $custom_field_group;

    public function __construct()
    {
        $field_definitions = Story_source_definitions::$fields;
    }

    public function install($field_group = self::DEFAULT_FIELD_GROUP_NAME) {
        $this->custom_field_group = $this->load_field_group($field_group);

        foreach ($this->field_definitions as $name => $definition) {
            if (ee('Model')->get('ChannelField')->filter('field_name', $name)->count() > 0) {
                continue;
            }
            
            $this->create_field($definition);
        }
    }

    public function uninstall() {
        foreach ($this->field_definitions as $name => $definition) {
            $model = ee('Model')->get('ChannelField')->filter('field_name', '==', $name)->first();
            if ($model != null) {
                $model->delete();
            }
        }
    }

    private function create_field($definition) {
        $name = $definition['field_name'];
        $field = ee('Model')->get('ChannelField')->filter('field_name', '==', $definition['field_name'])->first();
        
        if ($field == null) {
            $field = ee('Model')->make('ChannelField');
        }
        
        foreach ($definition as $key => $val) {
            $field->{$key} = $val;
        }
        
        $field->site_id = ee()->config->item('site_id');
        $field_group = $this->custom_field_group;
        $field->ChannelFieldGroups->add($field_group);
        
        $field->save();
        $field_group->save();

        $field = null;
    }
    
    private function load_field_group($group_name) {
        $group = ee('Model')->get('ChannelFieldGroup')->filter('group_name', '==', $group_name)->first();
        if ($group == null) {
            $group = ee('Model')->make('ChannelFieldGroup');
            $group->group_name = $group_name;
            $group->site_id = ee()->config->item('site_id');
            $group->save();
        }
        
        return $group;
    }
}