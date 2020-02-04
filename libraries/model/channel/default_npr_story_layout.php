<?php 
namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Channel;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Model\Channel\Display\DefaultChannelLayout;

class Default_npr_story_layout extends DefaultChannelLayout {
	private $custom_options_fields = array(
		// publish
		'teaser' => NULL,
		'byline' => NULL,
		'audio_files' => NULL,
		'npr_images' => NULL,
		'text' => NULL,
		// date
		'audio_runby_date' => NULL,
		'last_modified_date' => NULL,
		'story_date' => NULL,
		'pub_date' => NULL,
		// options
		'channel_entry_source' => NULL,
		'npr_story_id' => NULL,
		'nprone_featured' => NULL,
		'overwrite_local_values' => NULL,
		'publish_to_npr' => NULL,
		'send_to_one' => NULL
	);

    /**
     * Create a default publish layout for the NPR Story API channel.
     *
     * @return void
     */
    protected function createLayout()
	{
		// prevent channel custom fields from stomping layout custom fields.
		$this->synchronize_custom_fields($this->custom_options_fields);

		$layout = array();

		$layout[] = array(
			'id' => 'publish',
			'name' => 'publish',
			'visible' => TRUE,
			'fields' => array(
				array(
					'field' => 'title',
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => 'url_title',
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => $this->custom_options_fields['byline'],
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => $this->custom_options_fields['teaser'],
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => $this->custom_options_fields['text'],
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => $this->custom_options_fields['audio_files'],
					'visible' => TRUE,
					'collapsed' => FALSE
				),
				array(
					'field' => $this->custom_options_fields['npr_images'],
					'visible' => TRUE,
					'collapsed' => FALSE
				)
			)
		);

		$channel = ee('Model')->get('Channel', $this->channel_id)->first();

		// Date Tab ------------------------------------------------------------

		$date_fields = array(
			array(
				'field' => 'entry_date',
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => 'expiration_date',
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['pub_date'],
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['last_modified_date'],
				'visible' => TRUE,
				'collapsed' => TRUE
			),
			array(
				'field' => $this->custom_options_fields['story_date'],
				'visible' => TRUE,
				'collapsed' => TRUE
			),
			array(
				'field' => $this->custom_options_fields['audio_runby_date'],
				'visible' => TRUE,
				'collapsed' => TRUE
			)
		);

		if (bool_config_item('enable_comments') && $channel->comment_system_enabled)
		{
			$date_fields[] = array(
				'field' => 'comment_expiration_date',
				'visible' => TRUE,
				'collapsed' => FALSE
			);
		}

		$layout[] = array(
			'id' => 'date',
			'name' => 'date',
			'visible' => TRUE,
			'fields' => $date_fields
		);

		// Category Tab --------------------------------------------------------

		$cat_groups = ee('Model')->get('CategoryGroup')
			->filter('group_id', 'IN', explode('|', $channel->cat_group))
			->all();

		$category_group_fields = array();
		foreach ($cat_groups as $cat_group)
		{
			$category_group_fields[] = array(
				'field' => 'categories[cat_group_id_'.$cat_group->getId().']',
				'visible' => TRUE,
				'collapsed' => FALSE
			);
		}

		$layout[] = array(
			'id' => 'categories',
			'name' => 'categories',
			'visible' => TRUE,
			'fields' => $category_group_fields
		);

		// Options Tab ---------------------------------------------------------

		$option_fields = array(
			array(
				'field' => 'channel_id',
				'visible' => TRUE,
				'collapsed' => FALSE
            ),
            array(
				'field' => $this->custom_options_fields['channel_entry_source'],
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['npr_story_id'],
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['overwrite_local_values'],
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['publish_to_npr'],
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => $this->custom_options_fields['send_to_one'],
				'visible' => TRUE,
				'collapsed' => TRUE
			),
			array(
				'field' => $this->custom_options_fields['nprone_featured'],
				'visible' => TRUE,
				'collapsed' => TRUE
			),
			array(
				'field' => 'status',
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => 'author_id',
				'visible' => TRUE,
				'collapsed' => FALSE
			),
			array(
				'field' => 'sticky',
				'visible' => TRUE,
				'collapsed' => FALSE
			)
		);

		if (bool_config_item('enable_comments') && $channel->comment_system_enabled)
		{
			$option_fields[] = array(
				'field' => 'allow_comments',
				'visible' => TRUE,
				'collapsed' => FALSE
			);
		}

		$layout[] = array(
			'id' => 'options',
			'name' => 'options',
			'visible' => TRUE,
			'fields' => $option_fields
		);

		if ($this->channel_id)
		{
			// Here comes the ugly! @TODO don't do this
			ee()->legacy_api->instantiate('channel_fields');

			$module_tabs = ee()->api_channel_fields->get_module_fields(
				$this->channel_id,
				$this->entry_id
			);
			$module_tabs = $module_tabs ?: array();

			foreach ($module_tabs as $tab_id => $fields)
			{
				$tab = array(
					'id' => $tab_id,
					'name' => $tab_id,
					'visible' => TRUE,
					'fields' => array()
				);

				foreach ($fields as $key => $field)
				{
					$tab['fields'][] = array(
						'field' => $field['field_id'],
						'visible' => TRUE,
						'collapsed' => $field['field_is_hidden'] === 'y' ? TRUE : FALSE
					);
				}

				$layout[] = $tab;
			}
		}

		if ($channel->enable_versioning)
		{
			$layout[] = array(
				'id' => 'revisions',
				'name' => 'revisions',
				'visible' => TRUE,
				'fields' => array(
					array(
						'field' => 'versioning_enabled',
						'visible' => TRUE,
						'collapsed' => FALSE
					),
					array(
						'field' => 'revisions',
						'visible' => TRUE,
						'collapsed' => FALSE
					)
				)
			);
		}

		return $layout;
	}

	private function synchronize_custom_fields(&$fields) {
		foreach ($fields as $key => $value) {
			$model = ee('Model')->get('ChannelField')->filter('field_name', $key)->first();
			
			$value = "field_id_{$model->field_id}";

			$fields[$key] = $value;
		}
	}
}