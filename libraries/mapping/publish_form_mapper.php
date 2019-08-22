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
        $byline = $this->map_bylines($story->Byline);
        $permalink = $this->map_permalinks($story->Link);
        $text = $this->map_text($story->TextWithHtml);
        $url_title = $this->generate_url_title($entry, $story->title);

        $data = array(
            'byline' => $byline,
            'keywords' => $story->keywords,
            'last_modified_date' => $story->lastModifiedDate,
            'mini_teaser' => $story->miniTeaser,
            'permalink' => $permalink,
            'priority_keywords' => $story->priorityKeywords,
            'pub_date' => $story->pubDate,
            'short_title' => $story->shortTitle,
            'slug' => $story->slug,
            'subtitle' => $story->subtitle,
            'story_date' => $story->storyDate,
            'teaser' => $story->teaser,
            'text' => $text,
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

    private function map_bylines($byline_models)
    {
        $names = array();
        foreach ($byline_models as $model)
        {
            $names[] = $model->name;
        }

        $byline = implode(', ', $names);
        return $byline;
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

    private function map_text($text_models)
    {
        $text_array = array();
        foreach ($text_models->sortBy('num') as $model)
        {
            // check for paragraph tags before adding text.
            $paragraph = mb_substr($model->text, 0, 2) === '<p' ?
                $model->text :
                "<p>$model->text</p>";

            $text_array[] = $paragraph;
        }

        $text_array = implode($text_array);

        return $text_array;
    }
}