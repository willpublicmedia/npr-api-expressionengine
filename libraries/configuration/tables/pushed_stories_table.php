<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class pushed_stories_table extends Table {
    protected $_defaults = array();
    
    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 10,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'entry_id' => array(
            'type' => 'int',
            'constraint' => 10
        ),
        'npr_story_id' => array(
            'type' => 'varchar',
            'constraint' => 24,
        ),
    );
    
    protected $_keys = array(
        'primary' => 'id',
    );
    
    protected $_table_name = 'npr_story_api_pushed_stories';
}