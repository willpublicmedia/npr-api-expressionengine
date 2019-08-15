<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Field_installer {
    const DEFAULT_FIELD_GROUP_NAME = 'addon_fields';

    private $field_definitions = array(
        'channel_entry_source' => array(
            'field_name' => 'channel_entry_source',
            'field_label' => 'Story Source',
            'field_instructions' => 'Import a story from NPR or create a story for export.',
            'field_type' => 'radio',
            'field_list_items' => '',
            'field_settings' => array(
                'value_label_pairs' => array(
                    'local' => 'Local',
                    'npr' => 'NPR'
                    )
                ),
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'npr_story_id' => array(
            'field_name' => 'npr_story_id',
            'field_label' => 'NPR Story ID',
            'field_instructions' => 'Enter an NPR story ID as found in https://api.npr.org.',
            'field_type' => 'text',
            'field_maxl' => '64',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'publish_to_npr' => array(
            'field_name' => 'publish_to_npr',
            'field_label' => 'Publish to NPR',
            'field_instructions' => 'Enable to publish the story on the NPR API.',
            'field_type' => 'toggle',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_default_value' => 0
            )
        )
    );

    private $custom_field_group;

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