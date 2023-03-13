<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_story_table extends Table {
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
        'title' => array(
            'type' => 'varchar',
            'constraint' => 512
        ),
        'subtitle' => array(
            'type' => 'varchar',
            'constraint' => 1024
        ),
        'shortTitle' => array(
            'type' => 'varchar',
            'constraint' => 128
        ),
        'teaser' => array(
            'type' => 'varchar',
            'constraint' => '4096'
        ),
        'miniTeaser' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'organization_id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
        'slug' => array(
            'type' => 'varchar',
            'constraint' => 128
        ),
        'storyDate' => array(
            'type' => 'datetime'
        ),
        'pubDate' => array(
            'type' => 'datetime'
        ),
        'lastModifiedDate' => array(
            'type' => 'datetime'
        ),
        'keywords' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'priorityKeywords' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'pullQuote' => array(
            'type' => 'varchar',
            'constraint' => 4096
        ),
        'audioRunByDate' => array(
            'type' => 'datetime'
        ),
        'entry_id' => array(
            'type' => 'int',
            'constraint' => 10
        )
    );
    
    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'organization_id'
    );
    
    protected $_table_name = 'npr_story_api_stories';
}