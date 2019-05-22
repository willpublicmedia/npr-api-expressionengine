<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

require_once(__DIR__ . '/../../vendor/autoload.php');
use \NPRMLElement;

class Model_story_mapper {
    public function map_parsed_story($story): Model {
        $model = $this->load_base_model($story->id);
        $model->title = $story->title->value;
        $model->slug = $story->slug->value;
        $model->subtitle = $story->subtitle->value;
        $model->shortTitle = $story->subtitle->value;
        $model->teaser = $story->teaser->value;
        $model->miniTeaser = $story->miniTeaser->value;
        $model->storyDate = $this->convert_date_string($story->storyDate->value);
        $model->pubDate = $this->convert_date_string($story->pubDate->value);
        $model->lastModifiedDate = $this->convert_date_string($story->lastModifiedDate->value);
        $model->keywords = $story->keywords->value;
        $model->priorityKeywords = $story->keywords->value;

        $organization = $this->load_organization($story->organization);
        $model->Organization = $organization;
        
        // move this to channel behavior
        //$model->slug = ee('Format')->make('Text', $story->slug->value)->urlSlug(['separator' => '-', 'lowercase' => TRUE]);
        return $model;
    }

    private function convert_date_string($date_string) {
        return date('Y-m-d H:i:s', strtotime($date_string));
    }

    private function load_base_model($story_id) {
        if (ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->count() > 0) {
            return ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->first();
        }    
        
        $model = ee('Model')->make('npr_story_api:Npr_story');
        $model->id = $story_id;

        return $model;
    }
    
    private function load_organization(\NPRMLElement $org_element) {
        if (ee('Model')->get('npr_story_api:Npr_organization')->filter('id', $org_element->orgId)->count() > 0) {
            return ee('Model')->get('npr_story_api:Npr_organization')->filter('id', $org_element->orgId)->first();
        }

        $org = ee('Model')->make('npr_story_api:Npr_organization');
        $org->orgId = $org_element->orgId;
        $org->orgAbbr = $org_element->orgAbbr;
        $org->name = $org_element->name->value;
        $org->website = $org_element->website->value;
        $org->save();

        return $org;
    }
}