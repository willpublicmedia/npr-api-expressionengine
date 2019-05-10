<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

class Model_story_mapper {
    public function map_parsed_story($story): Model {
        $model = $this->load_base_model($story->id);
        $model->title = $story->title->value;
        $model->slug = $story->slug->value;
        // move this to channel behavior
        //$model->slug = ee('Format')->make('Text', $story->slug->value)->urlSlug(['separator' => '-', 'lowercase' => TRUE]);
        throw new \Exception('not implemented');
    }

    private function load_base_model($story_id) {
        if (ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->count() > 0) {
            return ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->first();
        }    
        
        $model = ee('Model')->make('npr_story_api:Npr_story');
        $model->id = $story_id;

        return $model;
    }
}