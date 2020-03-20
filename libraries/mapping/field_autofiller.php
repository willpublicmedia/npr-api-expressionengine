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
        $audio_data = $this->field_utils->get_grid_values($entry, $field_name);

        foreach ($audio_data as $k => $item)
        {
            $file_model = $this->get_file_model($item['file']);
            $format = $this->get_file_extension($item['file']);
            
            $item['audio_type'] = empty($item['audio_type']) ?
                $file_model->mime_type :
                $item['audio_type'];
    
            $item['audio_filesize'] = empty($item['audio_filesize']) ?
                $file_model->file_size :
                $item['audio_filesize'];

                
            $item['audio_format'] = empty($item['audio_format']) ?
                $format :
                $item['audio_format'];
                    
            $item['audio_url'] = empty($item['audio_url']) ?
                $this->build_url($file_model->getAbsoluteUrl()) :
                $item['audio_url'];
            
            $audio[$k] = $item;
        }

        
        return $entry;
    }

    public function autofill_image($field_name)
    {
        throw new \Exception('not implemented');
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
}