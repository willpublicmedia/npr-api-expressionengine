<?php 

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Security;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * Tools for checking NPR Story API permissions.
 */
class Permissions_checker {
    /**
	 * Makes sure users can access a given method.
	 *
	 * @access	private
	 * @return	void
	 */
	public function check_permissions()
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