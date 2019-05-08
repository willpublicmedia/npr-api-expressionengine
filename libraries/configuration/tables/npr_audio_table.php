<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_audio_table extends Table {
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
        'primary' => array(
            'type' => 'bool'
        ),
        'duration' => array(
            'type' => 'varchar',
            'constraint' => 48
        ),
        'description' => array(
            'type' => 'text'
        ),
        'format' => array(
            'type' => 'varchar',
            'constraint' => 24
        ),
        'rights' => array(
            'type' => 'varchar',
            'constraint' => 128
        ),
        'url' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'story' => array(
            'type' => 'int',
            'constraint' => 64
        ),
    );

    protected $_keys = array(
        'primary' => 'ee_id'
    );

    protected $_table_name = 'npr_story_api_stories_audio';
}