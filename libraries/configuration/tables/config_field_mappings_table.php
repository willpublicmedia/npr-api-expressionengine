<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class config_field_mappings_table extends Table {
    protected $_defaults = array(
        'custom_settings' => FALSE,
            'media_agency_field' => '',
            'media_credit_field' => '',
            'story_title' => '',
            'story_body' => '',
            'story_byline' => ''
    );

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 10,
            'unsigned' => true,
            'auto_increment' => true,
        ),
        'custom_settings' => array(
            'type' => 'boolean',
        ),
        'media_agency_field' => array(
            'type' => 'varchar',
            'constraint' => 128,
        ),
        'media_credit_field' => array(
            'type' => 'varchar',
            'constraint' => 128,
        ),
        'story_title' => array(
            'type' => 'varchar',
            'constraint' => 128,
        ),
        'story_body' => array(
            'type' => 'varchar',
            'constraint' => 128,
        ),
        'story_byline' => array(
            'type' => 'varchar',
            'constraint' => 128,
        )
    );

    protected $_keys = array(
        'primary' => 'id'
    );

    protected $_table_name = 'npr_story_api_field_mappings';
}