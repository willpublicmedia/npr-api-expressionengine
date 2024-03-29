<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use ExpressionEngine\Service\Model\Model;

require_once(__DIR__ . '/../../vendor/autoload.php');
use \NPRMLElement;

class Model_story_mapper
{
    public function map_parsed_story($story): Model
    {
        // throw new \Exception('Test using stories 691846168, 690346427, 744535478 [buggy!], 734538252');
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
        
        if (property_exists($story, 'correction') && !is_null($story->correction))
        {
            $correction = is_array($story->correction) ?
                $this->process_corrections($story->correction) :
                $this->process_correction($story->correction);
            $model->Correction = $correction;
        }

        if (property_exists($story, 'organization') && !is_null($story->organization))
        {
            $model->Organization = $this->load_organization($story->organization);
        }
        
        // newsroom doesn't use plain text.
        $model->TextWithHtml = $this->process_text($story->textWithHtml, 'textWithHtml');

        if (property_exists($story, 'audio') && !is_null($story->audio))
        {
            $audio = is_array($story->audio) ? 
                $this->process_audios($story->audio) : 
                $this->process_audio($story->audio);
            $model->Audio = $audio;
        }

        if (property_exists($story, 'audioRunByDate') && !is_null($story->audioRunByDate))
        {
            $model->audioRunByDate = $this->convert_date_string($story->audioRunByDate->value);
        }

        if (property_exists($story, 'byline') && !is_null($story->byline))
        {
            $byline = is_array($story->byline) ?
                $this->process_bylines($story->byline) :
                $this->process_byline($story->byline);
            $model->Byline = $byline;
        }

        // Newsroom will use local related links.
        // if (property_exists($story, 'relatedLink')) {
        //     $model->RelatedLink = $this->process_related_links($story->relatedLink);
        // }

        if (property_exists($story, 'image') && !is_null($story->image))
        {
            $image = is_array($story->image) ?
                $this->process_images($story->image) :
                $this->process_image($story->image);
            $model->Image = $image;
        }

        if (property_exists($story, 'link') && !is_null($story->link))
        {
            $link = is_array($story->link) ?
                $this->process_permalinks($story->link) :
                $this->process_permalink($story->link);
            $model->Link = $link;
        }
        
        if (property_exists($story, 'pullQuote') && !is_null($story->pullQuote))
        {
            $quote = is_array($story->pullQuote) ?
                $this->process_pullquotes($story->pullQuote) :
                $this->process_pullquote($story->pullQuote);
            $model->PullQuote = $quote;
        }

        if (property_exists($story, 'thumbnail') && !is_null($story->thumbnail))
        {
            $model->Thumbnail = $this->process_thumbnail($story->thumbnail);
        }

        if (property_exists($story, 'toenail') && !is_null($story->toenail))
        {
            $model->Toenail = $this->process_thumbnail($story->toenail);
        }

        // move this to channel behavior
        //$model->slug = ee('Format')->make('Text', $story->slug->value)->urlSlug(['separator' => '-', 'lowercase' => TRUE]);
        return $model;
    }

    private function convert_date_string($date_string)
    {
        $date = $date_string == '' ? NULL : date('Y-m-d H:i:s', strtotime($date_string));
        return $date;
    }

    private function load_base_model($story_id)
    {
        if (ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->count() > 0)
        {
            $model = ee('Model')->get('npr_story_api:Npr_story')->filter('id', $story_id)->first();
            $model->TextWithHtml = NULL;
            return $model;
        }    
        
        $model = ee('Model')->make('npr_story_api:Npr_story');
        $model->id = $story_id;

        return $model;
    }
    
    private function load_organization(\NPRMLElement $org_element)
    {
        $org = null;

        if (ee('Model')->get('npr_story_api:Npr_organization')->filter('orgId', $org_element->orgId)->count() > 0)
        {
            $org = ee('Model')->get('npr_story_api:Npr_organization')->filter('orgId', $org_element->orgId)->first();
        }
        else
        {
            $org = ee('Model')->make('npr_story_api:Npr_organization');
        }

        $org->orgId = $org_element->orgId;
        $org->orgAbbr = $org_element->orgAbbr;
        $org->name = $org_element->name->value;
        $org->website = $org_element->website->value;
        $org->save();

        return $org;
    }

    private function process_audio(\NPRMLElement $audio_element)
    {
        $audio = null;

        if (ee('Model')->get('npr_story_api:Npr_audio')->filter('id', $audio_element->id)->count() > 0)
        {
            $audio = ee('Model')->get('npr_story_api:Npr_audio')->filter('id', $audio_element->id)->first();
        }
        else
        {
            $audio = ee('Model')->make('npr_story_api:Npr_audio');
        }

        $audio->id = $audio_element->id;
        $audio->title = $audio_element->title->value;
        $audio->duration = $audio_element->duration->value;
        $audio->description = $audio_element->description->value;
        $audio->region = $audio_element->region->value;
        $audio->rightsholder = $audio_element->rightsHolder->value;
        $audio->type = $audio_element->type;
        
        $audio->permissions = $this->process_permissions($audio_element->permissions);
        $audio->Format = $this->process_audio_format($audio_element->format);
        // filesize

        $audio->save();
        return $audio;
    }

    private function process_audios(array $audio_element_array)
    {
        $audios = array();
        foreach ($audio_element_array as $audio_element)
        {
            $audio = $this->process_audio($audio_element);
            $audios[] = $audio;
        }

        return $audios;
    }

    private function process_audio_format(\NPRMLElement $format_element, $formats = array())
    {
        foreach ($format_element as $key => $value)
        {
            $is_nprml = $value instanceof \NPRMLElement;
            if (is_array($value) && $is_nprml === false)
            {
                foreach ($value as $child)
                {
                    $element = new \NPRMLElement();
                    $element->{$key} = $child;
                    $formats = $this->process_audio_format($element, $formats);
                }
            }

            // $value is often a single-element array.
            $format_data = is_array($value) ? array_pop($value) : $value;

            if (is_null($format_data)) {
                continue;
            }

            $url = $format_data->value;
            $type = $format_data->type;
            
            if (ee('Model')->get('npr_story_api:Npr_audio_format')->filter('url', $url)->filter('type', $type)->count() > 0)
            {
                $model = ee('Model')->get('npr_story_api:Npr_audio_format')->filter('url', $url)->filter('type', $type)->first();
            }
            else
            {
                $model = ee('Model')->make('npr_story_api:Npr_audio_format');
            }

            $model->format = $key;
            $model->url = $url;

            if (\property_exists($format_data, 'type') && !is_null($format_data->type)) {
                $model->type = $format_data->type;
            }

            if (\property_exists($format_data, 'fileSize') && !is_null($format_data->fileSize))
            {
                $model->filesize = $format_data->fileSize;
            }

            $model->save();
            $formats[] = $model;
        }

        return $formats;
    }
    
    private function process_byline(\NPRMLElement $byline_element)
    {
        $id = $byline_element->id;

        $byline;
        if (ee('Model')->get('npr_story_api:Npr_byline')->filter('byline_id', $id)->count() > 0)
        {
            $byline = ee('Model')->get('npr_story_api:Npr_byline')->filter('byline_id', $id)->first();
        }
        else
        {
            $byline = ee('Model')->make('npr_story_api:Npr_byline');
            $byline->byline_id = $id;
        }

        $byline->name = $byline_element->name->value;
        $byline->personId = $byline_element->name->personId;

        $byline->save();
        return $byline;
    }

    private function process_bylines(array $byline_element_array)
    {
        $bylines = array();
        foreach ($byline_element_array as $byline_element)
        {
            $byline = $this->process_byline($byline_element);
            $bylines[] = $byline;
        }

        return $bylines;
    }

    private function process_correction(\NPRMLElement $correction_element)
    {
        $date = $correction_element->correctionDate->value;

        $correction;
        if (ee('Model')->get('npr_story_api:Npr_pull_correction')->filter('correctionDate', $date)->count() > 0)
        {
            $correction = ee('Model')->get('npr_story_api:Npr_pull_correction')->filter('correctionDate', $date)->first();
        }
        else
        {
            $correction = ee('Model')->make('npr_story_api:Npr_pull_correction');
            $correction->correctionDate = $date;
        }

        $correction->correctionText = $correction_element->correctionText->value;
        $correction->correctionTitle = $correction_element->correctionTitle->value;

        return $correction;
    }

    private function process_corrections(array $corrections_element_array)
    {
        $corrections = array();
        foreach ($corrections_element_array as $correction_element)
        {
            $correction = $this->process_correction($correction_element);
            $corrections[] = $correction;
        }

        return $corrections;
    }

    private function process_image(\NPRMLElement $image_element)
    {
        $id = $image_element->id;

        $model;
        if (ee('Model')->get('npr_story_api:Npr_image')->filter('id', $id)->count() > 0)
        {
            $model = ee('Model')->get('npr_story_api:Npr_image')->filter('id', $id)->first();
        }
        else
        {
            $model = ee('Model')->make('npr_story_api:Npr_image');
            $model->id = $id;
        }

        $model->type = $image_element->type;
        $model->width = $image_element->width;
        $model->src = $image_element->src;
        $model->hasBorder = ($image_element->hasBorder === 'true');
        $model->title = $image_element->title->value;
        $model->caption = $image_element->caption;
        $model->link = $image_element->link->url;
        $model->producer = $image_element->producer->value;
        $model->provider = $image_element->provider->value;
        $model->providerUrl = $image_element->provider->url;
        if (\property_exists($image_element, 'copyright'))
        {
            $model->copyright = intval($image_element->copyright->value);
        }
        
        if (\property_exists($image_element, 'enlargement')) {
            if (\property_exists($image_element, 'src')) 
            {
                $model->enlargement = $image_element->enlargement->src;
            }
            
            if (\property_exists($image_element, 'src'))
            {
                $model->enlargementCaption = $image_element->enlargement->caption->value;
            }
        }


        if (property_exists($image_element, 'crop'))
        {
            $crops = is_array($image_element->crop) ?
                $image_element->crop :
                array($image_element->crop);
            $model->Crop = $this->process_image_crops($crops);
        }

        $model->save();
        return $model;
    }

    private function process_images(array $image_array)
    {
        $images = array();
        foreach ($image_array as $image_element)
        {
            $model = $this->process_image($image_element);
            $images[] = $model;
        }
        
        return $images;
    }

    private function process_image_crop(\NPRMLElement $crop_element)
    {
        $model;
        $type = $crop_element->type;
        $width = $crop_element->width;
        $height = $crop_element->height;
        $primary = (property_exists($crop_element, 'primary') && $crop_element->primary === 'true');

        if (ee('Model')->get('npr_story_api:Npr_image_crop')->filter('type', $type)->filter('width', $width)->count() > 0)
        {
            $model = ee('Model')->get('npr_story_api:Npr_image_crop')->filter('type', $type)->filter('width', $width)->first();
        }
        else
        {
            $model = ee('Model')->make('npr_story_api:Npr_image_crop');
            $model->type = $type;
            $model->width = $width;
        }

        $model->height = $height;
        $model->primary = $primary;
        $model->src = $crop_element->src;

        $model->save();
        return $model;
    }

    private function process_image_crops(array $crop_element_array)
    {
        $crops = array();

        foreach ($crop_element_array as $crop_element)
        {
            $model = $this->process_image_crop($crop_element);
            $crops[] = $model;
        }

        return $crops;
    }

    private function process_permalink(\NPRMLElement $link_element)
    {
        $link = $link_element->value;
        $model;
        if (ee('Model')->get('npr_story_api:Npr_permalink')->filter('link', $link)->count() > 0) 
        {
            $model = ee('Model')->get('npr_story_api:Npr_permalink')->filter('link', $link)->first();
        }
        else
        {
            $model = ee('Model')->make('npr_story_api:Npr_permalink');
            $model->link = $link;
        }
        
        $model->type = $link_element->type;
        $model->save();
        return $model;
    }

    private function process_permalinks(array $element_array)
    {
        $links = array();
        foreach ($element_array as $link_element)
        {
            $model = $this->process_permalink($link_element);
            $links[] = $model;
        }

        return $links;
    }

    private function process_permissions(\NPRMLElement $permissions_element)
    {
        $expected = ['download', 'embed', 'stream'];
        
        $permissions = array();

        foreach ($expected as $mode) {
            if (!property_exists($permissions_element, $mode))
            {
                continue;
            }

            if (!property_exists($permissions_element->$mode, 'allow'))
            {
                continue;
            }

            $permissions[$mode] = $permissions_element->$mode->allow;
        }

        return $permissions;
    }

    private function process_pullquote(\NPRMLElement $pullquote_element)
    {
        $id = $pullquote_element->id;

        $model;
        if (ee('Model')->get('npr_story_api:Npr_pull_quote')->filter('id', $id)->count() > 0)
        {
            $model = ee('Model')->get('npr_story_api:Npr_pull_quote')->filter('id', $id)->first();
        }
        else
        {
            $model = ee('Model')->make('npr_story_api:Npr_pull_quote');
            $model->id = $id;
        }

        $model->text = $pullquote_element->text->value;
        $model->person = $pullquote_element->person->value;
        $model->date = $this->convert_date_string($pullquote_element->date->value);

        $model->save();
        return $model;
    }

    private function process_pullquotes(array $pullquote_element_array)
    {
        $pullquotes = array();

        foreach ($pullquote_element_array as $pullquote_element)
        {
            $quote = $this->process_pullquote($pullquote_element);
            $pullquotes[] = $quote;
        }

        return $pullquotes;
    }
    
    private function process_text(NPRMLElement $text_element, $paragraph_type)
    {
        $paragraphs = array();

        foreach ($text_element->paragraphs as $text_element)
        {
            $paragraph = ee('Model')->make('npr_story_api:Npr_text_paragraph');
            $paragraph->text = $text_element->value;
            $paragraph->paragraphType = $paragraph_type;
            $paragraph->num = $text_element->num;

            $paragraph->save();
            $paragraphs[] = $paragraph;
        }

        return $paragraphs;
    }

    private function process_thumbnail(\NPRMLElement $thumbnail_element)
    {
        $provider = $thumbnail_element->provider->value;
        
        $models = array();
        $model;
        foreach ($thumbnail_element as $key => $value)
        {
            if ($key === 'provider' || is_null($value))
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
}