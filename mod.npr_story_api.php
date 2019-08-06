<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Npr_story_api 
{
    public $return_data;

    public function __construct()
    {
        $id = ee()->TMPL->fetch_param('id');
        $story = $this->story($id);

        $this->return_data = $story;
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
}