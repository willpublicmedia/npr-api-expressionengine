<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Npr_story_api_mcp
{
    private $api_settings = array(
        'api_key' => array(
            'display_name' => 'API Key',
            'value' => ''
        ),
        'pull_url' => array(
            'display_name' => 'Pull URL',
            'value' => ''
        ),
        'push_url' => array(
            'display_name' => 'Push URL',
            'value' => ''
        ),
        'org_id' => array(
            'display_name' => 'Org ID',
            'value' => ''
        ),
        'npr_pull_post_type' => array(
            'display_name' => 'NPR Pull Post Type',
            'value' => ''
        ),
        'npr_push_post_type' => array(
            'display_name' => 'NPR Push Post Type',
            'value' => ''
        ),
        'npr_permissions' => array(
            'display_name' => 'NPR Permissions',
            'value' => 'You have no Permission Groups defined with the NPR API.'
        )
    );

    private $base_url;

    public function __construct() {
        $this->_permissions_check();
        $this->base_url = ee('CP/URL')->make('addons/settings/npr_story_api');
        ee()->load->helper('form');
    }

    public function index() {
        $values = ee()->db->get('npr_story_api_settings');
        $settings = array(
            'settings' => $this->api_settings,
            'db_values' => $values
        );

        return ee('View')->make('npr_story_api:index')->render($settings);
    }

    private $post_types = array();
        
    private function validate_server($server) {
        return filter_var($server, FILTER_VALIDATE_URL);
    }

    /**
	 * Makes sure users can access a given method
	 *
	 * @access	private
	 * @return	void
	 */
	private function _permissions_check()
	{
		// super admins always can
		$can_access = (ee()->session->userdata('group_id') == '1');

		if ( ! $can_access)
		{
			// get the group_ids with access
			$result = ee()->db->select('module_member_groups.group_id')
				->from('module_member_groups')
				->join('modules', 'modules.module_id = module_member_groups.module_id')
				->where('modules.module_name',$this->name)
				->get();

			if ($result->num_rows())
			{
				foreach ($result->result_array() as $r)
				{
					if (ee()->session->userdata('group_id') == $r['group_id'])
					{
						$can_access = TRUE;
						break;
					}
				}
			}
		}

		if ( ! $can_access)
		{
			show_error(lang('unauthorized_access'), 403);
		}
	}
}