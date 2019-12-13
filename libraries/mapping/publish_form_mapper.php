<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH'))
{
    exit ('No direct script access allowed.');
}

class Publish_form_mapper
{
    private $settings;

    public function __construct()
    {
        $this->settings = ee()->db
            ->limit(1)
            ->get('npr_story_api_settings')
            ->row();
    }

    /**
     * @entry A ChannelEntry object.
     * @values Post values returned by the publish form.
     * @story An NPR Story object.
     */
    public function map($entry, $values, $story)
    {
        $audio = $this->map_audio($story->Audio);
        $byline = $this->map_bylines($story->Byline);
        $corrections = $this->map_corrections($story->Correction, $entry->entry_id);
        $images = $this->map_images($story->Image);
        $keywords = $this->map_keywords($story->keywords);
        $org = $this->map_organization($story->Organization);
        $permalinks = $this->map_permalinks($story->Link);
        $pullquotes = $this->map_pullquotes($story->PullQuote);
        $text = $this->map_text($story->TextWithHtml);
        $url_title = $this->generate_url_title($entry, $story->title);

        $data = array(
            'audio_files' => $audio,
            'audio_runby_date' => strtotime($story->audioRunByDate),
            'byline' => $byline,
            'corrections' => $corrections,
            'keywords' => $keywords,
            'last_modified_date' => strtotime($story->lastModifiedDate),
            'mini_teaser' => $story->miniTeaser,
            'npr_images' => $images,
            'organization' => $org,
            'permalinks' => $permalinks,
            'priority_keywords' => $story->priorityKeywords,
            'pub_date' => strtotime($story->pubDate),
            'pullquotes' => $pullquotes,
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
            $name = $field;
            if ($field !== 'title' && $field !== 'url_title')
            {
                $field = $this->get_field_name($field);
            }

            $values[$field] = $value;
            $entry->{$field} = $value;

            if ($this->field_is_grid($name))
            {
                // Grid_ft->post_save stomps data values with cache.
                ee()->session->set_cache('Grid_ft', $field, $value);
            }
        }

        $objects = array(
            'entry' => $entry,
            'values' => $values,
        );

        return $objects;
    }

    private function convert_audio_duration($raw)
    {
        return ltrim(gmdate('H:i:s', $raw), "00:");
    }

    private function field_is_grid($name)
    {
        $type = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->fields('field_type')
            ->first()
            ->field_type;
        
        $is_grid = ($type === 'grid' || $type === 'file_grid');
        return $is_grid;
    }

    private function generate_url_title($entry, $story_title)
    {
        $url_title = $entry->isNew() ? 
            (string) ee('Format')->make('Text', $story_title)->urlSlug() :
            $entry->url_title;
        
        return $url_title;
    }

    private function get_field_id($name)
    {
        $field_id = ee('Model')->get('ChannelField')
            ->filter('field_name', $name)
            ->fields('field_id')
            ->first()
            ->field_id;
        
        return $field_id;
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

    private function get_grid_column_names($field_id)
    {
        $ids = ee()->grid_model->get_columns_for_field($field_id, 'channel');

        $columns = array();
        foreach ($ids as $id => $data)
        {
            $name = $data['col_name'];
            $columns[$name] = "col_id_$id";
        }

        return $columns;
    }

    private function map_audio($audio_models)
    {
        $audio_array = array();
        
        /* get column names */
        $field_id = $this->get_field_id('audio_files');
        $grid_column_names = $this->get_grid_column_names($field_id);
        
        $count = 1;
        foreach ($audio_models as $model)
        {
            $stream = $this->map_audio_formats($model->Format);
            if (isset($stream[0]))
            {
                $stream = $stream[0];
            }

            // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
            $row_name = "new_row_$count";

            $audio = array(
                    $grid_column_names['audio_type'] => $model->type, // col_id => value?
                    $grid_column_names['audio_duration'] => $this->convert_audio_duration($model->duration),
                    $grid_column_names['audio_description'] => $model->description,
                    $grid_column_names['audio_format'] => $stream['format'],
                    $grid_column_names['audio_url'] => $stream['url'],
                    $grid_column_names['audio_rights'] => $model->rights,
                    $grid_column_names['audio_permissions'] => $this->parse_audio_permissions($model->permissions),
                    $grid_column_names['audio_title'] => $model->title,
                    $grid_column_names['audio_region'] => $model->region,
                    $grid_column_names['audio_rightsholder'] => $model->rightsholder
            );

            $audio_array['rows'][$row_name] = $audio;
            $count++;
        }
   
        return $audio_array;
    }

    private function map_audio_formats($format_models)
    {
        $preference = array('mp4', 'mp3');
        $model = NULL;
        foreach ($preference as $format)
        {
            $model = $format_models->filter('format', '==', $format)->first();
            if ($model != NULL)
            {
                break;
            }
        }
        
        if ($model === NULL)
        {
            return;
        }

        $format_array = array();
        $format_array[] = array(
            'type' => $model->type,
            'format' => $model->format,
            'url' => $model->url
        );

        return $format_array;
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

    private function map_corrections($correction_models, $entry_id)
    {
        $corrections = array();
        
        /* get column names */
        $field_id = $this->get_field_id('corrections');
        $grid_column_names = $this->get_grid_column_names($field_id);
        // $entry_rows = ee()->grid_model->get_entry($entry_id, $field_id, 'channel');

        $count = 1;
        foreach ($correction_models as $model)
        {
            // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
            $row_name = "new_row_$count";

            $correction = array(
                    $grid_column_names['correction_date'] => $model->correctionDate, // col_id => value?
                    $grid_column_names['correction_text'] => $model->correctionText
            );

            $corrections['rows'][$row_name] = $correction;
            $count++;
        }
     
        return $corrections;
    }

    private function map_image_crops($crop_models)
    {
        if (!($crop_models instanceof \EllisLab\ExpressionEngine\Service\Model\Collection))
        {
            $crop_models = array($crop_models);
        }

        $crop_array = array();
        foreach ($crop_models as $model)
        {
            $file = $this->sideload_file($model);

            $primary = $model->type === 'primary';
            if (property_exists($model, 'primary') && $model->primary)
            {
                $primary = true;
            }

            $crop_array[] = array(
                'type' => $model->type,
                'src' => $model->src,
                'height' => property_exists($model, 'height') ? $model->height : '',
                'width' => property_exists($model, 'width') ? $model->width : '',
                'primary' => $primary
            );
        }

        return $crop_array;
    }

    private function map_images($image_models)
    {
        $image_array = array();

        $field_id = $this->get_field_id('npr_images');
        $grid_column_names = $this->get_grid_column_names($field_id);
 
        $count = 1;
        foreach ($image_models as $model)
        {
            $crops = $this->map_image_crops($model->Crop);
            $crops[] = $this->map_image_crops($model)[0];
            foreach ($crops as $crop)
            {
                // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
                $row_name = "new_row_$count";
                
                $image = array(
                    $grid_column_names['crop_type'] => $crop['type'],
                    $grid_column_names['crop_src'] => $crop['src'],
                    $grid_column_names['crop_height'] => $crop['height'],
                    $grid_column_names['crop_width'] => $crop['width'],
                    $grid_column_names['crop_primary'] => $crop['primary'],
                    $grid_column_names['crop_has_border'] => $model->hasBorder,
                    $grid_column_names['crop_title'] => $model->title,
                    $grid_column_names['crop_caption'] => $model->caption->value,
                    $grid_column_names['crop_producer'] => $model->producer,
                    $grid_column_names['crop_provider'] => $model->provider,
                    $grid_column_names['crop_provider_url'] => $model->providerUrl,
                    $grid_column_names['copyright'] => $model->copyright,
                );
                
                $image_array['rows'][$row_name] = $image;
                $count++;
            }
        }

        return $image_array;
    }

    private function map_keywords($keywords)
    {
        // not implemented.
        // no examples of stories with keywords found.
    }

    private function map_organization($org_model)
    {
        $org_array = array();

        $field_id = $this->get_field_id('organization');
        $grid_column_names = $this->get_grid_column_names($field_id);
 
        $org_array['rows']['new_row_1'] = array(
            $grid_column_names['org_id'] => $org_model->orgId,
            $grid_column_names['org_abbr'] => $org_model->orgAbbr,
            $grid_column_names['org_name'] => $org_model->name,
            $grid_column_names['org_website'] => $org_model->website
        );      
    
        return $org_array;
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

    private function map_pullquotes($quote_models)
    {
        $quote_array = array();

        $field_id = $this->get_field_id('pullquotes');
        $grid_column_names = $this->get_grid_column_names($field_id);

        $count = 1;
        foreach ($quote_models as $model)
        {
            $row_name = "new_row_$count";

            $quote = array(
                $grid_column_names['quote_person'] = $model->person,
                $grid_column_names['quote_date'] = $model->date,
                $grid_column_names['quote_text'] = $model->text
            );

            $quote_array['rows'][$row_name] = $quote;
            $count++;
        }

        return $quote_array;
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

    private function parse_audio_permissions(array $permissions)
    {
        $allowed = array_keys($permissions, 'true');
        return implode(", ", $allowed);
    }

    private function sideload_file($model, $field = 'userfile')
    {
        $raw = file_get_contents($model->src);
        $tmpfile = '/tmp/' . basename($model->src);
        file_put_contents($tmpfile, $raw);
        
        $upload_data = array(
            'name' => basename($model->src),
            'error' => 0,
            'tmp_name' => $tmpfile
            , 'size' => 0
            , 'type' => ''
        );

        $destination = ee('Model')->get('UploadDestination', $this->settings->npr_image_destination)
			->filter('site_id', ee()->config->item('site_id'))
			->first();

        ee()->load->library('upload', array('upload_path' => dirname($destination->server_path)));
        // prep upload expectations
        $response = ee()->upload->raw_upload(basename($model->src), $raw);

        
        unlink($tmpfile);

        return;
    }
}