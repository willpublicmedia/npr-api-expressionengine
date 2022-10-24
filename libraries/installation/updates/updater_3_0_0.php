<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Updater_3_0_0
{
    private $fields = array(
        'audio_files' => array(
            'remove' => array(
                'audio_type',
                'audio_duration',
            ),
        ),
    );

    public function update(): bool
    {
        $publish_columns_removed = $this->remove_publish_form_columns($this->fields);
        $success = $publish_columns_removed;
        return $success;
    }

    private function remove_publish_form_columns(array $fields): bool
    {
        $success_audio_remove = false;

        foreach ($fields as $field => $actions) {
            $model = ee('Model')->get('ChannelField')
                ->filter('field_name', $field)
                ->fields('field_id')
                ->first();

            $field_id = $model->field_id;

            $remove_cols = $actions['remove'];
            foreach ($remove_cols as $col) {
                $model->remove($col);
            }

            $success_audio_remove = $model->save();
        }

        $this->log_message();

        $success = $success_audio_remove;

        return $success;
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
