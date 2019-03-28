<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit ('No direct script access.');
}

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use EllisLab\ExpressionEngine\Model\Channel\Display\DefaultChannelLayout;

class Layout_customizer {
    private $available_fields = array(
        'entry_source' => 'add_entry_source'
    );

    private $channel;

    public function __construct($channel) {
        ee()->load->library('layout');
        $this->channel = $channel;
    }

    public function add_field($field_name) {
        if (!array_key_exists($field_name, $this->available_fields)) {
            throw new Exception("No field creation method exists for {$field_name}.");
        }

        $method = $this->available_fields[$field_name];
        $this->{$method}();
    }

    private function add_entry_source() {
        try {
            $default_layout = new DefaultChannelLayout($this->channel->channel_id, NULL);
            var_dump($default_layout);
            
            $tabs[] = $tabs['options'] = array(
                'entry_source' => array(
                    'visible'		=> 'true',
                    'collapse'		=> 'false',
                    'htmlbuttons'	=> 'true',
                    'width'			=> '100%'
                    )
                );

            ee()->layout->add_layout_fields($tabs, array($this->channel->channel_id));
        }
        catch (Exception $err) {
            return;
        }

    }
}