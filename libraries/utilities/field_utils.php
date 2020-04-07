<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Utilities;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Field_utils
{
    public function __construct()
    {
        ee()->load->library('input');
    }

    /**
     * Grid_ft->post_save stomps data values with cache.
     */
    public function cache_grid_data(array $data): array
    {
        $entry_id = $data['entry_id'];
        $field_id = $data['field_id'];
        $field = "field_id_$field_id";

        $cache = array(
            'rows' => $data['values'][$entry_id]
        );

        // Expected:
        // $entry->field_id_48 = array(
        //     'rows' => array(
        //         'new_row_1' => array(
        //             'col_id_29' => 'foo',
        //             'col_id_30' => 'bar'
        //         )
        //     )
        // );

        // Grid_ft->post_save stomps data values with cache.
        ee()->session->set_cache('Grid_ft', $field, $cache);

        return $cache;
    }

    public function cache_posted_grid_values($field_name, $data)
    {
        // Expected:
        // $entry->field_id_48 = array(
        //     'rows' => array(
        //         'new_row_1' => array(
        //             'col_id_29' => 'foo',
        //             'col_id_30' => 'bar'
        //         )
        //     )
        // );

        // Grid_ft->post_save stomps data values with cache.
        ee()->session->set_cache('Grid_ft', $field_name, $data);
    }

    public function get_field_id($name)
    {
        $field_id = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->fields('field_id')
            ->first()
            ->field_id;
        
        return $field_id;
    }

    public function get_field_name($name)
    {
        $field = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->first();

        if ($field === NULL)
        {
            return '';
        }

        $field_id = $field->field_id;
        $field_name = "field_id_{$field_id}";
        
        return $field_name;
    }

    public function get_grid_column_names($field_id)
    {
        $ids = ee()->grid_model->get_columns_for_field($field_id, 'channel');

        $columns = array();
        foreach ($ids as $id => $data)
        {
            $name = $data['col_name'];
            $columns[$name] = "col_id_$id";
        }

        return $columns;
    }

    public function get_grid_values($entry, $field_name)
    {
        $content_type = 'channel';
        ee()->load->model('grid_model');
        $media_field_id = $this->get_field_id($field_name);
        
        // map column names
        $columns = ee()->grid_model->get_columns_for_field($media_field_id, $content_type);
		
        // get entry data
        $entry_data = ee()->grid_model->get_entry_rows($entry->entry_id, $media_field_id, $content_type, null);
        
        // loop entry data rows
        $media = array();
        foreach ($entry_data[$entry->entry_id] as $row)
        {
            $row_data = array();

            // return row, entry ids in case save is needed.
            $row_data['row_id'] = $row['row_id'];
            $row_data['entry_id'] = $row['entry_id'];

            // map column data to column names
            foreach ($columns as $column_id => $column_details)
            {
                $column_name = $column_details['col_name'];
                $row_column = "col_id_$column_id";
                $row_col_data = $row[$row_column];
                $row_data[$column_name] = $row_col_data;
            }

            $media[] = $row_data;
        }

        return $media;
    }

    public function get_posted_grid_values($field_name): array
    {
        $values = ee()->input->post($field_name);
        if (empty($values))
        {
            $values = array();
        }
        return $values;
    }

    public function save_grid_data(array $data, $cache = true): array
    {
        $content_type = 'channel';
        $entry_id = $data['entry_id'];
        $to_be_deleted = ee()->grid_model->save_field_data($data['values'][$entry_id], $data['field_id'], $content_type, $entry_id, $fluid_field_data_id = NULL);

        if (!$cache)
        {
            return array();
        }

        $cached = $this->cache_grid_data($data);
        $data['cached'] = $cached;

        return $data;
    }

    public function save_posted_grid_values(string $field_name, array $data): void
    {
        $post_backup = $_POST;
        if (array_key_exists($field_name, $_POST))
        {
            $_POST[$field_name] = $data;
        }
    }
}