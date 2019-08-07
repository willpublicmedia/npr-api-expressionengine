<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Npr_story_api 
{
    public $return_data;

    public function __construct()
    {
        $id = $this->validate_parameter(ee()->TMPL->fetch_param('npr_story_id'));
        $tagdata = ee()->TMPL->tagdata;

        $story = $this->story(intval($id));
        $data = $this->process_story($story);
        $variables = ee()->TMPL->parse_variables($tagdata, array($data));

        $this->return_data = $variables;
    }

    public function story($npr_story_id)
    {
        $model = ee('Model')->get('npr_story_api:Npr_story')
            ->filter('id', $npr_story_id)
            ->first();

        if ($model === NULL)
        {
            $model = ee()->TMPL->no_results;
        }

        return $model;
    }

    private function map_audio($audio_models)
    {
        if (count($audio_models) === 0)
        {
            return NULL;
        }

        $audio_array = array();
        foreach ($audio_models as $model)
        {
            $audio_array[] = array(
                'type' => $model->type,
                'duration' => $model->duration,
                'description' => $model->description,
                'formats' => $this->map_audio_formats($model->Format),
                'rights' => $model->rights,
                'permissions' => $model->permissions,
                'title' => $model->title,
                'region' => $model->region,
                'rightsholder' => $model->rightsholder
            );
        }
            
        return $audio_array;
    }

    private function map_audio_formats($format_models)
    {
        $format_array = array();
        foreach ($format_models as $model)
        {
            $format_array[] = array(
                'type' => $model->type,
                'format' => $model->format,
                'url' => $model->url
            );
        }

        return $format_array;
    }

    private function map_organization($org_model)
    {
        $org_array = array();
        $org_array[] = array(
            'name' => $org_model->name,
            'website' => $org_model->website
        );

        return $org_array;
    }

    private function map_thumbnails($thumbnail_models)
    {
        $thumbnail_array = array();
        foreach ($thumbnail_models as $thumbnail_model)
        {
            $thumbnail_array[] = array(
                'id' => $thumbnail_model->id,
                'size' => $thumbnail_model->size,
                'link' => $thumbnail_model->link,
                'provider' => $thumbnail_model->provider,
                'rights' => $thumbnail_model->rights
            );
        }

        return $thumbnail_array;
    }

    private function process_story($story)
    {
        $audio_array = $this->map_audio($story->Audio);
        $org_array = $this->map_organization($story->Organization);
        $thumbnail_array = $this->map_thumbnails($story->Thumbnail);
        $toenail_array = $this->map_thumbnails($story->Toenail);

        $data = array(
            'id' => $story->id,
            'title' => $story->title,
            'subtitle' => $story->subtitle,
            'shortTitle' => $story->shortTitle,
            'teaser' => $story->teaser,
            'miniTeaser' => $story->miniTeaser,
            'organization' => $org_array,
            'slug' => $story->slug,
            'storyDate' => $story->storyDate,
            'pubDate' => $story->pubDate,
            'lastModifiedDate' => $story->lastModifiedDate,
            'keywords' => $story->keywords,
            'priorityKeywords' => $story->priorityKeywords,
            'pullQuote' => array(),
            'audioRunByDate' => $story->audioRunByDate,
            'audio' => $audio_array,
            'thumbnails' => $thumbnail_array,
            'toenails' => $toenail_array
        );

        return $data;
    }

    private function validate_parameter($input)
    {
        return strip_tags($input);
    }
}