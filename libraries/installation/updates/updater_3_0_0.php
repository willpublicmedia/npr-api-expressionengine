<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates;

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields\Story_content_definitions as Story_content_definitions;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Field_installer;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Updater_3_0_0
{
    private $fields = array(
        'audio_files',
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

        $installer = new Field_installer();

        foreach ($fields as $field_name) {
            $settings = Story_content_definitions::$fields[$field_name];
            // use grid_lib to apply settings
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
