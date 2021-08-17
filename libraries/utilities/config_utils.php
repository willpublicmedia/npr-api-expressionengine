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

    public function list_mapped_channels($with_names = false): array
    {
        $results = ee()->db->
            select('mapped_channels')->
            from('npr_story_api_settings')->
            get()->
            result_array();

        $mapped_channels = (array_pop($results))['mapped_channels'];
        $mapped_channels = explode("|", $mapped_channels);

        if ($with_names) {
            $mapped_channels = $this->get_channel_names($mapped_channels);
        }

        return $mapped_channels;
    }

    private function get_channel_names(array $channel_ids): array
    {
        $models = ee('Model')->get('Channel')
            ->filter('channel_id', 'IN', $channel_ids)
            ->fields('channel_id', 'channel_name', 'channel_title')
            ->all();

        $channels = [];
        foreach ($models as $model) {
            $channels[] = [
                'id' => $model->channel_id,
                'name' => $model->channel_name,
                'title' => $model->channel_title,
            ];
        }

        return $channels;
    }
}
