<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class npr_story_table extends Table {
    private $_table_name = 'npr_story_api_stories';
}