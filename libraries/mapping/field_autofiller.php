<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/../utilities/field_utils.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\MP3File;

class Field_autofiller
{
    private $field_utils;

    public function __construct()
    {
        $this->field_utils = new Field_utils();
    }

    public function autofill_audio($field_name, $entry): void
    {
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $audio_data = $this->field_utils->get_posted_grid_values("field_id_$field_id");

        if (empty($audio_data)) {
            return;
        }

        foreach ($audio_data['rows'] as $row => $item) {
            $file_col = $column_names['file'];
            $file_model = $this->get_file_model($item[$file_col]);

            if ($file_model === null) {
                continue;
            }

            $format = $this->get_file_extension($item[$file_col]);

            if (array_key_exists('audio_duration', $column_names)) {
                $duration_col = $column_names['audio_duration'];
                $item[$duration_col] = empty($item[$duration_col]) ? $this->calculate_audio_duration($file_model) : $item[$duration_col];
            }

            if (array_key_exists('audio_type', $column_names)) {
                $audio_type_col = $column_names['audio_type'];
                $item[$audio_type_col] = empty($item[$audio_type_col]) ? $format : $item[$audio_type_col];
            }

            if (array_key_exists('audio_filesize', $column_names)) {
                $filesize_col = $column_names['audio_filesize'];
                $item[$filesize_col] = empty($item[$filesize_col]) ? $file_model->file_size : $item[$filesize_col];
            }

            if (array_key_exists('audio_format', $column_names)) {
                $format_col = $column_names['audio_format'];
                $item[$format_col] = empty($item[$format_col]) ? $format : $item[$format_col];
            }

            if (array_key_exists('audio_url', $column_names)) {
                $url_col = $column_names['audio_url'];
                if (empty($item[$url_col])) {
                    $item[$url_col] = $file_model === null ? $item[$url_col] : $this->build_url($file_model->getAbsoluteUrl());
                }
            }

            if (array_key_exists('audio_permissions', $column_names)) {
                $permissions_col = $column_names['audio_permissions'];
                $item[$permissions_col] = empty($item[$permissions_col]) ? 'download, stream, embed' : $item[$permissions_col];
            }

            if (array_key_exists('audio_title', $column_names)) {
                $title_col = $column_names['audio_title'];
                $position = array_search($row, array_keys($audio_data['rows'])) + 1;
                $item[$title_col] = empty($item[$title_col]) ? $entry->title . ' segment ' . $position : $item[$title_col];
            }

            $audio_data['rows'][$row] = $item;
        }

        $this->field_utils->save_posted_grid_values("field_id_$field_id", $audio_data);
        $this->field_utils->cache_posted_grid_values("field_id_$field_id", $audio_data);
    }

    public function autofill_images($field_name, $entry): void
    {
        $field_id = $this->field_utils->get_field_id($field_name);
        $column_names = $this->field_utils->get_grid_column_names($field_id);
        $image_data = $this->field_utils->get_posted_grid_values("field_id_$field_id");

        if (empty($image_data)) {
            return;
        }

        foreach ($image_data['rows'] as $row => $item) {
            $file_col = $column_names['file'];
            $file_model = $this->get_file_model($item[$file_col]);

            if ($file_model === null) {
                continue;
            }

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

            $image_data['rows'][$row] = $item;
        }

        $this->field_utils->save_posted_grid_values("field_id_$field_id", $image_data);
        $this->field_utils->cache_posted_grid_values("field_id_$field_id", $image_data);
    }

    private function build_url($input)
    {
        $site_url = ee()->config->item('site_url');
        $url = substr($input, 0, strlen($site_url)) === $site_url ?
        $input :
        $site_url . '/' . ltrim($input, '/');

        return $url;
    }

    private function calculate_audio_duration($model)
    {
        $path = $model->getAbsolutePath();
        if (!$path) {
            return null;
        }

        $mp3 = new MP3File($path);
        $duration = $mp3->getDurationEstimate();
        return $duration;
    }

    private function get_file_extension($filename)
    {
        $segments = explode('.', $filename);
        $extension = end($segments);
        return $extension;
    }

    private function get_file_model($entry_filepath)
    {
        $split = explode('}', $entry_filepath);

        if (!$split || sizeof($split) < 2) {
            ee('CP/Alert')->makeInline('autofill-model-error')
                ->asAttention()
                ->withTitle('NPR Stories')
                ->addToBody("Missing filepath information ($entry_filepath). Unable to autofill media fields.")
                ->defer();

            return null;
        }

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
            'width' => intval($hw[1]),
        ];

        return $dimensions;
    }

    private function prepare_grid_data(int $entry_id, int $field_id, array $named_data, array $column_names): array
    {
        $data = array(
            'entry_id' => $entry_id,
            'field_id' => $field_id,
        );

        foreach ($named_data as $item) {
            $row_id = $item['row_id'];

            $row = array();

            foreach ($item as $name => $value) {
                if ($name === 'entry_id' || $name == 'row_id') {
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
