<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Utilities;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/field_utils.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;

class Channel_entry_builder
{
    private $field_utils;

    public function __construct()
    {
        ee()->load->model('grid_model');
        $this->field_utils = new Field_utils();
    }

    /**
     * @param data Array of channel entry field name : field value pairs.
     * @param entry A ChannelEntry model.
     * @param values Array of input post data.
     */
    public function assign_data_to_entry($data, $entry, $values)
    {
        foreach ($data as $field => $value) {
            $name = $field;
            if ($field !== 'title' && $field !== 'url_title') {
                $field = $this->field_utils->get_field_name($field);
            }

            $values[$field] = $value;
            $entry->{$field} = $value;

            if ($this->field_is_grid($name)) {
                // Grid_ft->post_save stomps data values with cache.
                ee()->session->set_cache('Grid_ft', $field, $value);
            }
        }

        $objects = array(
            'entry' => $entry,
            'values' => $values,
        );

        return $objects;
    }

    /**
     * @param name Channel name.
     * @return bool
     */
    public function field_is_grid($name)
    {
        $type = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->fields('field_type')
            ->first()
            ->field_type ?? '';

        $is_grid = ($type === 'grid' || $type === 'file_grid');
        return $is_grid;
    }
}
