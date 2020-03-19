<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH'))
{
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../utilities/field_utils.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;

class Field_autofiller
{
    private $field_utils;

    public function __construct()
    {
        $this->field_utils = new Field_utils();
    }

    public function autofill_audio($field_name, $entry)
    {
        $audio_data = $this->get_media($entry, $field_name);
        throw new \Exception('not implemented');
    }

    public function autofill_image($field_name)
    {
        throw new \Exception('not implemented');
    }

    private function get_media($entry, $field_name)
    {
        $content_type = 'channel';
        ee()->load->model('grid_model');
        $media_field_id = $this->field_utils->get_field_id($field_name);
        
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