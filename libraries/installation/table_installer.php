<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

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
        foreach($table_names as $table) {
            ee()->dbforge->drop_table($table);
        }
    }

    private function create_table(Table $table_config) {
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