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

        // $audio_data = $this->field_utils->get_grid_values($entry, $field_name);
        // $test = $_POST["field_id_$field_id"];
        foreach ($audio_data['rows'] as $row => $columns)
        {
            $file_col = $column_names['file'];
            $file_model = $this->get_file_model($columns[$file_col]);
            $format = $this->get_file_extension($columns[$file_col]);
            foreach ($columns as $col => $value)
            {
$foo = 'bar';
            }
            
            // $item['audio_type'] = empty($item['audio_type']) ?
            //     $file_model->mime_type :
            //     $item['audio_type'];
    
            // $item['audio_filesize'] = empty($item['audio_filesize']) ?
            //     $file_model->file_size :
            //     $item['audio_filesize'];

                
            // $item['audio_format'] = empty($item['audio_format']) ?
            //     $format :
            //     $item['audio_format'];
                    
            // $item['audio_url'] = empty($item['audio_url']) ?
            //     $this->build_url($file_model->getAbsoluteUrl()) :
            //     $item['audio_url'];
            
            // $audio_data[$k] = $item;
        }

        // $prepared = $this->prepare_grid_data($entry->entry_id, $field_id, $audio_data, $column_names);
        // $cached = $this->field_utils->save_grid_data($prepared);

        // return $cached;
    }

    public function autofill_images($field_name, $entry)
    {
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $image_data = $this->field_utils->get_grid_values($entry, $field_name);

        foreach ($image_data as $k => $item)
        {
            $file_model = $this->get_file_model($item['file']);
            $format = $this->get_file_extension($item['file']);
            $dimensions = $this->get_image_dimensions($file_model->file_hw_original);
            
            $item['crop_type'] = empty($item['crop_type']) ?
                'default' :
                $item['crop_type'];
            
            $item['crop_src'] = empty($item['crop_src']) ?
                $this->build_url($file_model->getAbsoluteUrl()) :
                $item['crop_src'];
            
            $item['crop_width'] = empty($item['crop_width']) ?
                $dimensions['width'] :
                $item['crop_width'];

            $image_data[$k] = $item;
        }
        
        $prepared = $this->prepare_grid_data($entry->entry_id, $field_id, $image_data, $column_names);
        $cached = $this->field_utils->save_grid_data($prepared);
        
        return $cached;
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