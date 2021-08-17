<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Utilities;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Config_utils
{
    private function is_mapped_channel($channel_id)
    {
        $mapped_channels = $this->list_mapped_channels();
        $is_mapped = in_array($channel_id, $mapped_channels);

        return $is_mapped;
    }

    public function list_mapped_channels(): array
    {
        $results = ee()->db->
            select('mapped_channels')->
            from('npr_story_api_settings')->
            get()->
            result_array();

        $mapped_channels = (array_pop($results))['mapped_channels'];
        $mapped_channels = explode("|", $mapped_channels);

        return $mapped_channels;
    }
}
