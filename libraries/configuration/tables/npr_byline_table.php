<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_byline_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'byline_id' => array(
            'type' => int,
            'constraint' => 64
        ),
        'name' => array(
            'type' => 'varchar',
            'constraint' => 64
        ),
        'personId' => array(
            'type' => int,
            'constraint' => 64
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        )
    );

    protected $_keys = array(
        'primary' => 'id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_bylines';
}