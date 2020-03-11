<?php
namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Dependency_manager
{
    private $php_required_modules = [
        'curl',
        'xml'
    ];

    public function check_dependencies($show_errors = true): bool
    {
        $missing = $this->check_php_modules($this->php_required_modules);
        $has_dependencies = empty($missing);

        return $has_dependencies;
    }

    private function check_php_modules(array $modules): array
    {
        $failed = array();
        foreach($modules as $module)
        {
            if (!\extension_loaded($module))
            {
                $failed[] = $module;
            }
        }

        return $failed;
    }
}