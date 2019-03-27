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
    private $required_channels = array(
        'npr_stories' => null
    );

    public function __construct() {
        $this->required_channels = $this->load_required_channels();
    }

    /**
     * Create channel.
     * 
     * @return void
     */
    public function install($channel_names) {
        foreach($channel_names as $name) {
            if (!array_key_exists($name, $this->required_channels)) {
                throw new Exception("Channel configuration not found for {$name}.");
            }

            $data = $this->required_channels[$name];
            $data->save();
        }
    }

    /**
     * Delete NPR Story API channels.
     *
     * @return void
     */
    public function uninstall() {
        foreach(array_values($this->required_channels) as $model) {
            $model->delete();
        }
    }

    private function init_npr_story_channel($channel = null) {
        $data = array(
            'channel_name' => 'npr_stories',
            'channel_title' => 'NPR Stories',
            'channel_url' => '{base_url}npr',
            'channel_description' => 'Stories pulled from the NPR Story API.',
        );

        if ($channel == null) {
            $channel = ee('Model')->make('Channel');
        }

        foreach ($data as $key => $val) {
            $channel->{$key} = $val;
        }

        $channel->FieldGroups = ee('Model')->get('ChannelFieldGroup')->all();
        $channel->CustomFields = ee('Model')->get('ChannelField')->all();
        
        $status = ee('Model')->get('Status')->filter('status', '==', 'draft')->first();
        $channel->Statuses->add($status);
        $channel->deft_status = $status->status;

        return $channel;
    }

    private function load_channel_data($channel_name) {
        $channel = ee('Model')
            ->get('Channel')
            ->filter('channel_name', '==', $channel_name)
            ->first();
        
        switch($channel_name) {
            case 'npr_stories':
                $channel = $this->init_npr_story_channel($channel);
                break;
            default:
                throw new Exception("Couldn't find initializer function for channel {$channel_name}.");
        }

        return $channel;
    }

    private function load_required_channels() {
        $channels = $this->required_channels;
        
        foreach ($channels as $name => $channel) {
            $channel = $this->load_channel_data($name);
            $channels[$name] = $channel;
        }

        return $channels;
    }
}