<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Config_installer {
    public function install() {
        $this->create_settings_table();
        $this->create_field_mappings_table();
    }

    public function uninstall() {
        $tables = array(
            'npr_story_api_field_mappings',
            'npr_story_api_settings',
        );

        $this->delete_tables($tables);
    }

    private function create_field_mappings_table()
    {
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
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('npr_story_api_field_mappings');
    }

    private function create_settings_table()
    {
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

        ee()->dbforge->add_key('id', true);
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('npr_story_api_settings');
    }
    
    private function delete_tables($table_names)
    {
        foreach($table_names as $table) {
            ee()->dbforge->drop_table($table);
        }
    }
}