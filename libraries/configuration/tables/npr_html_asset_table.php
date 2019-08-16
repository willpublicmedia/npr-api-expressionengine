<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__FILE__);
require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_html_asset_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'ee_id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'id' => array(
            'type' => 'varchar',
            'constraint' => 24,
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
        'asset' => array(
            'type' => 'text'
        )
    );

    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_html_assets';
}