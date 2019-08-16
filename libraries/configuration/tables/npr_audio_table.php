<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
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
        'duration' => array(
            'type' => 'varchar',
            'constraint' => 48
        ),
        'description' => array(
            'type' => 'text'
        ),
        'permissions' => array(
            'type' => 'varchar',
            'constraint' => 1024
        ),
        'region' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'rights' => array(
            'type' => 'varchar',
            'constraint' => 128
        ),
        'rightsholder' => array(
            'type' => 'varchar',
            'constraint' => 512
        ),
        'title' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'type' => array(
            'type' => 'varchar',
            'constraint' => 48,
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
    );

    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_audio';
}