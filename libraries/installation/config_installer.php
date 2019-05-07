<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * NPR Story API configuration installer.
 */
class Config_installer {
    /**
     * Config constructor.
     *
     * @return void
     */
    public function __construct() {
        ee()->load->dbutil();
    }

    /**
     * Install NPR Story API settings.
     *
     * @return void
     */
    public function install() {
        $this->init_settings_table();
        $this->init_field_mappings_table();
    }

    /**
     * Uninstall NPR Story API settings.
     *
     * @return void
     */
    public function uninstall() {
        $tables = array(
            'npr_story_api_field_mappings',
            'npr_story_api_settings',
        );

        $this->delete_tables($tables);
    }

    private function add_default_settings($table, $defaults) {
        $results = ee()->db->get($table);
        
        if (!empty($results->result_array())) {
            return;
        }
        
        ee()->db->insert($table, $defaults);
    }

    private function create_table($table_name, $fields, $defaults) {
        if (!ee()->db->table_exists($table_name)) {
            ee()->dbforge->add_key('id', true);
            ee()->dbforge->add_field($fields);
            ee()->dbforge->create_table($table_name);
            ee()->db->insert($table_name, $fields);
        }

        $this->add_default_settings($table_name, $defaults);
    }

    private function init_field_mappings_table()
    {
        $table_name = 'npr_story_api_field_mappings';

        $fields = array(
            'id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'custom_settings' => array(
                'type' => 'boolean',
            ),
            'media_agency_field' => array(
                'type' => 'varchar',
                'constraint' => 128,
            ),
            'media_credit_field' => array(
                'type' => 'varchar',
                'constraint' => 128,
            ),
            'story_title' => array(
                'type' => 'varchar',
                'constraint' => 128,
            ),
            'story_body' => array(
                'type' => 'varchar',
                'constraint' => 128,
            ),
            'story_byline' => array(
                'type' => 'varchar',
                'constraint' => 128,
            )
        );

        $defaults = array(
            'custom_settings' => FALSE,
            'media_agency_field' => '',
            'media_credit_field' => '',
            'story_title' => '',
            'story_body' => '',
            'story_byline' => ''
        );

        $this->create_table($table_name, $fields, $defaults);
    }

    private function init_settings_table()
    {
        $table_name = 'npr_story_api_settings';

        $fields = array(
            'id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'api_key' => array(
                'type' => 'varchar',
                'constraint' => 64,
            ),
            'npr_permissions' => array(
                'type' => 'varchar',
                'constraint' => 256
            ),
            'npr_pull_post_type' => array(
                'type' => 'varchar',
                'constraint' => 64,
            ),
            'npr_push_post_type' => array(
                'type' => 'varchar',
                'constraint' => 64,
            ),
            'org_id' => array(
                'type' => 'int',
                'null' => TRUE,
                'constraint' => 10,
            ),
            'pull_url' => array(
                'type' => 'varchar',
                'constraint' => 64,
            ),
            'push_url' => array(
                'type' => 'varchar',
                'constraint' => 64,
            )
        );
        
        $defaults = array(
            'api_key' => '',
            'npr_permissions' => '',
            'npr_pull_post_type' => '',
            'npr_push_post_type' => '',
            'org_id' => null,
            'pull_url' => '',
            'push_url' => ''
        );

        $this->create_table($table_name, $fields, $defaults);
    }
    
    private function delete_tables($table_names)
    {
        foreach($table_names as $table) {
            ee()->dbforge->drop_table($table);
        }
    }
}