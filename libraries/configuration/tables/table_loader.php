<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;

class Table_loader {
    public function load(string $model_name): ITable {
        $table_name = 'IllinoisPublicMedia\\NprStoryApi\\Libraries\\Configuration\\Tables\\'
        . $model_name
        . '_table';

        $data = new $table_name();
        return $data;
    }
}