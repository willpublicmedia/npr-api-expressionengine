<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Utilities;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Field_utils
{
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
}