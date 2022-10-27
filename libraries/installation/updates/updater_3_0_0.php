<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Updater_3_0_0
{
    private $fields = array(
        'delete_columns' => [
            'audio_files' => [
                'audio_type',
                'audio_duration',
                'audio_filesize',
                'audio_format',
                'audio_rights',
                'audio_region',
                'audio_rightsholder',
            ],
        ],
    );

    public function update(): bool
    {
        $operation_success = [];
        $delete = $this->fields['delete_columns'];

        foreach ($delete as $field => $columns) {
            $publish_columns_removed = $this->remove_publish_form_columns($field, $columns);
            $operation_success[] = $publish_columns_removed;
        }

        $success = (end($operation_success) === true && count(array_unique($operation_success)) === 1) ? true : false;
        return $success;
    }

    private function add_grid_columns($definition, $field)
    {
        $grid_type = $definition['field_type'];
        $settings = array(
            'content_type' => 'channel',
            'settings_form_field_name' => $grid_type,
            'field_id' => $field->field_id,
            'grid' => $definition['field_settings'][$grid_type],
        );

        $this->load_grid_lib($settings);
        ee()->grid_lib->apply_settings($settings);
    }

    /**
     * Loads Grid library and assigns relevant field information to it
     */
    private function load_grid_lib($settings)
    {
        // Loader strips leading slashes. Use path relative to Loader class.
        ee()->load->library('../../ExpressionEngine/Addons/grid/libraries/Grid_lib.php');

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
        ee()->grid_lib->in_modal_context = false;
        ee()->grid_lib->settings_form_field_name = 'grid';
    }

    private function remove_publish_form_columns(string $field_name, array $columns_to_delete): bool
    {
        $settings = [
            'defined' => [],
        ];

        $this->load_grid_lib($settings);

        $model = ee('Model')->get('ChannelField')->filter('field_name', $field_name)->fields('field_id', 'field_name')->first();

        if ($model === null) {
            return false;
        }

        $field_id = $model->field_id;
        $columns = ee()->grid_model->get_columns_for_field($field_id, 'channel', false);

        foreach ($columns as $column) {
            if (!in_array($column['col_name'], $columns_to_delete)) {
                continue;
            }

            ee()->grid_model->delete_columns($column['col_id'], $column['col_type'], $field_id, $column['content_type']);
        }

        return true;
    }

    private function log_message()
    {
        ee('CP/Alert')->makeInline('npr-column-update')
            ->asAttention()
            ->withTitle("NPR Data Fields Updated")
            ->addToBody('Removed audio_files columns: ' . implode(',', $this->fields['audio_files']['remove']))
            ->defer();
    }
}
