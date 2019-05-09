<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

class Model_story_mapper {
    public function map_parsed_story($story): Model {
        throw new \Exception('not implemented');
    }
}