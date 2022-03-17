<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/libraries/installation/dependency_manager.php';
require_once __DIR__ . '/libraries/installation/field_installer.php';
require_once __DIR__ . '/libraries/installation/channel_installer.php';
require_once __DIR__ . '/libraries/installation/status_installer.php';
require_once __DIR__ . '/libraries/installation/extension_installer.php';
require_once __DIR__ . '/libraries/configuration/tables/table_loader.php';
require_once __DIR__ . '/libraries/configuration/tables/itable.php';
require_once __DIR__ . '/libraries/installation/table_installer.php';
require_once __DIR__ . '/libraries/installation/updates/updater_2_0_0.php';
use IllinoisPublicMedia\NprStoryApi\Constants;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table_loader;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Channel_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Dependency_manager;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Extension_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Field_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Status_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Table_installer;
use IllinoisPublicMedia\NprStoryApi\Libraries\Installation\Updates\Updater_2_0_0;

/**
 * NPR Story API updater.
 */
class Npr_story_api_upd
{
    private $channels = array(
        'npr_stories',
    );

    private $module_name = 'Npr_story_api';

    private $publish_layout = 'NPR Story API';

    private $tables = array(
        // table order matters for column relationships
        'config' => array(
            'config_settings',
            'config_field_mappings',
        ),
        'story' => array(
            'npr_story',
            'npr_organization',
            'npr_audio',
            'npr_audio_format',
            'npr_byline',
            'npr_html_asset',
            'npr_image',
            'npr_image_crop',
            'npr_permalink',
            'npr_pull_correction',
            'npr_pull_quote',
            // rewrite related link for push-only.
            // 'npr_related_link',
            'npr_text_paragraph',
            'npr_thumbnail',
            'pushed_stories',
        ),
    );

    private $version = Constants::VERSION;

    /**
     * NPR Story API updater constructor.
     *
     * @return void
     */
    public function __construct()
    {
        ee()->load->dbforge();
    }

    /**
     * Install NPR Story API module.
     *
     * @return bool
     */
    public function install()
    {
        if ($this->check_dependencies() === false) {
            return false;
        }

        $this->create_tables($this->tables['config']);
        $this->create_tables($this->tables['story']);
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

        if (APP_VER < 6) {
            ee()->db->delete('module_member_groups', array('module_id' => $query->row('module_id')));
        } else {
            ee()->db->delete('module_member_roles', array('module_id' => $query->row('module_id')));
        }

        ee()->db->delete('modules', array('module_name' => $this->module_name));
        ee()->db->delete('actions', array('class' => $this->module_name));

        $this->delete_channels();
        $this->delete_statuses();
        $this->delete_fields();
        $this->delete_extensions();
        $this->delete_tables($this->tables['story']);
        $this->delete_tables($this->tables['config']);

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

        if ($this->check_dependencies() === false) {
            return false;
        }

        $updated = true;

        if (version_compare($current, '2.0.0', '<')) {
            $updater_2_0_0 = new Updater_2_0_0();
            $success = $updater_2_0_0->update();

            if (!$success) {
                $updated = false;
            }
        }

        return $updated;
    }

    private function check_dependencies(): bool
    {
        $manager = new Dependency_manager();
        $has_dependencies = $manager->check_dependencies();

        return $has_dependencies;
    }

    private function create_required_channels()
    {
        $installer = new Channel_installer();
        $installer->install($this->channels, $this->publish_layout);
    }

    private function create_required_extensions()
    {
        $installer = new Extension_installer();
        $installer->install();
    }

    private function create_required_fields()
    {
        $installer = new Field_installer();
        $installer->install();
    }

    private function create_required_statuses()
    {
        $statuses = array(
            'draft',
        );

        $installer = new Status_installer();
        $installer->install($statuses);
    }

    private function create_tables(array $table_names)
    {
        $tables = array();
        foreach ($table_names as $name) {
            $data = $this->load_table_config($name);
            array_push($tables, $data);
        }

        $installer = new Table_installer();
        $installer->install($tables);
    }

    private function delete_channels()
    {
        $installer = new Channel_installer();
        $installer->uninstall($this->channels, $this->publish_layout);
    }

    private function delete_extensions()
    {
        $uninstaller = new Extension_installer();
        $uninstaller->uninstall();
    }

    private function delete_fields()
    {
        $uninstaller = new Field_installer();
        $uninstaller->uninstall();
    }

    private function delete_statuses()
    {
        $uninstaller = new Status_installer();
        $uninstaller->uninstall();
    }

    private function delete_tables(array $table_names)
    {
        $tables = array();
        foreach ($table_names as $name) {
            $data = $this->load_table_config($name);
            $table_name = $data->table_name();
            array_push($tables, $table_name);
        }

        $uninstaller = new Table_installer();
        $uninstaller->uninstall($tables);
    }

    private function load_table_config(string $table_name): ITable
    {
        $loader = new Table_loader();
        $data = $loader->load($table_name);

        return $data;
    }
}
