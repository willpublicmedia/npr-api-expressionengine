<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates;

require_once __DIR__ . '/../../configuration/tables/npr_story_table.php';

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Updater_3_0_0
{
    private $delete_columns = [
        'audio_files' => [
            'audio_type',
            'audio_filesize',
            'audio_format',
            'audio_rights',
            'audio_region',
            'audio_rightsholder',
        ],
    ];

    private $update_column_length = [
        // namespace slashes must be escaped
        'IllinoisPublicMedia\\NprStoryApi\\Libraries\\Configuration\\Tables\\npr_story_table' => [
            'slug',
            'shortTitle',
        ],
    ];

    public function __construct()
    {
        ee()->load->dbforge();
    }

    public function update(): bool
    {
        $operation_success = [];

        $delete = $this->delete_columns;
        foreach ($delete as $field => $columns) {
            $publish_columns_removed = $this->remove_publish_form_columns($field, $columns);
            $operation_success[] = $publish_columns_removed;
        }

        $update = $this->update_column_length;
        foreach ($update as $table_name => $columns) {
            foreach ($columns as $column_name) {
                $status = $this->update_column_length($table_name, $column_name);
                $operation_success[] = $status;
            }
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

        if (!$entry_id) {
            return;
        }

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

        foreach ($columns_to_delete as $delete_column) {
            foreach ($columns as $live_column) {
                if ($delete_column === $live_column['col_name']) {
                    ee()->grid_model->delete_columns($live_column['col_id'], $live_column['col_type'], $field_id, $live_column['content_type']);
                }
            }
        }

        // foreach ($columns as $column) {
        //     if (!in_array($column['col_name'], $columns_to_delete)) {
        //         continue;
        //     }

        //     if (!array_key_exists('col_id', $column) || !isset($column['col_id'])) {
        //         continue;
        //     }

        //     if (!array_key_exists('col_type', $column) || !isset($column['col_type'])) {
        //         continue;
        //     }

        //     ee()->grid_model->delete_columns($column['col_id'], $column['col_type'], $field_id, $column['content_type']);
        // }

        $this->log_message("$field_name-column-removal", 'NPR Story API Field Update', "Removed columns from $field_name: " . implode(', ', $columns_to_delete));

        return true;
    }

    private function update_column_length(string $table_name, string $column_name): bool
    {
        $table = new $table_name;
        $fields = $table->fields();
        $definition = array(
            $column_name => array(
                'name' => $column_name,
                'type' => $fields[$column_name]['type'],
                'constraint' => $fields[$column_name]['constraint'],
            ),
        );

        $result = ee()->dbforge->modify_column($table->table_name(), $definition);

        return $result;
    }

    private function log_message(string $alert_name, string $title, string $message): void
    {
        ee('CP/Alert')->makeInline($alert_name)
            ->asAttention()
            ->withTitle($title)
            ->addToBody($message)
            ->defer();
    }
}
