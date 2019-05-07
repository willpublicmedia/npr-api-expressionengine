<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Dto\Installation;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Table {
    public $table_name;
    
    public $fields;

    public $defaults;
}