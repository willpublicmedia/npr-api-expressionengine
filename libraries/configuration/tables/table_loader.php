<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/../../utilities/autoloader.php';
require_once __DIR__ . '/itable.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Tables\ITable;
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Autoloader;

class Table_loader
{
    /**
     * @param $path Path to check for loadable files.
     */
    public function __construct(string $path = '')
    {
        $path = $path === '' ? __DIR__ : $path;
        $this->preload_requirements($path);
    }

    public function load(
        string $model_name,
        string $namespace = 'IllinoisPublicMedia\\NprStoryApi\\Libraries\\Configuration\\Tables\\',
        string $identifier = '_table'
    ): ITable {
        $table_name = $namespace . $model_name . $identifier;

        $data = new $table_name();
        return $data;
    }

    /**
     * Require all classes in the specified directory.
     */
    private function preload_requirements($preload_dir)
    {
        $autoloader = new Autoloader();
        $autoloader->load_dir($preload_dir);
    }
}
