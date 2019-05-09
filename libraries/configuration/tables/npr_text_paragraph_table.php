<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_text_paragraph_table extends Table {
    protected $_defaults = array();

    protected $_fields = array(
        'id' => array(
            'type' => 'int',
            'constraint' => 255,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        // distinguishes text, listtext, textwithhtml
        'paragraphType' => array(
            'type' => 'varchar',
            'constraint' => 24
        ),
        'num' => array(
            'type' => 'int',
            'constraint' => 255
        ),
        'text' => array(
            'type' => 'text'
        ),
        'tag' => array(
            'type' => 'varchar',
            'constraint' => 12
        ),
        'story_id' => array(
            'type' => 'int',
            'constraint' => 64
        )
    );

    protected $_keys = array(
        'primary' => 'id',
        'foreign' => array(
            array(
                'column' => 'story_id',
                'foreign_table' => 'exp_npr_story_api_stories',
                'foreign_column' => 'id'
            )
        )
    );

    protected $_table_name = 'npr_story_api_stories_text_paragraphs';
}