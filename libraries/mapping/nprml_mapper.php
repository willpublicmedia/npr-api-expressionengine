<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/compatibility/ipm_compatibility.php';
require_once __DIR__ . '/../utilities/field_utils.php';
use IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Compatibility\Ipm_compatibility;
use IllinoisPublicMedia\NprStoryApi\Libraries\Utilities\Field_utils;

class Nprml_mapper
{
    private $field_utils;

    public function __construct()
    {
        $this->field_utils = new Field_utils();
    }

    public function map(&$entry, $values)
    {
        $npr_story = $this->nprstory_post_to_nprml_story($entry, $values);
        $doc = array();
        $doc[] = array(
            'tag' => 'list',
            'children' => array(array('tag' => 'story', 'children' => $npr_story)),
        );
        $nprml = $this->nprstory_nprml_array_to_xml('nprml', array('version' => '0.93'), $doc);
        return $nprml;
    }

    private function convert_audio($audio_data)
    {
        $audio = array();
        foreach ($audio_data as $data) {
            $caption = $data['audio_description'] == '' ?
            $data['audio_title'] :
            $data['audio_description'];

            $format = $this->get_audio_format($data);

            $audio[] = array(
                'tag' => 'audio',
                'children' => array(
                    array(
                        'tag' => 'format',
                        'children' => array(
                            array(
                                'tag' => $data['audio_format'],
                                'text' => $data['audio_url'],
                            ),
                        ),
                    ),
                    array(
                        'tag' => 'description',
                        'text' => $caption,
                    ),
                    array(
                        'tag' => 'duration',
                        'text' => $data['audio_duration'],
                    ),
                ),
            );
        }

        return $audio;
    }

    private function convert_images($image_data, $custom_media_credit, $custom_media_agency, $dist_media_option = array())
    {
        $use_custom = false;
        $images = array();
        foreach ($image_data as $data) {
            // Check for image in content and assign a corepublisher flag.
            // WordPress may add something like "-150X150" to the end of the filename, before the extension.
            // $image_name_parts = explode( ".", $data["file"] );
            // $image_regex = "/" . $image_name_parts[0] . "\-[a-zA-Z0-9]*" . $image_name_parts[1] . "/";
            $in_body = "";
            // if ( preg_match( $image_regex, $content ) ) {
            //     if ( strstr( $data["file"], '?') ) {
            //         $in_body = "&origin=body";
            //     } else {
            //         $in_body = "?origin=body";
            //     }
            // }

            $manipulations = $this->get_manipulations($data);
            $crops = $this->create_image_crops($manipulations);

            // set default crop type
            $image_type = $data['crop_primary'] == true ? 'primary' : 'standard';
            $tag_data = array(
                'tag' => 'image',
                'attr' => array('src' => $data['crop_src'] . $in_body, 'type' => $image_type),
                'children' => array(
                    array(
                        'tag' => 'title',
                        'text' => $data['crop_title'],
                    ),
                    array(
                        'tag' => 'caption',
                        'text' => $data['crop_caption'],
                    ),
                    array(
                        'tag' => 'producer',
                        'text' => $custom_media_credit,
                    ),
                    array(
                        'tag' => 'provider',
                        'text' => $custom_media_agency,
                    ),
                ),
            );

            foreach ($crops as $crop) {
                $tag_data['children'][] = $crop;
            }

            $images[] = $tag_data;
        }

        return $images;
    }

    private function create_image_crops($manipulations): array
    {
        $crops = array();
        foreach ($manipulations as $manipulation) {
            $crops[] = array(
                'tag' => 'crop',
                'attr' => array(
                    'type' => $manipulation['type'],
                    'src' => $manipulation['src'],
                    // 'height' => $manipulation['height'],
                    'width' => $manipulation['width'],
                ),
            );
        }

        return $crops;
    }

    private function get_audio_format($data)
    {
        throw new \Exception("Not implemented. Fetch from audio file.", 1);
    }

    private function get_bylines($entry, $split_bylines = false)
    {
        $byline_field = $this->field_utils->get_field_name('byline');
        $byline_value = $entry->{$byline_field};

        if (empty($byline_value)) {
            $author = $entry->Author->screen_name;
            return array($author);
        }

        $bylines = $split_bylines ?
        explode(', ', $byline_value) :
        array($byline_value);

        return $bylines;
    }

    private function get_content($entry)
    {
        $content_field = $this->field_utils->get_field_name('text');
        return $entry->{$content_field};
    }

    private function get_date($format = 'D, d M Y H:i:s +0000', $field, $localize, $entry)
    {
        $field_name = $this->field_utils->get_field_name($field);
        $data = $entry->{$field_name};
        if (empty($data)) {
            return false;
        }

        $date = ee()->localize->format_date($format, $data, $localize);

        return $date;
    }

    private function get_file_id($file_src)
    {
        $image_url_data = parse_url($file_src);
        $image_path = ltrim($image_url_data['path'], '/');
        $image_path_elements = explode('/', $image_path);
        $filename = array_pop($image_path_elements);

        $file_id = ee()->db->select('file_id')
            ->from('files')
            ->where(array(
                'file_name' => $filename,
            ))
            ->limit(1)
            ->get()
            ->row()
            ->file_id;

        return $file_id;
    }

    private function get_manipulations($image_data): array
    {
        $file = ee('Model')->get('File')->filter('file_id', $image_data['file_id'])->first();
        if ($file === null) {
            return array();
        }

        $destinations = $file->UploadDestination;
        $dimensions = $destinations->FileDimensions;

        $manipulations = array();
        foreach ($dimensions as $dimension) {
            $src = rtrim($destinations->url, '/') . "/_" . $dimension->short_name . "/" . $file->file_name;
            $manipulation = [
                'type' => $dimension->short_name,
                'src' => $src,
                'height' => $dimension->height,
                'width' => $dimension->width,
            ];

            $manipulations[] = $manipulation;
        }

        return $manipulations;
    }

    private function get_media($entry, $field_name)
    {
        $content_type = 'channel';
        ee()->load->model('grid_model');
        $media_field_id = $this->field_utils->get_field_id($field_name);

        // map column names
        $columns = ee()->grid_model->get_columns_for_field($media_field_id, $content_type);

        // get entry data
        $entry_data = ee()->grid_model->get_entry_rows($entry->entry_id, $media_field_id, $content_type, null);

        // loop entry data rows
        $media = array();
        foreach ($entry_data[$entry->entry_id] as $row) {
            $row_data = array();

            // map column data to column names
            foreach ($columns as $column_id => $column_details) {
                $column_name = $column_details['col_name'];
                $row_column = "col_id_$column_id";
                $row_col_data = $row[$row_column];
                $row_data[$column_name] = $row_col_data;
            }

            // get filename from possible url
            $file_id = $this->get_file_id($row_data['crop_src']);
            $row_data['file_id'] = $file_id;

            $media[] = $row_data;
        }

        return $media;
    }

    /**
     * If you have configured any Permissions Groups for content you distribute through the NPR Story API you can optionally add them in the NPR Permissions setting.
     * Note that by default all content in the NPR Story API is open to everyone, unless you restrict access to a Permissions Group.
     * For more on setting these up see the
     * [NPR Story API Content Permissions Control page](https://nprsupport.desk.com/customer/en/portal/articles/1995557-npr-api-content-permissions-control).
     */
    private function get_npr_story_default_permission()
    {
        // use defaults.
        return array();
    }

    private function get_option($option_name)
    {
        $option_value;
        switch ($option_name) {
            case 'dp_npr_push_use_custom_map':
                $option_value = false;
                break;
            case 'ds_npr_api_mapping_body':
                $option_value = array();
                break;
            default:
                $option_value = false;
        }
        return $option_value;
    }

    private function get_permalink($entry)
    {
        $site_url = rtrim(ee()->config->item('site_url'), '/');

        $channel_url = $entry->Channel->comment_url;
        $channel_url = ltrim($channel_url, '/');
        $channel_url = rtrim($channel_url, '/');

        $url = "$site_url/$channel_url/" . $entry->url_title;
        return $url;
    }

    private function get_teaser($entry)
    {
        $teaser_field = $this->field_utils->get_field_name('teaser');

        if (empty($entry->{$teaser_field})) {
            return '';
        }

        $teaser_text = $entry->{$teaser_field};

        $compat = new Ipm_compatibility();
        $teaser_text = $compat->strip_tags($teaser_text);

        return $teaser_text;
    }

    /**
     * Helper function to get the post expiry datetime
     *
     * The datetime is stored in post meta _nprone_expiry_8601
     * This assumes that the post has been published
     *
     * @param WP_Post|int $post the post ID or WP_Post object
     * @return DateTime the DateTime object created from the post expiry date
     * @see note on DATE_ATOM and DATE_ISO8601 https://secure.php.net/manual/en/class.datetime.php#datetime.constants.types
     * @uses nprstory_get_datetimezone
     * @since 1.7
     * @todo rewrite this to use fewer queries, so it's using the WP_Post internally instead of the post ID
     */
    public function nprstory_get_post_expiry_datetime($entry)
    {
        // TODO: Not implemented
        $expiration_field = $this->field_utils->get_field_name('audio_runby_date');
        $expiration = $entry->{$expiration_field};

        if ($expiration == '') {
            $entry_date = new \DateTime();
            $entry_date->setTimestamp($entry->entry_date);
            $expiration = $entry_date->add(new \DateInterval('P7D'));
        }

        // $timezone = nprstory_get_datetimezone();

        // if ( empty( $iso_8601 ) ) {
        //     // return DateTime for the publish date plus seven days
        //     $future = get_the_date( DATE_ATOM, $post ); // publish date
        //     return date_add( date_create( $future, $timezone ), new DateInterval( 'P7D' ) );
        // } else {
        //     // return DateTime for the expiry date
        //     return date_create( $iso_8601, $timezone );
        // }
        return $expiration;
    }

    private function nprstory_nai_get_excerpt($text, $word_count = 30)
    {
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);

        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1,
            PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            //$text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        return $text;
    }

    /**
     * convert a PHP array to XML
     */
    public function nprstory_nprml_array_to_xml($tag, $attrs, $data)
    {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $root = $xml->createElement($tag);
        foreach ($attrs as $k => $v) {
            $root->setAttribute($k, $v);
        }
        foreach ($data as $item) {
            $elemxml = $this->nprstory_nprml_item_to_xml($item, $xml);
            $root->appendChild($elemxml);
        }
        $xml->appendChild($root);
        return $xml->saveXML();
    }

    private function nprstory_nprml_split_paragraphs($html)
    {
        $parts = array_filter(
            array_map('trim', preg_split("/<\/?p>/", $html))
        );
        $graphs = array();
        $num = 1;
        foreach ($parts as $part) {
            $graphs[] = array(
                'tag' => 'paragraph',
                'attr' => array('num' => $num),
                'cdata' => $part,
            );
            $num++;
        }
        return $graphs;
    }

    /**
     * convert a loosely-defined item to XML
     *
     * @todo figure out way for this to safely fail
     *
     * @param Array $item Must have a key 'tag'
     * @param DOMDocument $xml
     */
    public function nprstory_nprml_item_to_xml($item, $xml)
    {
        if (!array_key_exists('tag', $item)) {
            error_log("Unable to convert NPRML item to XML: no tag for: " . print_r($item, true)); // debug use
            // this should actually be a serious error
        }
        $elem = $xml->createElement($item['tag']);
        if (array_key_exists('children', $item)) {
            foreach ($item['children'] as $child) {
                $childxml = $this->nprstory_nprml_item_to_xml($child, $xml);
                $elem->appendChild($childxml);
            }
        }
        if (array_key_exists('text', $item)) {
            $elem->appendChild(
                $xml->createTextNode($item['text'])
            );
        }
        if (array_key_exists('cdata', $item)) {
            $elem->appendChild(
                $xml->createCDATASection($item['cdata'])
            );
        }
        if (array_key_exists('attr', $item)) {
            foreach ($item['attr'] as $attr => $val) {
                $elem->setAttribute($attr, $val);
            }
        }
        return $elem;
    }

    private function nprstory_post_to_nprml_story(&$entry, $values)
    {
        /**
         * permalink
         */
        $story = array();
        $story[] = array(
            'tag' => 'link',
            'attr' => array('type' => 'html'),
            'text' => $this->get_permalink($entry),
        );

        /**
         * map custom fields
         */
        $use_custom = $this->get_option('dp_npr_push_use_custom_map');

        /**
         * content
         */
        $content = $this->get_content($entry);

        /**
         * teaser
         */
        $teaser_text = $this->get_teaser($entry);
        if (empty($teaser_text)) {
            $teaser_text = $this->nprstory_nai_get_excerpt($content);
        }

        $story[] = array(
            'tag' => 'teaser',
            'text' => $teaser_text,
        );

        /*
         * title
         */
        $story[] = array(
            'tag' => 'title',
            'text' => $entry->title,
        );

        /**
         * Bylines
         *
         * Use byline contributor values if present. Otherwise use the post author.
         */
        $bylines = $this->get_bylines($entry);
        foreach ($bylines as $contributor) {
            $story[] = array(
                'tag' => 'byline',
                'children' => array(
                    array(
                        'tag' => 'name',
                        'text' => $contributor,
                    ),
                ),
            );
        }

        /*
         * Send to NPR One
         *
         * If the box is checked, the value here is '1'
         * @see nprstory_save_send_to_one
         */
        $send_to_one = $entry->{$this->field_utils->get_field_name('send_to_one')};
        if ($send_to_one === 1) {
            $story[] = array(
                'tag' => 'parent',
                'attr' => array('id' => '319418027', 'type' => 'collection'),
            );
        }

        /*
         * This story should be featured in NPR One
         *
         * If the box is checked, the value here is '1'
         * @see nprstory_save_nprone_featured
         */
        $nprone_featured = $entry->{$this->field_utils->get_field_name('send_to_one')};
        if ($send_to_one === 1 && $nprone_featured === 1) {
            $story[] = array(
                'tag' => 'parent',
                'attr' => array('id' => '500549367', 'type' => 'collection'),
            );
        }

        /*
         * Mini Teaser (not yet implemented)
         * Slug (not yet implemented)
         */
        #'miniTeaser' => array( 'text' => '' ),
        #'slug' => array( 'text' => '' ),

        /*
         * Dates and times
         */
        if ($entry->{$this->field_utils->get_field_name('pub_date')} === null) {
            $entry->{$this->field_utils->get_field_name('pub_date')} = $entry->edit_date;
        }

        $story[] = array(
            'tag' => 'pubDate',
            'text' => ee()->localize->format_date('%r', $entry->{$this->field_utils->get_field_name('pub_date')}, true),
        );

        $story_date = $entry->{$this->field_utils->get_field_name('story_date')};
        if ($story_date === null || $story_date === 0) {
            $entry->{$this->field_utils->get_field_name('story_date')} = $entry->edit_date;
        }

        $story[] = array(
            'tag' => 'storyDate',
            'text' => ee()->localize->format_date('%r', $entry->{$this->field_utils->get_field_name('story_date')}, true),
        );

        $edit_date = ee()->localize->format_date('%r', $entry->edit_date, true);
        $entry->{$this->field_utils->get_field_name('last_modified_date')} = $entry->edit_date;
        $story[] = array(
            'tag' => 'lastModifiedDate',
            'text' => $edit_date,
        );

        $story[] = array(
            'tag' => 'partnerId',
            'text' => $entry->entry_id,
        );

        // NPR One audio run-by date
        $datetime = $this->nprstory_get_post_expiry_datetime($entry); // if expiry date is not set, returns publication date plus 7 days
        if ($datetime instanceof \DateTime) {
            $story[] = array(
                'tag' => 'audioRunByDate',
                'text' => date_format($datetime, 'j M Y H:i:00 O'), // 1 Oct 2017 01:00:00 -0400, 29 Feb 2020 23:59:00 -0500
            );

            $entry->{$this->field_utils->get_field_name('audio_runby_date')} = $datetime->getTimestamp();
        }

        /*
         * @TODO:  When the API accepts sending both text and textWithHTML, send a totally bare text.
         * Don't do do_shortcode().
         *
         * For now (using the npr story api) we can either send text or textWithHTML, not both.
         * It would be nice to send text after we strip all html and shortcodes, but we need the html
         * and sending both will duplicate the data in the API
         */
        $story[] = array(
            'tag' => 'textWithHtml',
            'children' => $this->nprstory_nprml_split_paragraphs($content),
        );

        $perms_group = $this->get_npr_story_default_permission();
        if (!empty($perms_group)) {
            $story[] = array(
                'tag' => 'permissions',
                'children' => array(
                    array(
                        'tag' => 'permGroup',
                        'attr' => array('id' => $perms_group),
                    ),
                ),
            );
        }

        /*
         * Attach images to the post
         */
        $images = $this->get_media($entry, 'npr_images');
        $image_credits = $this->process_image_credits($images);
        $custom_media_agency = $image_credits['media_agency'];
        $custom_media_credit = $image_credits['media_credit'];
        $images = $this->convert_images($images, $custom_media_credit, $custom_media_agency);
        foreach ($images as $image) {
            $story[] = $image;
        }

        /*
         * Attach audio to the post
         */
        $audio = $this->get_media($entry, 'audio_files');
        $audio = $this->convert_audio($audio);
        foreach ($audio as $item) {
            $story[] = $item;
        }

        /*
         * The story has been assembled; now we shall return it
         */
        return $story;
    }

    private function process_image_credits($image_data)
    {
        $credits = array();
        foreach ($image_data as $data) {
            if ($data['crop_primary'] === 1) {
                $components = explode("/", $data['crop_credit']);
                $credits['media_credit'] = $components[0];
                $credits['media_agency'] = count($components) > 1 ? $components[1] : null;
            }
        }
        return $credits;
    }
}
