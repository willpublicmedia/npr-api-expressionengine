<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once(__DIR__ . '/../configuration/tables/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;

/**
 * NPR Story API table installer.
 */
class Table_installer {
    private $prefix;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        ee()->load->dbutil();

        $this->prefix = ee()->config->item('dbprefix');
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
            $table_name = $this->prefix . $table;
            ee()->dbforge->drop_table($table_name);
        }
    }

    private function create_table(ITable $table) {
        $name = $table->table_name();
        
        if (ee()->db->table_exists($name)) {
            return;
        }

        $fields = $table->fields();
        ee()->dbforge->add_field($fields);
        
        $keys = $table->keys();
        $this->add_keys($keys);

        ee()->dbforge->create_table($name);
        
        $defaults = $table->defaults();
        if (!empty($defaults)) {
            $this->add_default_settings($name, $defaults);
        }
    }

    private function add_default_settings(string $table_name, array $default_settings) {
        if (!substr($table_name, 0, strlen($this->prefix)) === $this->prefix) {
            $table_name = $this->prefix . $table_name;
        }
        
        $results = ee()->db->get($table_name);
        
        if (!empty($results->result_array())) {
            return;
        }
        
        ee()->db->insert($table_name, $default_settings);
    }

    private function add_keys(array $keys) {
        ee()->dbforge->add_key($keys['primary'], TRUE);

        if (array_key_exists('foreign', $keys)) {
            $foreign_keys = is_array($keys['foreign']) ?
                $keys['foreign'] :
                array($keys['foreign']);
            
            foreach ($foreign_keys as $foreign_key) {
                ee()->dbforge->add_key($foreign_key);
            }
        }
    }
}