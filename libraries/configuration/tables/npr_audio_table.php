<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class npr_audio_table extends Table {
    private $_fields = array(
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
            'type' => 'varchar',
        ),
        'format' => array(
            'type' => 'varchar',
            'constraint' => 24
        ),
        'rights' => array(
            'type' => 'varchar',
            'constraint' => 64
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

    private $_keys = array(
        'primary' => 'ee_id'
    );

    private $_table_name = 'npr_story_api_stories_audio';
}