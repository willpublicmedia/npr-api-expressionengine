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
        $values['title'] = $story->title;
        if ($entry->isNew() && $entry->title != $values['title'])
        {
            $values['url_title'] = $this->generate_url_title($values['title']);
            $entry->url_title = $values['url_title'];
        }
        
        $entry->title = $values['title'];

        $objects = array(
            'entry' => $entry,
            'values' => $values,
            'story' => $story
        );

        return $objects;
    }

    private function generate_url_title($title)
    {
        return (string) ee('Format')->make('Text', $title)->urlSlug();
    }
}