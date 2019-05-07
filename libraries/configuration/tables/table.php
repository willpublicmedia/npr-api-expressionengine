<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Table {
    public function table_name() {
        return $this->_table_name;
    }
    
    public function defaults() {
        return $this->_defaults;
    }
    
    public function fields() {
        return $this->_fields;
    }

    public function keys() {
        return $this->_keys;
    }
}