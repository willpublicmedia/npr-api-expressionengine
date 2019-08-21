<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH'))
{
    exit ('No direct script access allowed.');
}

class Publish_form_mapper
{
    /**
     * @entry A ChannelEntry object.
     * @values Post values returned by the publish form.
     * @story An NPR Story object.
     */
    public function map($entry, $values, $story)
    {
        $url_title = $this->generate_url_title($entry, $story->title);
        $permalink = $this->map_permalinks($story->Link);

        $data = array(
            'permalink' => $permalink,
            'title' => $story->title,
            'url_title' => $url_title
        );

        $values['title'] = $data['title'];
        $values['url_title'] = $data['url_title'];
        $entry->title = $values['title'];
        $entry->url_title = $values['url_title'];

        $objects = array(
            'entry' => $entry,
            'values' => $values,
            'story' => $story
        );

        return $objects;
    }

    private function generate_url_title($entry, $story_title)
    {
        $url_title = $entry->isNew() ? 
            (string) ee('Format')->make('Text', $story_title)->urlSlug() :
            $entry->url_title;
        
        return $url_title;
    }

    private function map_permalinks($link_models)
    {
        $model = $link_models->filter('type', '==', 'html')->first();
        
        if ($model === NULL)
        {
            return NULL;
        }

        return  $model->link;
    }
}