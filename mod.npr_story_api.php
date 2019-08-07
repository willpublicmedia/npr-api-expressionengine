<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Template_mapper;

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

    private function process_story($story)
    {
        $mapper = new Template_mapper();
        $data = $mapper->map($story);

        return $data;
    }

    private function validate_parameter($input)
    {
        return strip_tags($input);
    }
}