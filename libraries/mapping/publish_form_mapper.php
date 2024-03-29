<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/../utilities/field_utils.php';
require_once __DIR__ . '/../utilities/channel_entry_builder.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Channel_entry_builder;
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;

class Publish_form_mapper
{
    private $entry_builder;

    private $field_utils;

    private $file_manager_compatibility_mode = true;

    private $settings;

    public function __construct()
    {
        $this->entry_builder = new Channel_entry_builder();
        $this->field_utils = new Field_utils();

        if (APP_VER >= 7) {
            $compatibility_mode = ee()->config->item('file_manager_compatibility_mode');
            if ($compatibility_mode === 'n') {
                $this->file_manager_compatibility_mode = false;
            }
        }

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
        $audio_runby_date = $story->audioRunByDate ? strtotime($story->audioRunByDate) : null;
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
            'audio_runby_date' => $audio_runby_date,
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
            'url_title' => $url_title,
        );

        $objects = $this->entry_builder->assign_data_to_entry($data, $entry, $values);
        $objects['story'] = $story;
        return $objects;
    }

    private function convert_audio_duration($raw)
    {
        return ltrim(gmdate('H:i:s', $raw), "00:");
    }

    private function generate_url_title($entry, $story_title)
    {
        $url_title = $entry->isNew() ?
        (string) ee('Format')->make('Text', $story_title)->urlSlug() :
        $entry->url_title;

        if (empty($url_title)) {
            $url_title = $entry->url_title;
        }

        return $url_title;
    }

    private function map_audio($audio_models)
    {
        $audio_array = array();

        /* get column names */
        $field_id = $this->field_utils->get_field_id('audio_files');
        $grid_column_names = $this->field_utils->get_grid_column_names($field_id);

        $count = 1;
        foreach ($audio_models as $model) {
            $stream = $this->map_audio_formats($model->Format);
            if (isset($stream[0])) {
                $stream = $stream[0];
            }

            // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
            $row_name = "new_row_$count";

            $audio = array(
                $grid_column_names['audio_type'] => $model->type, // col_id => value?
                $grid_column_names['audio_duration'] => $this->convert_audio_duration($model->duration),
                $grid_column_names['audio_description'] => $model->description,
                $grid_column_names['audio_filesize'] => $stream['filesize'],
                $grid_column_names['audio_format'] => $stream['format'],
                $grid_column_names['audio_url'] => $stream['url'],
                $grid_column_names['audio_rights'] => $model->rights,
                $grid_column_names['audio_permissions'] => $this->parse_audio_permissions($model->permissions),
                $grid_column_names['audio_title'] => $model->title,
                $grid_column_names['audio_region'] => $model->region,
                $grid_column_names['audio_rightsholder'] => $model->rightsholder,
            );

            $audio_array['rows'][$row_name] = $audio;
            $count++;
        }

        return $audio_array;
    }

    private function map_audio_formats($format_models)
    {
        $preference = array('mp3', 'mp4');
        $model = null;
        foreach ($preference as $format) {
            $model = $format_models->filter('format', '==', $format)
                ->filter('filesize', '!=', '')
                ->first();
                
            if (!is_null($model)) {
                break;
            }
            
            $model = $format_models->filter('format', '==', $format)->first();
        }

        if (is_null($model)) 
        {
            return;
        }

        $format_array = array();
        $format_array[] = array(
            'type' => $model->type,
            'filesize' => $model->filesize,
            'format' => $model->format,
            'url' => $model->url,
        );

        return $format_array;
    }

    private function map_bylines($byline_models)
    {
        $names = array();
        foreach ($byline_models as $model) {
            $names[] = $model->name;
        }

        $byline = implode(', ', $names);
        return $byline;
    }

    private function map_corrections($correction_models, $entry_id)
    {
        $corrections = array();

        /* get column names */
        $field_id = $this->field_utils->get_field_id('corrections');
        $grid_column_names = $this->field_utils->get_grid_column_names($field_id);

        $count = 1;
        foreach ($correction_models as $model) {
            // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
            $row_name = "new_row_$count";

            $correction = array(
                $grid_column_names['correction_date'] => $model->correctionDate, // col_id => value?
                $grid_column_names['correction_text'] => $model->correctionText,
            );

            $corrections['rows'][$row_name] = $correction;
            $count++;
        }

        return $corrections;
    }

    private function map_image_credit($image_model)
    {
        $credit = "{$image_model->producer}/{$image_model->provider}";

        if ($image_model->copyright !== 0) {
            $credit = "Copyright {$image_model->copyright} {$credit}";
        }

        return $credit;
    }

    private function map_image_crops($crop_models)
    {
        if (!($crop_models instanceof \EllisLab\ExpressionEngine\Service\Model\Collection)) {
            $crop_models = array($crop_models);
        }

        $crop_array = array();
        foreach ($crop_models as $model) {
            $primary = property_exists($model, 'primary') && $model->primary;

            // we only care about the largest image size.
            // caution: watch for <image primary='false' /> edge case.
            if (!$primary) {
                continue;
            }

            $file_segments = $this->sideload_file($model);
            $file = $this->file_manager_compatibility_mode === true ?
                $file_segments['dir'] . $file_segments['file']->file_name :
                '{' . $file_segments['dir'] . ':' . $file_segments['file']->file_id . ':url}';

            $crop_array[] = array(
                'file' => $file,
                'type' => $model->type,
                'src' => $model->src,
                'height' => property_exists($model, 'height') ? $model->height : '',
                'width' => property_exists($model, 'width') ? $model->width : '',
                'primary' => $primary,
            );
        }

        return $crop_array;
    }

    private function map_images($image_models)
    {
        $image_array = array();

        $field_id = $this->field_utils->get_field_id('npr_images');
        $grid_column_names = $this->field_utils->get_grid_column_names($field_id);

        $count = 1;
        foreach ($image_models as $model) {
            $caption = $model->caption->value ? $model->caption->value : $model->title;
            $credit = $this->map_image_credit($model);
            $crops = $this->map_image_crops($model->Crop);

            $extra_images = $this->map_image_crops($model);
            if (array_key_exists(0, $extra_images)) {
                $crops[] = $extra_images[0];
            }
            
            foreach ($crops as $crop) {
                // we only care about the largest image size.
                if (!$crop['primary']) {
                    continue;
                }

                // should be row_id_x if row exists, but this doesn't seem to duplicate entries.
                $row_name = "new_row_$count";

                $image = array(
                    $grid_column_names['file'] => $crop['file'],
                    $grid_column_names['crop_type'] => $crop['type'],
                    $grid_column_names['crop_src'] => $crop['src'],
                    // $grid_column_names['crop_height'] => $crop['height'],
                    $grid_column_names['crop_width'] => $crop['width'],
                    $grid_column_names['crop_primary'] => $crop['primary'],
                    // $grid_column_names['crop_has_border'] => $model->hasBorder,
                    // $grid_column_names['crop_title'] => $model->title,
                    $grid_column_names['crop_caption'] => $model->caption->value,
                    // $grid_column_names['crop_producer'] => $model->producer,
                    // $grid_column_names['crop_provider'] => $model->provider,
                    $grid_column_names['crop_provider_url'] => $model->providerUrl,
                    // $grid_column_names['copyright'] => $model->copyright,
                    $grid_column_names['crop_credit'] => $credit,
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

        $field_id = $this->field_utils->get_field_id('organization');
        $grid_column_names = $this->field_utils->get_grid_column_names($field_id);

        $org_array['rows']['new_row_1'] = array(
            $grid_column_names['org_id'] => $org_model->orgId,
            $grid_column_names['org_abbr'] => $org_model->orgAbbr,
            $grid_column_names['org_name'] => $org_model->name,
            $grid_column_names['org_website'] => $org_model->website,
        );

        return $org_array;
    }

    private function map_permalinks($link_models)
    {
        $model = $link_models->filter('type', '==', 'html')->first();

        if ($model === null) {
            return null;
        }

        return $model->link;
    }

    private function map_pullquotes($quote_models)
    {
        $quote_array = array();

        $field_id = $this->field_utils->get_field_id('pullquotes');
        $grid_column_names = $this->field_utils->get_grid_column_names($field_id);

        $count = 1;
        foreach ($quote_models as $model) {
            $row_name = "new_row_$count";

            $quote = array(
                $grid_column_names['quote_person'] = $model->person,
                $grid_column_names['quote_date'] = $model->date,
                $grid_column_names['quote_text'] = $model->text,
            );

            $quote_array['rows'][$row_name] = $quote;
            $count++;
        }

        return $quote_array;
    }

    private function map_text($text_models)
    {
        $text_array = array();
        foreach ($text_models->sortBy('num') as $model) {
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
        // rename file if it'll be problematic.
        $filename = $this->strip_sideloaded_query_strings($model->src);

        $file = ee('Model')->get('File')
            ->filter('upload_location_id', $this->settings->npr_image_destination)
            ->filter('file_name', $filename)
            ->first();

        if ($file != null) {
            $dir = $this->file_manager_compatibility_mode ? 
                '{filedir_' . $this->settings->npr_image_destination . '}' : 
                'file';

            return array(
                'dir' => $dir,
                'file' => $file,
            );
        }

        $destination = ee('Model')->get('UploadDestination')
            ->filter('id', $this->settings->npr_image_destination)
            ->filter('site_id', ee()->config->item('site_id'))
            ->first();

        ee()->load->library('upload', array('upload_path' => $destination->server_path));
        // upload path should be set by library loader, but it's not.
        ee()->upload->set_upload_path($destination->server_path);

        $raw = file_get_contents($model->src);

        if (ee()->upload->raw_upload($filename, $raw) === false) {
            ee('CP/Alert')->makeInline('shared-form')
                ->asIssue()
                ->withTitle(lang('upload_filedata_error'))
                ->addToBody('')
                ->now();

            return false;
        }

        // from filemanager
        $upload_data = ee()->upload->data();

        // (try to) Set proper permissions
        @chmod($upload_data['full_path'], FILE_WRITE_MODE);
        // --------------------------------------------------------------------
        // Add file the database

        ee()->load->library('filemanager', array('upload_path' => dirname($destination->server_path)));
        $thumb_info = ee()->filemanager->get_thumb($upload_data['file_name'], $destination->id);
        
        // Build list of information to save and return
        $file_data = array(
            'upload_location_id' => $destination->id,
            'site_id' => ee()->config->item('site_id'),

            'file_name' => $upload_data['file_name'],
            'orig_name' => $filename, // name before any upload library processing
            'file_data_orig_name' => $upload_data['orig_name'], // name after upload lib but before duplicate checks

            'is_image' => $upload_data['is_image'],
            'mime_type' => $upload_data['file_type'],

            'file_thumb' => $thumb_info['thumb'],
            'thumb_class' => $thumb_info['thumb_class'],

            'modified_by_member_id' => ee()->session->userdata('member_id'),
            'uploaded_by_member_id' => ee()->session->userdata('member_id'),

            'file_size' => $upload_data['file_size'] * 1024, // Bring it back to Bytes from KB
            'file_height' => $upload_data['image_height'],
            'file_width' => $upload_data['image_width'],
            'file_hw_original' => $upload_data['image_height'] . ' ' . $upload_data['image_width'],
            'max_width' => $destination->max_width,
            'max_height' => $destination->max_height,
        );

        $is_crop = property_exists($model, 'image_id') ? true : false;

        $file_data['title'] = $filename;
        $file_data['description'] = $is_crop ? $model->Image->caption : $model->caption->value;
        $file_data['credit'] = $is_crop ? $this->map_image_credit($model->Image) : $this->map_image_credit($model);

        $saved = ee()->filemanager->save_file($upload_data['full_path'], $destination->id, $upload_data);

        if ($saved['status'] === false) {
            return;
        }

        $file = ee('Model')->get('File')
            ->filter('file_id', $saved['file_id'])
            ->limit(1)
            ->first();

        $file->title = $file_data['title'];
        $file->description = $file_data['description'];
        $file->credit = $file_data['credit'];
        $file->save();

        $dir = $this->file_manager_compatibility_mode === true ?
            '{filedir_' . $destination->id . '}' :
            'file';

        $results = array(
            'dir' => $dir,
            'file' => $file,
        );

        return $results;
    }

    private function strip_sideloaded_query_strings($url)
    {
        $url_data = parse_url($url);
        $filename = basename($url_data['path']);

        if (!array_key_exists('query', $url_data)) {
            return $filename;
        }

        $path_data = pathinfo($filename);
        $filename = "{$path_data['filename']}-{$url_data['query']}.{$path_data['extension']}";

        ee()->load->library('upload');
        $filename = ee()->upload->clean_file_name($filename);

        return $filename;
    }
}
