<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Mapping;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../vendor/autoload.php');
use \NPRMLElement;

class Nprml_mapper
{
    public function map($entry, $values)
    {
        $npr_story = $this->nprstory_post_to_nprml_story($entry, $values);
        $doc = array();
        $doc[] = array(
            'tag' => 'list',
            'children' => array( array( 'tag' => 'story', 'children' => $npr_story ), ),
        );
        $nprml = nprstory_nprml_array_to_xml( 'nprml', array( 'version' => '0.93' ), $doc );
        return $nprml;
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
        $id = ee()->db->get('npr_story_api_settings')
            ->limit(1)
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
        
        $teaser_text = '';
        if ( ! empty( $entry->{$teaser_field} ) ){
            $teaser_text = $entry->{$teaser_field};
        }

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
    function nprstory_get_post_expiry_datetime( $post )
    {
        // TODO: Not implemented
        $expiration_field = $this->get_field_name('audio_expiration_date');
        $expiration = $post->{$expiration_field};
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
    
    private function nprstory_post_to_nprml_story($entry, $values)
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
        $story[] = array(
            'tag' => 'storyDate',
            'text' => $this->get_date('D, d M Y H:i:s +0000', 'entry_date', FALSE, $entry)
        );
        $story[] = array(
            'tag' => 'pubDate',
            'text' => $this->get_date('D, d M Y H:i:s +0000', 'entry_date', FALSE, $entry),
        );
        $story[] = array(
            'tag' => 'lastModifiedDate',
            'text' => $this->get_date('D, d M Y H:i:s +0000', 'entry_date', FALSE, $entry) 
        );

        $story[] = array(
            'tag' => 'partnerId',
            'text' => $this->get_partnerId(),
        );

        // NPR One audio run-by date
        $datetime = $this->nprstory_get_post_expiry_datetime( $post ); // if expiry date is not set, returns publication date plus 7 days
        if ( $datetime instanceof DateTime ) {
            $story[] = array(
                'tag' => 'audioRunByDate',
                'text' => date_format( $datetime, 'j M Y H:i:00 O' ) // 1 Oct 2017 01:00:00 -0400, 29 Feb 2020 23:59:00 -0500
            );
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

        $perms_group = get_option( 'ds_npr_story_default_permission' );
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

        $custom_media_credit = get_option( 'ds_npr_api_mapping_media_credit' );
        $custom_media_agency = get_option( 'ds_npr_api_mapping_media_agency' );

        /* remove this for now until we decide if we're going to actually do this...km
        $dist_media_option = get_option('ds_npr_api_mapping_distribute_media');
        $dist_media_polarity = get_option('ds_npr_api_mapping_distribute_media_polarity');
        */

        /*
        * Attach images to the post
        */
        $args = array(
            'order'=> 'DESC',
            'post_mime_type' => 'image',
            'post_parent' => $post->ID,
            'post_status' => null,
            'post_type' => 'attachment'
        );

        $images = get_children( $args );
        $primary_image = get_post_thumbnail_id( $post->ID );

        foreach ( $images as $image ) {
            $custom_credit = '';
            $custom_agency = '';
            $image_metas = get_post_custom_keys( $image->ID );
            if ( $use_custom && !empty( $custom_media_credit ) && $custom_media_credit != '#NONE#' && in_array( $custom_media_credit,$image_metas ) ) {
                $custom_credit = get_post_meta( $image->ID, $custom_media_credit, true );
            }
            if ( $use_custom && ! empty( $custom_media_agency ) && $custom_media_agency != '#NONE#' && in_array( $custom_media_agency,$image_metas ) ) {
                $custom_agency = get_post_meta( $image->ID, $custom_media_agency, true);
            }

            if ( $use_custom && !empty( $dist_media_option ) && $dist_media_option != '#NONE#' && in_array( $dist_media_option,$image_metas ) ) {
                $dist_media = get_post_meta( $image->ID, $dist_media_option, true );
            }

            // If the image field for distribute is set and polarity then send it.
            // All kinds of other math when polarity is negative or the field isn't set.
            $image_type = 'standard';
            if ( $image->ID == $primary_image ) {
                $image_type = 'primary';
            }

            // Is the image in the content?  If so, tell the API with a flag that CorePublisher knows.
            // WordPress may add something like "-150X150" to the end of the filename, before the extension.
            // Isn't that nice? Let's remove that.
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
            $story[] = array(
                'tag' => 'image',
                'attr' => array( 'src' => $image->guid . $in_body, 'type' => $image_type ),
                'children' => array(
                    array(
                        'tag' => 'title',
                        'text' => $image->post_title,
                    ),
                    array(
                        'tag' => 'caption',
                        'text' => $image->post_excerpt,
                    ),
                    array(
                        'tag' => 'producer',
                        'text' => $custom_credit
                    ),
                    array(
                        'tag' => 'provider',
                        'text' => $custom_agency
                    )
                ),
            );
        }

        /*
        * Attach audio to the post
        *
        * Should be able to do the same as image for audio, with post_mime_type = 'audio' or something.
        */
        $args = array(
            'order'=> 'DESC',
            'post_mime_type' => 'audio',
            'post_parent' => $post->ID,
            'post_status' => null,
            'post_type' => 'attachment'
        );
        $audios = get_children( $args );

        foreach ( $audios as $audio ) {
            $audio_meta = wp_get_attachment_metadata( $audio->ID );
            $caption = $audio->post_excerpt;
            // If we don't have excerpt filled in, try content
            if ( empty( $caption ) ) {
                $caption = $audio->post_content;
            }

            $story[] = array(
                'tag' => 'audio',
                'children' => array(
                    array(
                        'tag' => 'format',
                        'children' => array (
                            array(
                                'tag' => 'mp3',
                                'text' => $audio->guid,
                            )
                        ),
                    ),
                    array(
                        'tag' => 'description',
                        'text' => $caption,
                    ),
                    array(
                        'tag' => 'duration',
                        'text' => $audio_meta['length'],
                    ),
                ),
            );
        }

        /*
        * Support for Powerpress enclosures
        *
        * This logic is specifically driven by enclosure metadata items that are
        * created by the PowerPress podcasting plug-in. It will likely have to be
        * re-worked if we need to accomodate other plug-ins that use enclosures.
        */
        if ( $enclosures = get_metadata( 'post', $post->ID, 'enclosure' ) ) {
            foreach( $enclosures as $enclosure ) {
                $pieces = explode( "\n", $enclosure );
                if ( !empty( $pieces[3] ) ) {
                    $metadata = unserialize( $pieces[3] );
                    $duration = ( ! empty($metadata['duration'] ) ) ? nprstory_convert_duration_to_seconds( $metadata['duration'] ) : NULL;
                }
                $story[] = array(
                    'tag' => 'audio',
                    'children' => array(
                        array(
                            'tag' => 'duration',
                            'text' => ( !empty($duration) ) ? $duration : 0,
                        ),
                        array(
                            'tag' => 'format',
                            'children' => array(
                                array(
                                'tag' => 'mp3',
                                'text' => $pieces[0],
                                ),
                            ),
                        ),
                    ),
                );
            }
        }

        /*
        * The story has been assembled; now we shall return it
        */
        return $story;
    }
}