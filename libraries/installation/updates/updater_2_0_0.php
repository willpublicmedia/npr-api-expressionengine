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
            foreach ($v as $col)
            {
                print_r("<p>Dealing with $k: $col</p>");
            }
        }
        return false;
    }
}