<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Table_loader {
    public function load(string $table_name): Table {
        $data = new $table_name();
        return $data;
    }
}