<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Layout_customizer;

/**
 * Installs channels required by NPR Story API module.
 */
class Channel_installer {
    private $required_channels = array(
        'npr_stories'
    );

    /**
     * Create channel.
     * 
     * @return void
     */
    public function install($channel_names) {
        foreach($channel_names as $name) {
            if (!in_array($name, $this->required_channels)) {
                throw new \Exception("Channel configuration not found for {$name}.");
            }

            $this->update_channel_data($name);
        }
    }

    /**
     * Delete NPR Story API channels.
     *
     * @return void
     */
    public function uninstall($channel, $layout_name) {
        foreach($this->required_channels as $name) {
            $model = ee('Model')->get('Channel')->filter('channel_name', '==', $name)->first();
            $model->delete();
        }

        $customizer = new Layout_customizer($channel, $layout_name);
        $customizer->uninstall($layout_name);
    }

    private function customize_layout($channel, $layout_name, $field_names) {
        $customizer = new Layout_customizer($channel, $layout_name);

        foreach ($field_names as $field) {
            $customizer->add_field($field);
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
        
        $draft = ee('Model')->get('Status')->filter('status', '==', 'draft')->first();
        $channel->Statuses->add($draft);
        $channel->deft_status = 'draft';
        
        $channel->save();
        $draft->save();

        $layout_fields = array(
            'entry_source'
        );

        $this->customize_layout($channel, $layout_fields);
    }

    private function update_channel_data($channel_name) {
        $channel = ee('Model')
            ->get('Channel')
            ->filter('channel_name', '==', $channel_name)
            ->first();
        
        switch($channel_name) {
            case 'npr_stories':
                $this->init_npr_story_channel($channel);
                break;
            default:
                throw new \Exception("Couldn't find initializer function for channel {$channel_name}.");
        }
    }
}