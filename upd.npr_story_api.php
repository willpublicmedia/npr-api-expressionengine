<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(__DIR__ . '/libraries/configuration/config_installer.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Config_installer;

/**
 * NPR Story API updater.
 */
class Npr_story_api_upd
{
    private $version = '0.0.0';

    private $module_name = 'Npr_story_api';

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
        ee()->db->delete('actions', array('class' => 'Ipm_pledge_tracker_mcp'));

        $this->delete_config();

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
        if (version_compare($current, $this->version, '=')) {
            return false;
        }

        return true;
    }

    private function create_config_tables() {
        $config_installer = new Config_installer();
        $config_installer->install();
    }

    private function delete_config() {
        $uninstaller = new Config_installer();
        $uninstaller->uninstall();
    }
}