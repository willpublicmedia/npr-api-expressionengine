<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Npr_story_api_upd {
        private $version = '0.0.0';

        private $module_name = 'Npr_story_api';

        public function install() {
            $data = array(
                'module_name' => $this->module_name,
                'module_version' => $this->version,
                'has_cp_backend' => 'y',
                'has_publish_fields' => 'n'
            );

            ee()->db->insert('modules', $data);

            $this->create_settings_table();

            return TRUE;
        }

        public function uninstall() {
            ee()->db->select('module_id');
            ee()->db->from('modules');
            ee()->db->where('module_name', $this->module_name);
            $query = ee()->db->get();
    
            ee()->db->delete('module_member_groups', array('module_id' => $query->row('module_id')));
            ee()->db->delete('modules', array('module_name' => $this->module_name));
            ee()->db->delete('actions', array('class' => $this->module_name));
            ee()->db->delete('actions', array('class' => 'Ipm_pledge_tracker_mcp'));

            $this->delete_settings_table();

		    return TRUE;
        }

        public function update($current = '') {
            if (version_compare($current, $this->version, '='))
            {
                return FALSE;
            }

            return TRUE;
        }

        private function create_settings_table() {
            throw new Exception('not implemented.');
        }

        private function delete_settings_table() {
            throw new Exception('not implemented.');
        }
    }
?>