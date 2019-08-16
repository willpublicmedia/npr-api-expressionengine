<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_organization_table extends Table {
    protected $_defaults = array();
    
    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'orgId' => array(
            'type' => 'int',
            'constraint' => 24
        ),
        'orgAbbr' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'name' => array(
            'type' => 'varchar',
            'constraint' => 512
        ),
        'website' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'website_type' => array(
            'type' => 'varchar',
            'constraint' => 128
        )
    );
    
    protected $_keys = array(
        'primary' => 'id'
    );

    protected $_table_name = 'npr_story_api_stories_organizations';
}