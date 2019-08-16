<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_image_crop_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 64,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'image_id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
        'type' => array(
            'type' => 'varchar',
            'constraint' => 64
        ),
        'src' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'height' => array(
            'type' => 'int',
            'constraint' => 4
        ),
        'width' => array(
            'type' => 'int',
            'constraint' => 4
        ),
        'primary' => array(
            'type' => 'bool'
        )
    );

    protected $_keys = array(
        'primary' => 'id',
        'foreign' => 'image_id'
    );

    protected $_table_name = 'npr_story_api_stories_image_crops';
}