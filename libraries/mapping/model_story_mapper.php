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
        // throw new \Exception('Test using stories 691846168, 690346427');
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
        $model->Organization = $this->load_organization($story->organization);

        if (property_exists($story, 'thumbnail')) {
            $model->Thumbnail = $this->process_thumbnail($story->thumbnail);
        }

        if (property_exists($story, 'audio')) {
            foreach ($story->audio as $item)
            {
                $audio = $this->process_audio($item);
                $model->Audio->add($audio);
            }
        }

        if (property_exists($story, 'toenail'))
        {
            $model->Toenail = $this->process_thumbnail($story->toenail);
        }

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
        if (ee('Model')->get('npr_story_api:Npr_organization')->filter('orgId', $org_element->orgId)->count() > 0) {
            return ee('Model')->get('npr_story_api:Npr_organization')->filter('orgId', $org_element->orgId)->first();
        }

        $org = ee('Model')->make('npr_story_api:Npr_organization');
        $org->orgId = $org_element->orgId;
        $org->orgAbbr = $org_element->orgAbbr;
        $org->name = $org_element->name->value;
        $org->website = $org_element->website->value;
        $org->save();

        return $org;
    }

    private function process_audio($audio_element) {
        if (ee('Model')->get('npr_story_api:Npr_audio')->filter('id', $audio_element->id)->count() > 0) {
            return ee('Model')->get('npr_story_api:Npr_audio')->filter('id', $audio_element->id)->first();
        }

        $audio = ee('Model')->make('npr_story_api:Npr_audio');
        $audio->id = $audio_element->id;
        $audio->title = $audio_element->title->value;
        $audio->duration = $audio_element->duration->value;
        $audio->description = $audio_element->description->value;
        $audio->region = $audio_element->region->value;
        $audio->rightsholder = $audio_element->rightsHolder->value;
        $audio->type = $audio_element->type;
        
        $audio->permissions = $this->serialize_permissions($audio_element->permissions);
        
        // format
        // type
        // filesize

        $audio->save();
        return $audio;
    }

    private function process_thumbnail(\NPRMLElement $thumbnails) {
        $provider = $thumbnails->provider->value;
        
        $models = array();
        $model;
        foreach ($thumbnails as $key => $value) {
            if ($key === 'provider')
            {
                continue;
            }

            $link = $value->value;

            if (ee('Model')->get('npr_story_api:Npr_thumbnail')
                ->filter('link', $link)
                ->filter('size', $key)
                ->count() > 0) 
            {
                $model = ee('Model')->get('npr_story_api:Npr_thumbnail')
                    ->filter('link', $link)
                    ->filter('size', $key)
                    ->first();
            } 
            else
            {
                $model = ee('Model')->make('npr_story_api:Npr_thumbnail');
            }
            
            $model->size = $key;
            $model->provider = $provider;
            $model->link = $link;
            $model->save();

            $models[] = $model;
        }

        return $models;
    }

    private function serialize_permissions(\NPRMLElement $permissions_element) {
        $permissions = array();
        foreach ($permissions_element as $key => $value)
        {
            $permissions[$key] = $permissions_element->$key->allow;
        }

        return json_encode($permissions);
    }
}