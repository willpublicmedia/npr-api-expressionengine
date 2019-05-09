<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_story_org_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE
        ),
        'org_id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE
        )
    );

    protected $_keys = array(
        'primary' => 'id',
        'foreign' => array(
            array(
                'column' => 'story_id',
                'foreign_table' => 'npr_story_api_stories',
                'foreign_column' => 'ee_id'
            ),
            array(
                'column' => 'org_id',
                'foreign_table' => 'npr_story_api_stories_organizations',
                'foreign_column' => 'id'
            )
        )
    );

    protected $_table_name = 'npr_story_api_stories_organizations_map';
    
    
}