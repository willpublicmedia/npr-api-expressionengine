<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_audio_format_table extends Table {
    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'audio_id' => array(
            'type' => 'int',
            'constraint' => 64
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
        'primary' => 'id',
        'foreign' => 'audio_id'
    );

    protected $_table_name = 'npr_story_api_stories_audio_formats';
}