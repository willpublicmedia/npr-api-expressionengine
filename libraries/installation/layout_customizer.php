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

        $default = new Default_npr_story_layout($this->channel->channel_id, NULL);
        $layout = $default->getLayout();

        $model = ee('Model')->make('ChannelLayout', $layout);
        $model->layout_name = $layout_name;
        $model->save();
    }
}