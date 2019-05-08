<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;

/**
 * NPR Story API table installer.
 */
class Table_installer {
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        ee()->load->dbutil();
    }

    /**
     * Install NPR Story API tables.
     *
     * @return void
     */
    public function install(array $table_configs) {
        foreach ($table_configs as $table) {
            $this->create_table($table);
        }
    }

    /**
     * Uninstall NPR Story API tables.
     *
     * @return void
     */
    public function uninstall(array $table_names) {
        $prefix = ee()->config->item('dbprefix');

        foreach($table_names as $table) {
            $table_name = $prefix . $table;
            ee()->dbforge->drop_table($table_name);
        }
    }

    private function create_table(ITable $table) {
        $name = $table->table_name();
        
        if (!ee()->db->table_exists($name)) {
            $keys = $table->keys();
            $fields = $table->fields();

            ee()->dbforge->add_key($keys['primary'], TRUE);
            ee()->dbforge->add_field($fields);
            ee()->dbforge->create_table($name);
            ee()->db->insert($name, $fields);
        }

        $defaults = $table->defaults();
        if (!empty($defaults)) {
            $this->add_default_settings($name, $defaults);
        }
    }

    private function add_default_settings(string $table_name, array $default_settings) {
        $results = ee()->db->get($table);
        
        if (!empty($results->result_array())) {
            return;
        }
        
        ee()->db->insert($table, $defaults);
    }
}