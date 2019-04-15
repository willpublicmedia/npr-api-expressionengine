<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit ('No direct script access.');
}

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use IllinoisPublicMedia\NprStoryApi\Libraries\Model\Channel\Default_npr_story_layout;

class Layout_customizer {
    private $channel;

    public function __construct($channel) {
        $this->channel = $channel;
    }

    public function install($layout_name, $field_names) {
        $this->create_layout($layout_name);
        $this->assign_layout($layout_name, $this->channel);
    }

    public function uninstall($layout_name) {
        $model = ee('Model')->get('ChannelLayout')->filter('layout_name', '==', $layout_name)->first();

        if ($model != null) {
            $model->delete();
        }
    }

    private function assign_layout($layout_name, $channel) {
        $layout = ee('Model')->get('ChannelLayout')->filter('layout_name', '==', $layout_name)->first();

        $channel->ChannelLayouts->add($layout);

        $channel->save();
        $layout->save();
    }

    private function create_layout($layout_name) {
        $model = ee('Model')->get('ChannelLayout')->filter('layout_name', '==', $layout_name)->first();

        if ($model != null) {
            return;
        }

        $channel_layout = ee('Model')->make('ChannelLayout');
        $channel_layout->Channel = $this->channel;

        $default_layout = new Default_npr_story_layout($this->channel->channel_id, NULL);
        $field_layout = $default_layout->getLayout();
        foreach ($this->channel->getAllCustomFields() as $custom_field)
        {
            $field_layout[0]['fields'][] = array(
                'field' => $custom_field->field_id,
                'visible' => TRUE,
                'collapsed' => FALSE
            );
        }

        $channel_layout->layout_name = $layout_name;
        $channel_layout->field_layout = $field_layout;
        $channel_layout->save();
    }
}