<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_audio_format_table extends Table {
    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'format' => array(
            'type' => 'varchar',
            'constraint' => 24
        ),
        'type' => array(
            'type' => 'varchar',
            'constraint' => 48
        ),
        'url' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
    );

    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_audio';
}