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

    private $custom_field_group;
    
    private $field_definitions;
    
    private $preferred_wysiwyg_editor = 'wygwam';
    
    private $validation_errors;

    public function __construct()
    {
        $this->field_definitions = array(
            'source' => Story_source_definitions::$fields,
            'content' => Story_content_definitions::$fields
        );
        
        ee()->lang->loadfile('admin_content');
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

                    if ($model->field_type === $definition['field_type'])
                    {
                        $this->assign_field_group($model);
                        $this->notify_field_reuse($model->field_name, $model->field_type);
                    }
                    else
                    {
                        $this->warn_type_mismatch($model->field_name, $model->field_type, $definition['field_type']);
                    }
                    
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
            'settings_form_field_name' => 'grid',
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
            $this->store_validation_error($field->field_name, $validation_result);
            return;
        }
        
        $field->save();
        $this->assign_field_group($field);
        
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

    /**
	 * Loads Grid library and assigns relevant field information to it
	 */
	private function load_grid_lib($settings)
	{
        // Loader strips leading slashes. Use path relative to Loader class.
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
    
    private function notify_field_reuse($field_name, $field_type)
    {
        ee('CP/Alert')->makeInline("npr-api-field-creation-$field_name")
            ->asAttention()
            ->withTitle('NPR field creation notice.')
            ->addToBody(
                "A field with the name $field_name and type $field_type was found and assigned to the " . 
                Field_installer::DEFAULT_FIELD_GROUP_NAME . " group.")
            ->defer();
    }

    private function store_validation_error($field_name, $validation_result)
    {
        foreach ($validation_result->getAllErrors() as $key => $errors)
        {
            $alert = ee('CP/Alert')->makeInline("npr-api-field-creation-$field_name")
                ->asWarning()
                ->withTitle('NPR field creation warning.')
                ->addToBody("Could not create field named $field_name.");

            foreach ($errors as $message)
            {
                $alert->addToBody(lang($message));
            }
            
            $alert->defer();
        }
    }

    private function use_preferred_rte($editor_type_name)
    {
        return ee('Addon')->installed($editor_type_name) ? $editor_type_name : 'rte';
    }

    private function warn_type_mismatch($original_field_name, $original_field_type, $new_field_type)
    {
        ee('CP/Alert')->makeInline("npr-api-field-creation-$field_name")
            ->asWarning()
            ->withTitle('NPR field creation warning.')
            ->addToBody(
                "The $original_field_name field with type $new_field_type could not be reused or created because a field with the same name already exists with type $original_field_type.")
            ->defer();
    }
}