<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Template_mapper
{
    /**
     * Convert an NPR Story model to ExpressionEngine template tag data.
     */
    public function map($story)
    {
        $audio_array = $this->map_audio($story->Audio);
        $org_array = $this->map_organization($story->Organization);
        $thumbnail_array = $this->map_thumbnails($story->Thumbnail);
        $toenail_array = $this->map_thumbnails($story->Toenail);
        $html_assets = $this->map_html_assets($story->HtmlAsset);
        $images = $this->map_images($story->Image);
        $permalink = $this->map_permalinks($story->Link);
        $pullquotes = $this->map_pullquotes($story->PullQuote);
        $bylines = $this->map_bylines($story->Byline);
        $text = $this->map_text($story->TextWithHtml);
        $corrections = $this->map_corrections($story->Correction);

        $data = array(
            'id' => $story->id,
            'audio' => $audio_array,
            'audioRunByDate' => $story->audioRunByDate,
            'bylines' => $bylines,
            'corrections' => $corrections,
            'html_assets' => $html_assets,
            'images' => $images,
            'keywords' => $story->keywords,
            'lastModifiedDate' => $story->lastModifiedDate,
            'miniTeaser' => $story->miniTeaser,
            'organization' => $org_array,
            'permalink' => $permalink,
            'priorityKeywords' => $story->priorityKeywords,
            'pubDate' => $story->pubDate,
            'pullquotes' => $pullquotes,
            'shortTitle' => $story->shortTitle,
            'slug' => $story->slug,
            'subtitle' => $story->subtitle,
            'storyDate' => $story->storyDate,
            'teaser' => $story->teaser,
            'text' => $text,
            'thumbnails' => $thumbnail_array,
            'title' => $story->title,
            'toenails' => $toenail_array
        );

        return $data;
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
            $stream = $this->map_audio_formats($model->Format);
            if (isset($stream[0]))
            {
                $stream = $stream[0];
            }

            $audio_array[] = array(
                'type' => $model->type,
                'duration' => $model->duration,
                'description' => $model->description,
                'format' => $stream['format'],
                'url' => $stream['url'],
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
        $byline_array = array();
        foreach ($byline_models as $model)
        {
            $byline_array[] = array(
                'name' => $model->name
            );
        }
        return $byline_array;
    }

    private function map_corrections($correction_models)
    {
        $correction_array = array();
        foreach ($correction_models as $model)
        {
            $correction_array[] = array(
                'title' => $model->correctionTitle,
                'date' => $model->correctionDate,
                'text' => $model->correctionText
            );
        }

        return $correction_array;
    }

    private function map_html_assets($asset_models)
    {
        $asset_array = array();
        foreach ($asset_models as $model)
        {
            $asset_array[] = array(
                'asset' => $model->asset
            );
        }

        return $asset_array;
    }

    private function map_image_crops($crop_models)
    {
        $crop_array = array();
        foreach ($crop_models as $model)
        {
            $crop_array[] = array(
                'type' => $model->type,
                'src' => $model->src,
                'height' => $model->height,
                'width' => $model->width,
                'primary' => $model->primary
            );
        }

        return $crop_array;
    }

    private function map_images($image_models)
    {
        $image_array = array();
        foreach ($image_models as $model)
        {
            $image_array[] = array(
                'crops' => $this->map_image_crops($model->Crop),
                'type' => $model->type,
                'width' => $model->width,
                'src' => $model->src,
                'hasBorder' => $model->hasBorder,
                'title' => $model->title,
                'caption' => $model->caption,
                'link' => $model->link,
                'producer' => $model->producer,
                'provider' => $model->provider,
                'providerUrl' => $model->providerUrl,
                'copyright' => $model->copyright,
                'enlargement' => $model->enlargement,
                'enlargementCaption' => $model->enlargementCaption
            );
        }

        return $image_array;
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

    private function map_organization($org_model)
    {
        $org_array = array();
        $org_array[] = array(
            'name' => $org_model->name,
            'website' => $org_model->website
        );

        return $org_array;
    }

    private function map_pullquotes($quote_models)
    {
        $quote_array = array();
        foreach ($quote_models as $model)
        {
            $quote_array[] = array(
                'person' => $model->person,
                'date' => $model->date,
                'text' => $model->text
            );
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
}