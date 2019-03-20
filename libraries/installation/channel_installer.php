<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Model\Channel\Channel;

/**
 * Installs channels required by NPR Story API module.
 */
class Channel_installer {
    private $channel_data;

    public function __construct() {
        $this->channel_data = $this->load_channel_data();
    }

    /**
     * Create channel.
     * 
     * @return void
     */
    public function install($channel_names) {
        foreach($channel_names as $name) {
            if (!array_key_exists($name, $this->channel_data)) {
                throw new Exception("Channel configuration not found for {$name}.");
            }

            $data = $this->channel_data[$name];
            $this->create_channel($data);
        }
    }
    
    /**
     * Delete NPR Story API channels.
     *
     * @return void
     */
    public function uninstall() {
        foreach(array_values($this->channel_data) as $model) {
            $model->delete();
        }
    }

    /**
     * Create a new channel using a channel model.
     *
     * @param  Channel $model Channel model.
     *
     * @return void
     */
    private function create_channel($model) {
        $model->save();
    }

    private function load_channel_data() {
        $npr_stories = $this->load_npr_story_channel();

        $channels = array(
            'npr_stories' => $npr_stories
        );

        return $channels;
    }

    private function load_npr_story_channel() {
        $channel = ee('Model')->make('Channel', array(
            'channel_name' => 'npr_stories',
            'channel_title' => 'NPR Stories',
            'channel_url' => '{base_url}npr',
            'channel_description' => 'Stories pulled from the NPR Story API.',
        ));

        $fields = ee('Model')->get('ChannelFieldGroup')->all();

        return $channel;
    }
}