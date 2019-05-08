<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\Table;

class npr_story_table extends Table {
    protected $_table_name = 'npr_story_api_stories';

    protected $_defaults = array();
    
    protected $_fields = array();
    
    protected $_keys = array();
}