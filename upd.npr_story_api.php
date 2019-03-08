<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Npr_story_api_upd
{
    private $version = '0.0.0';

    private $module_name = 'Npr_story_api';

    public function install()
    {
        $data = array(
            'module_name' => $this->module_name,
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n',
        );

        ee()->db->insert('modules', $data);

        $this->create_settings_table();
        $this->create_field_mappings_table();

        return true;
    }

    public function uninstall()
    {
        ee()->db->select('module_id');
        ee()->db->from('modules');
        ee()->db->where('module_name', $this->module_name);
        $query = ee()->db->get();

        ee()->db->delete('module_member_groups', array('module_id' => $query->row('module_id')));
        ee()->db->delete('modules', array('module_name' => $this->module_name));
        ee()->db->delete('actions', array('class' => $this->module_name));
        ee()->db->delete('actions', array('class' => 'Ipm_pledge_tracker_mcp'));

        $tables = array(
            'npr_story_api_field_mappings',
            'npr_story_api_settings',
        );

        $this->delete_tables($tables);

        return true;
    }

    public function update($current = '')
    {
        if (version_compare($current, $this->version, '=')) {
            return false;
        }

        return true;
    }

    private function create_field_mappings_table()
    {
        ee()->load->dbforge();

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
            ),
        );
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('npr_story_api_field_mappings');
    }

    private function create_settings_table()
    {
        ee()->load->dbforge();

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
            ),
        );

        ee()->dbforge->add_key('id', true);
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('npr_story_api_settings');
    }

    private function delete_tables($table_names)
    {
        foreach($table_names as $table) {
            ee()->db->delete($table);
        }
    }
}