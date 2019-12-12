<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * Tools for building NPR Story API control panel forms.
 */
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
                'title' => 'Org ID',
                'fields' => array(
                    'org_id' => array(
                        'type' => 'text',
                        'value' => ''
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

    /**
     * Build control panel form for API settings.
     *
     * @param  mixed $settings NPR Story API setting values.
     *
     * @return mixed Control panel form.
     */
    public function build_api_settings_form($settings) {
        $this->api_settings_form[0][] = $this->get_upload_destinations();
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

    private function get_upload_destinations() {
        $destinations = ee('Model')->get('UploadDestination')
            ->filter('site_id', ee()->config->item('site_id'))
            ->filter('module_id', 0) // limit selection to user-defined destinations
            ->all();
        $file_choices = array();
        foreach ($destinations as $dest) { $file_choices[] = $dest->name; }
            
        
        $upload_field = array(
            'title' => 'Image Upload Destination',
            'fields' => array(
                'npr_upload_destination' => array(
                    'type' => 'radio',
                    'choices' => $file_choices,
                    'value' => ''
                )
            ),
            'required' => true
        );

        return $upload_field;
    }
}