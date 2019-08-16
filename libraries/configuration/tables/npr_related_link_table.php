<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__FILE__);
require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_related_link_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'ee_id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
        'type' => array(
            'type' => 'varchar',
            'constraint' => '24'
        ),
        'caption' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        )
    );

    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_related_links';
}