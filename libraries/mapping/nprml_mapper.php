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

    private function get_option($option_name)
    {
        $option_value;
        switch ($option_name)
        {
            case 'dp_npr_push_use_custom_map':
                $option_value = FALSE;
                break;
            default:
                FALSE;
        }
        return $option_value;
    }

    private function get_permalink($entry)
    {
        return $entry->entry_id;
    }

    private function nprstory_post_to_nprml_story($entry, $values)
    {
        $story = array();
        $story[] = array(
            'tag' => 'link',
            'attr' => array( 'type' => 'html' ),
            'text' => $this->get_permalink($entry),
        );

        $use_custom = $this->get_option('dp_npr_push_use_custom_map');

        //get the list of metas available for this post
        $post_metas = get_post_custom_keys( $post->ID );

        $teaser_text = '';
        if ( ! empty( $post->post_excerpt ) ){
            $teaser_text = $post->post_excerpt;
        }

        /*
        * Custom content
        */
        $custom_content_meta = get_option( 'ds_npr_api_mapping_body' );
        if (
            $use_custom
            && ! empty( $custom_content_meta )
            && $custom_content_meta != '#NONE#'
            && in_array( $custom_content_meta, $post_metas )
        ){
            $content = get_post_meta( $post->ID, $custom_content_meta, true);
            $post_for_teaser = $post;
            $post_for_teaser->post_content = $content;
            if ( empty( $teaser_text ) ){
                $teaser_text = nprstory_nai_get_excerpt( $post_for_teaser );
            }
        } else {
            $content = $post->post_content;
            if ( empty( $teaser_text ) ) {
                $teaser_text = nprstory_nai_get_excerpt( $post );
            }
        }

        /*
        * Clean up the content by applying shortcodes and then stripping any remaining shortcodes.
        */
        // Let's see if there are any plugins that need to fix their shortcodes before we run do_shortcode
        if ( has_filter( 'npr_ds_shortcode_filter' ) ) {
            $content = apply_filters( 'npr_ds_shortcode_filter', $content );
        }

        // Let any plugin that has short codes try and replace those with HTML
        $content = do_shortcode( $content );

        //for any remaining short codes, nuke 'em
        $content = strip_shortcodes( $content );
        $content = apply_filters( 'the_content', $content );

        $story[] = array(
            'tag' => 'teaser',
            'text' => $teaser_text,
        );

        /*
        * Custom title
        */
        $custom_title_meta = get_option( 'ds_npr_api_mapping_title' );
        if (
            $use_custom
            && !empty( $custom_title_meta )
            && $custom_title_meta != '#NONE#'
            && in_array( $custom_content_meta, $post_metas )
        ){
            $custom_title = get_post_meta( $post->ID, $custom_title_meta, true );
            $story[] = array(
                'tag' => 'title',
                'text' => $custom_title,
            );
        } else {
            $story[] = array(
                'tag' => 'title',
                'text' => $post->post_title,
            );
        }

        /*
        * If there is a custom byline configured, send that.
        *
        * If the site is using the coauthurs plugin, and get_coauthors exists, send the display names
        * If no cool things are going on, just send the display name for the post_author field.
        */
        $byline = FALSE;
        $custom_byline_meta = get_option( 'ds_npr_api_mapping_byline' );
        // Custom field mapping byline
        if (
            $use_custom
            && ! empty( $custom_byline_meta )
            && $custom_byline_meta != '#NONE#'
            && in_array( $custom_content_meta, $post_metas )
        ) {
            $byline = TRUE;
            $story[] = array(
                'tag' => 'byline',
                'children' => array(
                    array(
                        'tag' => 'name',
                        'text' => get_post_meta( $post->ID, $custom_byline_meta, true ),
                    )
                ),
            );
        }
        // Co-Authors Plus support overrides the NPR custom byline
        if ( function_exists( 'get_coauthors' ) ) {
            $coauthors = get_coauthors( $post->ID );
            if ( ! empty( $coauthors ) ) {
                $byline = TRUE;
                foreach( $coauthors as $i=>$co ) {
                    $story[] = array(
                        'tag' => 'byline',
                        'children' => array(
                            array(
                                'tag' => 'name',
                                'text' => $co->display_name,
                            )
                        )
                    );
                }
            } else {
                nprstory_error_log( 'we do not have co authors' );
            }
        } else {
            nprstory_error_log('can not find get_coauthors');
        }
        if ( ! $byline ) {
            $story[] = array(
                'tag' => 'byline',
                'children' => array (
                    array(
                        'tag' => 'name',
                        'text' => get_the_author_meta( 'display_name', $post->post_author ),
                    )
                ),
            );
        }

        /*
        * Send to NPR One
        *
        * If the box is checked, the value here is '1'
        * @see nprstory_save_send_to_one
        */
        $nprapi = get_post_meta( $post->ID, '_send_to_one', true ); // 0 or 1
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
        $nprapi = get_post_meta( $post->ID, '_nprone_featured', true ); // 0 or 1
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
            'text' => mysql2date( 'D, d M Y H:i:s +0000', $post->post_date_gmt ),
        );
        $story[] = array(
            'tag' => 'pubDate',
            'text' => mysql2date( 'D, d M Y H:i:s +0000', $post->post_modified_gmt ),
        );
        $story[] = array(
            'tag' => 'lastModifiedDate',
            'text' => mysql2date( 'D, d M Y H:i:s +0000', $post->post_modified_gmt ), 
        );
        $story[] = array(
            'tag' => 'partnerId',
            'text' => $post->guid,
        );

        // NPR One audio run-by date
        $datetime = nprstory_get_post_expiry_datetime( $post ); // if expiry date is not set, returns publication date plus 7 days
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
            'children' => nprstory_nprml_split_paragraphs( $content ),
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