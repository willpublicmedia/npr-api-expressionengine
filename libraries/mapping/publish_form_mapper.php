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
        $corrections = $this->map_corrections($story->Correction);
        $permalink = $this->map_permalinks($story->Link);
        $text = $this->map_text($story->TextWithHtml);
        $url_title = $this->generate_url_title($entry, $story->title);

        $data = array(
            'audio_runby_date' => strtotime($story->audioRunByDate),
            'byline' => $byline,
            'keywords' => $story->keywords,
            'last_modified_date' => strtotime($story->lastModifiedDate),
            'mini_teaser' => $story->miniTeaser,
            'permalink' => $permalink,
            'priority_keywords' => $story->priorityKeywords,
            'pub_date' => strtotime($story->pubDate),
            'short_title' => $story->shortTitle,
            'slug' => $story->slug,
            'subtitle' => $story->subtitle,
            'story_date' => strtotime($story->storyDate),
            'teaser' => $story->teaser,
            'text' => $text,
            'title' => $story->title,
            'url_title' => $url_title
        );

        $objects = $this->assign_data_to_entry($data, $entry, $values);
        $objects['story'] = $story;
        return $objects;
    }

    private function assign_data_to_entry($data, $entry, $values)
    {
        foreach ($data as $field => $value)
        {
            if ($field !== 'title' && $field !== 'url_title')
            {
                $field = $this->get_field_name($field);
            }

            $values[$field] = $value;
            $entry->{$field} = $value;
        }

        $objects = array(
            'entry' => $entry,
            'values' => $values,
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

    private function get_field_name($name)
    {
        $field = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->first();

        if ($field === NULL)
        {
            return '';
        }

        $field_id = $field->field_id;
        $field_name = "field_id_{$field_id}";
        
        return $field_name;
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

    private function map_corrections($correction_models)
    {
        $corrections = array();
        return $corrections;
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