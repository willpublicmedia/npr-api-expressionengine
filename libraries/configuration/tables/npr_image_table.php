<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/table.php');
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_image_table extends Table {
    protected $_defaults = array();

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
        'title' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'type' => array(
            'type' => 'varchar',
            'constraint' => 128
        ),
        'width' => array(
            'type' => 'int',
            'constraint' => 24
        ),
        'src' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'hasBorder' => array(
            'type' => 'bool'
        ),
        'caption' => array(
            'type' => 'varchar',
            'constraint' => 2560
        ),
        'link' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'producer' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'provider' => array(
            'type' => 'varchar',
            'constraint' => 256
        ),
        'providerUrl' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'copyright' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'constraint' => 4
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        ),
        'enlargement' => array(
            'type' => 'varchar',
            'constraint' => 2048
        ),
        'enlargementCaption' => array(
            'type' => 'varchar',
            'constraint' => 2048
        )
    );

    protected $_keys = array(
        'primary' => 'ee_id',
        'foreign' => 'story_id'
    );

    protected $_table_name = 'npr_story_api_stories_images';
}