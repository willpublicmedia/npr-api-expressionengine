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
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $audio_data = $this->field_utils->get_posted_grid_values("field_id_$field_id");

        if (empty($audio_data))
        {
            return;
        }
        
        foreach ($audio_data['rows'] as $row => $item)
        {
            $file_col = $column_names['file'];
            $file_model = $this->get_file_model($item[$file_col]);
            $format = $this->get_file_extension($item[$file_col]);

            $audio_type_col = $column_names['audio_type'];
            $item[$audio_type_col] = empty($item[$audio_type_col]) ?
                $file_model->mime_type :
                $item[$audio_type_col];

            $filesize_col = $column_names['audio_filesize'];
            $item[$filesize_col] = empty($item[$filesize_col]) ?
                $file_model->file_size :
                $item[$filesize_col];

            $format_col = $column_names['audio_format'];
            $item[$format_col] = empty($item[$format_col]) ?
                $format :
                $item[$format_col];

            $url_col = $column_names['audio_url'];
            $item[$url_col] = empty($item[$url_col]) ?
                $this->build_url($file_model->getAbsoluteUrl()) :
                $item[$url_col];

            $audio_data['rows'][$row] = $item;
        }

        $this->field_utils->save_posted_grid_values("field_id_$field_id", $audio_data);
    }

    public function autofill_images($field_name, $entry)
    {
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $image_data = $this->field_utils->get_posted_grid_values("field_id_$field_id");

        if (empty($image_data))
        {
            return;
        }

        foreach ($image_data['rows'] as $row => $item)
        {
            $file_col = $column_names['file'];
            $file_model = $this->get_file_model($item[$file_col]);
            $format = $this->get_file_extension($item[$file_col]);
            $dimensions = $this->get_image_dimensions($file_model->file_hw_original);
            
            $crop_col = $column_names['crop_type'];
            $item[$crop_col] = empty($item[$crop_col]) ?
                'default' :
                $item[$crop_col];
            
            $src_col = $column_names['crop_src'];
            $item[$src_col] = empty($item[$src_col]) ?
                $this->build_url($file_model->getAbsoluteUrl()) :
                $item[$src_col];
            
            $width_col = $column_names['crop_width'];
            $item[$width_col] = empty($item[$width_col]) ?
                $dimensions['width'] :
                $item[$width_col];

            $image_data[$row] = $item;
        }
        
        $this->field_utils->save_posted_grid_values("field_id_$field_id", $image_data);
    }

    private function build_url($input)
    {
        $site_url = ee()->config->item('site_url');
        $url = substr($input, 0, strlen($site_url)) === $site_url ?
            $input :
            $site_url . '/' . ltrim($input, '/');
        
        return $url;
    }

    private function get_file_extension($filename)
    {
        return end(explode('.', $filename));
    }

    private function get_file_model($entry_filepath)
    {
        $split = explode('}', $entry_filepath);
        preg_match('/\d+$/', $split[0], $location_id);
            
        $file_model = ee('Model')->get('File')
            ->filter('file_name', $split[1])
            ->filter('upload_location_id', $location_id[0])
            ->first();
        
        return $file_model;
    }

    private function get_image_dimensions($file_hw_property)
    {
        $hw = explode(' ', $file_hw_property);
        $dimensions = [
            'height' => intval($hw[0]),
            'width' => intval($hw[1])
        ];

        return $dimensions;
    }

    private function prepare_grid_data(int $entry_id, int $field_id, array $named_data, array $column_names): array
    {
        $data = array(
            'entry_id' => $entry_id,
            'field_id' => $field_id
        );

        foreach ($named_data as $item)
        {
            $row_id = $item['row_id'];
            
            $row = array();
            
            foreach ($item as $name => $value)
            {
                if ($name === 'entry_id' || $name == 'row_id')
                {
                    continue;
                }

                $col = $column_names[$name];
                $row[$col] = $value;
            }

            $data['values'][$entry_id]["row_id_$row_id"] = $row;
        }

        return $data;
    }
}