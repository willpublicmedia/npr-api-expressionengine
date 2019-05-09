<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Config_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Field_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Channel_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Status_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Extension_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table_loader;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Table_installer;

/**
 * NPR Story API updater.
 */
class Npr_story_api_upd
{
    private $channels = array(
        'npr_stories'
    );

    private $module_name = 'Npr_story_api';

    private $publish_layout = 'NPR Story API';
    
    private $tables = array(
        // table order matters for column relationships
        'story' => array(
            'npr_story',
            'npr_organization',
            'npr_audio',
            'npr_image',
            'npr_pull_correction',
            'npr_pull_quote',
            'npr_related_link',
            'npr_text_paragraph',
            'npr_thumbnail',
            'npr_story_org'
        )
    );

    private $version = '0.0.0';

    /**
     * NPR Story API updater constructor.
     *
     * @return void
     */
    public function __construct() {
        ee()->load->dbforge();
    }

    /**
     * Install NPR Story API module.
     *
     * @return bool
     */
    public function install()
    {
        $this->create_config_tables();
        $this->create_story_tables($this->tables['story']);
        $this->create_required_fields();
        $this->create_required_statuses();
        $this->create_required_channels();
        $this->create_required_extensions();

        $data = array(
            'module_name' => $this->module_name,
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n',
        );

        ee()->db->insert('modules', $data);
        
        return true;
    }

    /**
     * Uninstall NPR Story API module.
     *
     * @return bool
     */
    public function uninstall()
    {
        ee()->db->select('module_id');
        ee()->db->from('modules');
        ee()->db->where('module_name', $this->module_name);
        $query = ee()->db->get();

        ee()->db->delete('module_member_groups', array('module_id' => $query->row('module_id')));
        ee()->db->delete('modules', array('module_name' => $this->module_name));
        ee()->db->delete('actions', array('class' => $this->module_name));

        $this->delete_channels();
        $this->delete_statuses();
        $this->delete_fields();
        $this->delete_extensions();
        $this->delete_config();
        $this->delete_story_tables($this->tables['story']);

        return true;
    }

    /**
     * Update NPR Story API module.
     *
     * @param  mixed $current Current module version.
     *
     * @return bool
     */
    public function update($current = '')
    {
        if (version_compare($this->version, '1.0.0', '<')) {
            $this->uninstall();
            $this->install();

            return true;
        }
        
        if (version_compare($current, $this->version, '=')) {
            return false;
        }

        return true;
    }

    private function create_config_tables() {
        $config_installer = new Config_installer();
        $config_installer->install();
    }

    private function create_required_channels() {
        $installer = new Channel_installer();
        $installer->install($this->channels, $this->publish_layout);
    }

    private function create_required_extensions() {
        $installer = new Extension_installer();
        $installer->install();
    }

    private function create_required_fields() {
        $installer = new Field_installer();
        $installer->install();
    }

    private function create_required_statuses() {
        $statuses = array(
            'draft'
        );

        $installer = new Status_installer();
        $installer->install($statuses);
    }

    private function create_story_tables(array $table_names) {
        $tables = array();
        foreach ($table_names as $name) {
            $data = $this->load_table_config($name);
            array_push($tables, $data);
        }

        $installer = new Table_installer();
        $installer->install($tables);
    }

    private function delete_channels() {
        $installer = new Channel_installer();
        $installer->uninstall($this->channels, $this->publish_layout);
    }

    private function delete_config() {
        $uninstaller = new Config_installer();
        $uninstaller->uninstall();
    }

    private function delete_extensions() {
        $uninstaller = new Extension_installer();
        $uninstaller->uninstall();
    }

    private function delete_fields() {
        $uninstaller = new Field_installer();
        $uninstaller->uninstall();
    }

    private function delete_statuses() {
        $uninstaller = new Status_installer();
        $uninstaller->uninstall();
    }

    private function delete_story_tables(array $table_names) {
        $tables = array();
        foreach ($table_names as $name) {
            $data = $this->load_table_config($name);
            $table_name = $data->table_name();
            array_push($tables, $table_name);
        }

        $uninstaller = new Table_installer();
        $uninstaller->uninstall($tables);
    }

    private function load_table_config(string $table_name): ITable {
        $loader = new Table_loader();
        $data = $loader->load($table_name);

        return $data;
    }
}