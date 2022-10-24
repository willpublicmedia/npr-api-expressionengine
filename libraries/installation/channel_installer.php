<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once(__DIR__ . '/../utilities/autoloader.php');
require_once(__DIR__ . '/field_installer.php');
require_once(__DIR__ . '/layout_customizer.php');
use ExpressionEngine\Model\Channel\Channel;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Field_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Layout_customizer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Autoloader;

/**
 * Installs channels required by NPR Story API module.
 */
class Channel_installer {
    private $required_channels = array(
        'npr_stories'
    );

    private $layout_name;

    /**
     * Create channel.
     * 
     * @return void
     */
    public function install($channel_names, $layout_name) {
        $this->layout_name = $layout_name;

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
        $this->preload_requirements();
        foreach($this->required_channels as $name) {
            $model = ee('Model')->get('Channel')->filter('channel_name', '==', $name)->first();
            $model->delete();
        }

        $customizer = new Layout_customizer($channel);
        $customizer->uninstall($layout_name);
    }

    private function customize_layout($channel) {
        $customizer = new Layout_customizer($channel);

        $customizer->install($this->layout_name);
    }

    private function init_npr_story_channel($channel = null) {
        $channel_name = 'npr_stories';
        $data = array(
            'channel_name' => $channel_name,
            'channel_title' => 'NPR Stories',
            'channel_url' => "/$channel_name/",
            'channel_description' => 'Stories pulled from the NPR Story API.',
            'comment_url' => "/$channel_name/story/",
            'preview_url' => "/$channel_name/story/{entry_id}",
            'rss_url' => "/$channel_name/rss",
            'search_results_url' => "/$channel_name/story/"
        );

        if ($channel == null) {
            $channel = ee('Model')->make('Channel');
        }

        foreach ($data as $key => $val) {
            $channel->{$key} = $val;
        }

        $channel->FieldGroups = ee('Model')->get('ChannelFieldGroup')->filter('group_name', '==', Field_installer::DEFAULT_FIELD_GROUP_NAME)->all();
        
        $draft = ee('Model')->get('Status')->filter('status', '==', 'draft')->first();
        $channel->Statuses->add($draft);
        $channel->deft_status = 'draft';
        
        $channel->save();
        $draft->save();

        $this->customize_layout($channel);
    }

    private function preload_requirements() {
        $dirs = array(
            __DIR__ . '/../model/channel',
            __DIR__ . '/../model/content',
        );

        $autoloader = new Autoloader();
        foreach ($dirs as $dir) {
            $autoloader->load_dir($dir);
        }
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