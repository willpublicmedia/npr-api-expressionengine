<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Dto\Installation\Table;

class npr_story_table implements iTable {
    public function table_name() {
        return 'npr_story_api_stories';
    }

    public function fields() {
        return array();
    }

    public function defaults() {
        return array();
    }
}