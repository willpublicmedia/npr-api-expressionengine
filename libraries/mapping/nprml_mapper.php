<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/compatibility/ipm_compatibility.php');
use \NPRMLElement;
use IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Compatibility\Ipm_compatibility;

class Nprml_mapper
{
    public function map(&$entry, $values)
    {
        $npr_story = $this->nprstory_post_to_nprml_story($entry, $values);
        $doc = array();
        $doc[] = array(
            'tag' => 'list',
            'children' => array( array( 'tag' => 'story', 'children' => $npr_story ), ),
        );
        $nprml = $this->nprstory_nprml_array_to_xml( 'nprml', array( 'version' => '0.93' ), $doc );
        return $nprml;
    }

    private function convert_audio($audio_data)
    {
        $audio = array();
        foreach ($audio_data as $data) {
            $caption = $data['audio_description'] == '' ? 
                $data['audio_title'] : 
                $data['audio_description'];

            $audio[] = array(
                'tag' => 'audio',
                'children' => array(
                    array(
                        'tag' => 'format',
                        'children' => array (
                            array(
                                'tag' => $data['audio_format'],
                                'text' => $data['audio_url'],
                            )
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
        foreach ($image_data as $data)
        {
            // Check for image in content and assign a corepublisher flag.
            // WordPress may add something like "-150X150" to the end of the filename, before the extension.
            $image_name_parts = explode( ".", $image_guid );
            $image_regex = "/" . $image_name_parts[0] . "\-[a-zA-Z0-9]*" . $image_name_parts[1] . "/"; 
            $in_body = "";
            if ( preg_match( $image_regex, $content ) ) {
                if ( strstr( $image->guid, '?') ) {
                    $in_body = "&origin=body";
                } else {
                    $in_body = "?origin=body";
                }
            }

            // set default crop type
            $image_type = $data['crop_primary'] == true ? 'primary' : 'standard';
            $images[] = array(
                'tag' => 'image',
                'attr' => array( 'src' => $data['crop_src'] . $in_body, 'type' => $image_type ),
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
                        'text' => $custom_media_credit
                    ),
                    array(
                        'tag' => 'provider',
                        'text' => $custom_media_agency
                    )
                ),
            );
        }

        return $images;
    }
    
    private function get_bylines($entry)
    {
        $byline_field = $this->get_field_name('byline');
        $byline_value = $entry->{$byline_field};
        
        $bylines = empty($byline_value) ?
            array($entry->author) :
            explode(", ", $byline_value);

        return $bylines;
    }

    private function get_content($entry)
    {
        $content_field = $this->get_field_name('text');
        return $entry->{$content_field};
    }

    private function get_date($format = 'D, d M Y H:i:s +0000', $field, $localize, $entry)
    {
        $field_name = $this->get_field_name($field);
        $data = $entry->{$field_name};
        if (empty($data))
        {
            return FALSE;
        }

        $date = ee()->localize->format_date($format, $data, $localize);

        return $date;
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

    private function get_file_id($file_src)
    {
        $image_url_data = parse_url($file_src);
        $image_path = ltrim($image_url_data['path'], '/');
        $image_path_elements = explode('/', $image_path);
        $filename = array_pop($image_path_elements);
        
        $file_id = ee()->db->select('file_id')
            ->from('files')
            ->where(array(
                'file_name' => $filename
                ))
                ->limit(1)
                ->get()
                ->row()
                ->file_id;
        
        return $file_id;
    }

    private function get_media($entry, $field_name)
    {
        $content_type = 'channel';
        ee()->load->model('grid_model');
        $media_field_id = $this->get_field_id($field_name);
        
        // map column names
        $columns = ee()->grid_model->get_columns_for_field($media_field_id, $content_type);
		
        // get entry data
        $entry_data = ee()->grid_model->get_entry_rows($entry->entry_id, $media_field_id, $content_type, null);
        
        // loop entry data rows
        $media = array();
        foreach ($entry_data[$entry->entry_id] as $row)
        {
            $row_data = array();

            // map column data to column names
            foreach ($columns as $column_id => $column_details)
            {
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

    private function get_media_agency($image_data)
    {
        $media_agency = '';
        foreach ($image_data as $data)
        {
            if ($data['crop_primary'] === 1)
            {
                $media_agency = $data['crop_provider'];
            }
        }
        
        return $media_agency;
    }

    private function get_media_credit($image_data)
    {
        $media_credit = '';
        foreach ($image_data as $data)
        {
            if ($data['crop_primary'] === 1)
            {
                $media_credit = $data['crop_producer'];
            }
        }
        
        return $media_credit;
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
        switch ($option_name)
        {
            case 'dp_npr_push_use_custom_map':
                $option_value = FALSE;
                break;
            case 'ds_npr_api_mapping_body':
                $option_value = array();
                break;
            default:
                $option_value = FALSE;
        }
        return $option_value;
    }

    private function get_partnerId()
    {
        $id = ee()->db->select('org_id')
            ->from('npr_story_api_settings')
            ->limit(1)
            ->get()
            ->row('org_id');

        return $id;
    }

    private function get_permalink($entry)
    {
        return $entry->entry_id;
    }

    private function get_teaser($entry)
    {   
        $teaser_field = $this->get_field_name('teaser');
        
        if ( empty( $entry->{$teaser_field} ) )
        {
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
    function nprstory_get_post_expiry_datetime( $entry )
    {
        // TODO: Not implemented
        $expiration_field = $this->get_field_name('audio_runby_date');
        $expiration = $entry->{$expiration_field};

        if ($expiration == '')
        {
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
        $text = str_replace( ']]>', ']]&gt;', $text );
        $text = strip_tags( $text );

        $words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, 
                             PREG_SPLIT_NO_EMPTY );
        if ( count( $words ) > $excerpt_length ) {
            array_pop( $words );
            $text = implode( ' ', $words );
            //$text = $text . $excerpt_more;
        } else {
            $text = implode( ' ', $words );
        }
        return $text;
    }

    /**
     * convert a PHP array to XML
     */
    function nprstory_nprml_array_to_xml( $tag, $attrs, $data ) {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $root = $xml->createElement( $tag );
        foreach ( $attrs as $k => $v ) {
            $root->setAttribute( $k, $v );
        }
        foreach ( $data as $item ) {
            $elemxml = $this->nprstory_nprml_item_to_xml( $item, $xml );
            $root->appendChild( $elemxml );
        }
        $xml->appendChild( $root );
        return $xml->saveXML();
    }

    private function nprstory_nprml_split_paragraphs( $html ) {
        $parts = array_filter( 
            array_map( 'trim', preg_split( "/<\/?p>/", $html ) ) 
        );
        $graphs = array();
        $num = 1;
        foreach ( $parts as $part ) {
            $graphs[] = array( 
                'tag' => 'paragraph',
                'attr' => array( 'num' => $num ),
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
    function nprstory_nprml_item_to_xml( $item, $xml ) {
        if ( ! array_key_exists( 'tag', $item ) ) {
            error_log( "Unable to convert NPRML item to XML: no tag for: " . print_r( $item, true ) ); // debug use
            // this should actually be a serious error
        }
        $elem = $xml->createElement( $item[ 'tag' ] );
        if ( array_key_exists( 'children', $item ) ) {
            foreach ( $item[ 'children' ] as $child ) {
                $childxml = $this->nprstory_nprml_item_to_xml( $child, $xml );
                $elem->appendChild( $childxml );
            }
        }
        if ( array_key_exists( 'text', $item ) ) { 
            $elem->appendChild(
                $xml->createTextNode( $item[ 'text' ] )
            );
        }
        if ( array_key_exists( 'cdata', $item ) ) { 
            $elem->appendChild(
                $xml->createCDATASection( $item[ 'cdata' ] )
            );
        }
        if ( array_key_exists( 'attr', $item ) ) { 
            foreach ( $item[ 'attr' ] as $attr => $val ) {
                $elem->setAttribute( $attr, $val );
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
            'attr' => array( 'type' => 'html' ),
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
        if (empty($teaser_text))
        {
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
        foreach($bylines as $contributor) {
            $story[] = array(
                'tag' => 'byline',
                'children' => array(
                    array(
                        'tag' => 'name',
                        'text' => $contributor,
                    )
                )
            );
        }

        /*
        * Send to NPR One
        *
        * If the box is checked, the value here is '1'
        * @see nprstory_save_send_to_one
        */
        // $nprapi = get_post_meta( $post->ID, '_send_to_one', true ); // 0 or 1
        $nprapi = 0;
        if ( ! empty( $nprapi ) && ( '1' === $nprapi || 1 === $nprapi ) ) {
            $story[] = array(
                'tag' => 'parent',
                'attr' => array( 'id' => '319418027', 'type' => 'collection' ),
            );
        }

        /*
        * This story should be featured in NPR One
        *
        * @see nprstory_save_nprone_featured
        */
        // $nprapi = get_post_meta( $post->ID, '_nprone_featured', true ); // 0 or 1
        if ( ! empty( $nprapi ) && ( '1' === $nprapi || 1 === $nprapi ) ) {
            $story[] = array(
                'tag' => 'parent',
                'attr' => array( 'id' => '500549367', 'type' => 'collection' ),
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
        $pub_date = ee()->localize->format_date('D, d M Y H:i:s +0000', $entry->entry_date, false);
        $entry->{$this->get_field_name('pub_date')} = $pub_date;
        $story[] = array(
            'tag' => 'pubDate',
            'text' => $pub_date
        );

        $entry->{$this->get_field_name('story_date')} = $pub_date;
        $story[] = array(
            'tag' => 'storyDate',
            'text' => $pub_date
        );

        $edit_date = ee()->localize->format_date('D, d M Y H:i:s +0000', $entry->edit_date, false);
        $entry->{$this->get_field_name('last_modified_date')} = $edit_date;
        $story[] = array(
            'tag' => 'lastModifiedDate',
            'text' => $edit_date
        );

        $story[] = array(
            'tag' => 'partnerId',
            'text' => $this->get_partnerId(),
        );

        // NPR One audio run-by date
        $datetime = $this->nprstory_get_post_expiry_datetime( $entry ); // if expiry date is not set, returns publication date plus 7 days
        if ( $datetime instanceof \DateTime ) {
            $story[] = array(
                'tag' => 'audioRunByDate',
                'text' => date_format( $datetime, 'j M Y H:i:00 O' ) // 1 Oct 2017 01:00:00 -0400, 29 Feb 2020 23:59:00 -0500
            );

            $entry->{$this->get_field_name('audio_runby_date')} = $datetime->getTimestamp();
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
            'children' => $this->nprstory_nprml_split_paragraphs( $content ),
        );
       
        $perms_group = $this->get_npr_story_default_permission();
        if (!empty( $perms_group ) ) {
            $story[] = array(
                'tag' => 'permissions',
                'children' => array (
                    array( 
                        'tag' => 'permGroup',
                        'attr' => array( 'id' => $perms_group ),
                    )
                ),
            );
        }
        
        /*
        * Attach images to the post
        */
        $images = $this->get_media($entry, 'npr_images');
        $custom_media_agency = $this->get_media_agency($images);
        $custom_media_credit = $this->get_media_credit($images);
        $images = $this->convert_images($images, $custom_media_credit, $custom_media_agency);
        foreach ($images as $image)
        {
            $story[] = $image;
        }

        /*
        * Attach audio to the post
        */
        $audio = $this->get_media($entry, 'audio_files');
        $audio = $this->convert_audio($audio);
        foreach ($audio as $item)
        {
            $story[] = $item;
        }
        
        /*
        * The story has been assembled; now we shall return it
        */
        return $story;
    }
}