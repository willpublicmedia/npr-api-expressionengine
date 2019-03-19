<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Config_form_builder {
    private $api_settings_form = array(
        array(
            array(
                'title' => 'API Key',
                'fields' => array(
                    'api_key' => array(
                        'type' => 'text',
                        'value' => '',
                        'required' => TRUE
                    )
                )
            ),
            array(
                'title' => 'Pull URL',
                'fields' => array(
                    'pull_url' => array(
                        'type' => 'text',
                        'value' => ''
                    )
                )
            ),
            array(
                'title' => 'Push URL',
                'fields' => array(
                    'push_url' => array(
                        'type' => 'text',
                        'value' => ''
                    )
                )
            ),
            array(
                'title' => 'Org ID',
                'fields' => array(
                    'org_id' => array(
                        'type' => 'text',
                        'value' => ''
                    )
                )
            ),
            array(
                'title' => 'NPR Pull Post Type',
                'fields' => array(
                    'npr_pull_post_type' => array(
                        'type' => 'text',
                        'value' => ''
                    )
                )
            ),
            array(
                'title' => 'NPR Push Post Type',
                'fields' => array(
                    'npr_push_post_type' => array(
                        'type' => 'text',
                        'value' => ''
                    )
                )
            )
        )
    );

    public function build_api_settings_form($settings) {
        $this->add_form_values($settings);
        $form_data = $this->api_settings_form;

        return $form_data;
    }

    private function add_form_values($settings) {
        foreach ($this->api_settings_form[0] as &$item) {
            // get field id.
            reset($item['fields']);
            $field_name = key($item['fields']);

            $value = $settings[$field_name];

            $item['fields'][$field_name]['value'] = $value;
        }
    }
}