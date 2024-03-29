<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class config_settings_table extends Table {
    protected $_defaults = array(
        'api_key' => '',
        'mapped_channels' => '',
        'npr_permissions' => '',
        'org_id' => null,
        'pull_url' => '',
        'push_url' => ''
    );
    
    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 10,
            'unsigned' => true,
            'auto_increment' => true,
        ),
        'api_key' => array(
            'type' => 'varchar',
            'constraint' => 64
        ),
        'mapped_channels' => array(
            'type' => 'text'
        ),
        'npr_permissions' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'npr_image_destination' => array(
            'type' => 'varchar',
            'constraint' => 64
        ),
        'org_id' => array(
            'type' => 'int',
            'null' => TRUE,
            'constraint' => 10
        ),
        'pull_url' => array(
            'type' => 'varchar',
            'constraint' => 64,
        ),
        'push_url' => array(
            'type' => 'varchar',
            'constraint' => 64
        )
    );

    protected $_keys = array(
        'primary' => 'id'
    );

    protected $_table_name = 'npr_story_api_settings';
}