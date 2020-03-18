<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Updater_2_0_0
{
    private $fields = array(
        'audio_files' => array(
            'audio_format',
            'audio_url'
        ),
        'npr_images' => array(
            'crop_type',
            'crop_src'
        )
    );

    public function update(): bool
    {
        $success = $this->remove_constraints($this->fields);
        return $success;
    }

    private function remove_constraints(array $fields): bool
    {
        foreach ($fields as $k => $v)
        {
            $parent_id = ee('Model')->get('ChannelField')
                ->filter('field_name', $k)
                ->fields('field_id')
                ->first()
                ->field_id;

            foreach ($v as $col)
            {
                ee()->db->update(
                    'exp_grid_columns', // table name
                    array('col_required' => 'n'), // updated columns and values
                    array('col_name' => $col, 'field_id' => $parent_id) // where clause
                );
            }

            $this->log_message();
        }

        return true;
    }

    private function log_message() {
        ee('CP/Alert')->makeInline('npr-db-update')
            ->asAttention()
            ->withTitle("NPR Data Fields Updated")
            ->addToBody("Removed format and url requirement from audio files field. Removed crop type and crop source requirement from npr images field.")
            ->defer();
    }
}