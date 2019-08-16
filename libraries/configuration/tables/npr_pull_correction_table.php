<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_pull_correction_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'correctionTitle' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'correctionText' => array(
            'type' => 'text'
        ),
        'correctionDate' => array(
            'type' => 'varchar',
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

    protected $_table_name = 'npr_story_api_stories_corrections';
}