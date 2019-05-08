<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

interface ITable {
    function defaults();

    function fields();

    function keys();

    function table_name();
}