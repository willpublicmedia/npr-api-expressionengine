<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Config_form_builder {
    public function build_api_settings() {
        $form_data = array(
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

        return $form_data;
    }
}