<?php
/**
 * @file
 *
 * Defines a class for NPRML creation/transmission and retrieval/parsing
 * Unlike NPRAPI class, Npr_api_expressionengine is ExpressionEngine-specific
 */
namespace IllinoisPublicMedia\NprStoryApi\Libraries\Publishing;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../exceptions/configuration_exception.php');
require_once(__DIR__ . '/../exceptions/npr_response_exception.php');
require_once(__DIR__ . '/../dto/http/api_response.php');
require_once(__DIR__ . '/../mapping/model_story_mapper.php');
use \NPRAPI;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Exceptions\Configuration_exception;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Exceptions\Npr_response_exception;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Dto\Http\Api_response;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Model_story_mapper;
use EllisLab\ExpressionEngine\Service\Model\Model;

class Npr_api_expressionengine extends NPRAPI {
    /**
     * 
     * Query a single url.  If there is not an API Key in the query string, append one, but otherwise just do a straight query
     * 
     * @param string $url -- the full url to query.
     */
    public function query_by_url($url) {
        //check to see if the API key is included, if not, add the one from the options
        if ( ! stristr( $url, 'apiKey=' ) ) {
            throw new Configuration_exception('NPR API key not found. Configure key in NPR Story API module settings.');
        }

        $this->request->request_url = $url;

        $response = $this->connect_as_curl($url);
        
        if ($response->body) {
            $this->xml = $response->body;
        } else {
            $this->notice[] = 'No data available.';
        }

        return $response;
    }

    /**
     * Makes HTTP request to NPR API.
     *
     * @param array $params
     *   Key/value pairs to be sent (within the request's query string).
     *
     *
     * @param string $path
     *   The path part of the request URL (i.e., https://example.com/PATH).
     *
     * @param string $base
     *   The base URL of the request (i.e., HTTP://EXAMPLE.COM/path) with no trailing slash.
     */
    public function request($params = array(), $path = 'query', $base = self::NPRAPI_PULL_URL) {
        if (!isset($params['apiKey']) || $params['apiKey'] === '') {
            throw new Configuration_exception('NPR API key not found. Configure key in NPR Story API module settings.');
        }

        $request_url = $this->build_request($params, $path, $base);

        $response = $this->query_by_url($request_url);
        $this->response = $response;
    }

    /**
     * Save story as received from NPR Story API.
     *
     * @param  mixed $story
     *
     * @return void
     */
    public function save_clean_response($story) {
        $model = $this->map_to_model($story);
        return $model;
    }

    /**
     *
     * This function will go through the list of stories in the object and check to see if there are updates
     * available from the NPR API if the pubDate on the API is after the pubDate originally stored locally.
     *
     * @param bool $publish
     * @return int|null $post_id or null
     */
    public function update_posts_from_stories( $publish = TRUE, $qnum = false ) {
        throw new Exception('not implemented');
        // $pull_post_type = get_option( 'ds_npr_pull_post_type' );
        // if ( empty( $pull_post_type ) ) {
        // $pull_post_type = 'post';
        // }

        // $post_id = null;

        // if ( ! empty( $this->stories ) ) {
        // $single_story = TRUE;
        // if ( sizeof( $this->stories ) > 1) {
        // $single_story = FALSE;
        // }
        // foreach ( $this->stories as $story ) {
        // $exists = new WP_Query(
        // array( 'meta_key' => NPR_STORY_ID_META_KEY,
        // 'meta_value' => $story->id,
        // 'post_type' => $pull_post_type,
        // 'post_status' => 'any'
        // )
        // );

        // //set the mod_date and pub_date to now so that for a new story we will fail the test below and do the update
        // $post_mod_date = strtotime(date('Y-m-d H:i:s'));
        // $post_pub_date = $post_mod_date;

        // if ( $exists->found_posts ) {
        // $existing = $exists->post;
        // $post_id = $existing->ID;
        // $existing_status = $exists->posts[0]->post_status;
        // $post_mod_date_meta = get_post_meta( $existing->ID, NPR_LAST_MODIFIED_DATE_KEY );
        // if ( ! empty( $post_mod_date_meta[0] ) ) {
        // $post_mod_date = strtotime( $post_mod_date_meta[0] );
        // }
        // $post_pub_date_meta = get_post_meta( $existing->ID, NPR_PUB_DATE_META_KEY );
        // if ( ! empty( $post_pub_date_meta[0] ) ) {
        // $post_pub_date = strtotime($post_pub_date_meta[0]);
        // }
        // } else {
        // $existing = $existing_status = null;
        // }

        // //add the transcript
        // $story->body .= $this->get_transcript_body($story);

        // $story_date = new DateTime($story->storyDate->value);
        // $post_date = $story_date->format('Y-m-d H:i:s');

        // //set the story as draft, so we don't try ingesting it
        // $args = array(
        // 'post_title'   => $story->title,
        // 'post_excerpt' => $story->teaser,
        // 'post_content' => $story->body,
        // 'post_status'  => 'draft',
        // 'post_type'    => $pull_post_type,
        // 'post_date'    => $post_date,
        // );
        // if( false !== $qnum ) {
        // $args['tags_input'] = get_option('ds_npr_query_tags_'.$qnum);
        // }
        // //check the last modified date and pub date (sometimes the API just updates the pub date), if the story hasn't changed, just go on
        // if ( $post_mod_date != strtotime( $story->lastModifiedDate->value ) || $post_pub_date !=  strtotime( $story->pubDate->value ) ) {

        // $by_line = '';
        // $byline_link = '';
        // $multi_by_line = '';
        // //continue to save single byline into npr_byline as is, but also set multi to false
        // if ( isset( $story->byline->name->value ) ) { //fails if there are multiple bylines or no bylines
        // $by_line = $story->byline->name->value;
        // $multi_by_line = 0; //only single author, set multi to false
        // if ( ! empty( $story->byline->link ) ) {
        // $links = $story->byline->link;
        // if ( is_string( $links ) ) {
        // $byline_link = $links;
        // } else if ( is_array( $links ) ) {
        // foreach ( $links as $link ) {
        // if ( empty( $link->type ) ) {
        // continue;
        // }
        // if ( 'html' === $link->type ) {
        // $byline_link = $link->value;
        // }
        // }
        // } else if ( $links instanceof NPRMLElement && ! empty( $links->value ) ) {
        // $byline_link = $links->value;
        // }
        // }
        // }

        // //construct delimited string if there are multiple bylines
        // if ( ! empty( $story->byline ) ) {
        // $i = 0;
        // foreach ( (array) $story->byline as $single ) {
        // if ( ! empty( $single->name->value ) ) {
        // if ( $i == 0 ) {
        // $multi_by_line .= $single->name->value; //builds multi byline string without delimiter on first pass
        // } else {
        // $multi_by_line .= '|' . $single->name->value; //builds multi byline string with delimiter
        // }
        // $by_line = $single->name->value; //overwrites so as to save just the last byline for previous single byline themes
        // }
        // if ( ! empty( $single->link ) ) {
        // foreach( (array) $single->link as $link ) {
        // if ( empty( $link->type ) ) {
        // continue;
        // }
        // if ( 'html' === $link->type ) {
        // $byline_link = $link->value; //overwrites so as to save just the last byline link for previous single byline themes
        // $multi_by_line .= '~' . $link->value; //builds multi byline string links
        // }
        // }
        // }
        // $i++; 
        // }
        // }
        // //set the meta RETRIEVED so when we publish the post, we dont' try ingesting it
        // $metas = array(
        // NPR_STORY_ID_META_KEY        => $story->id,
        // NPR_API_LINK_META_KEY        => $story->link['api']->value,
        // NPR_HTML_LINK_META_KEY       => $story->link['html']->value,
        // //NPR_SHORT_LINK_META_KEY    => $story->link['short']->value,
        // NPR_STORY_CONTENT_META_KEY   => $story->body,
        // NPR_BYLINE_META_KEY          => $by_line,
        // NPR_BYLINE_LINK_META_KEY     => $byline_link,
        // NPR_MULTI_BYLINE_META_KEY    => $multi_by_line,
        // NPR_RETRIEVED_STORY_META_KEY => 1,
        // NPR_PUB_DATE_META_KEY        => $story->pubDate->value,
        // NPR_STORY_DATE_MEATA_KEY     => $story->storyDate->value,
        // NPR_LAST_MODIFIED_DATE_KEY   => $story->lastModifiedDate->value,
        // );
        // //get audio
        // if ( isset($story->audio) ) {
        // $mp3_array = array();
        // $m3u_array = array();
        // foreach ( (array) $story->audio as $n => $audio ) {
        // if ( ! empty( $audio->format->mp3['mp3']) && $audio->permissions->download->allow == 'true' ) {
        // if ($audio->format->mp3['mp3']->type == 'mp3' ) {
        // $mp3_array[] = $audio->format->mp3['mp3']->value;	
        // }
        // if ($audio->format->mp3['m3u']->type == 'm3u' ) {
        // $m3u_array[] = $audio->format->mp3['m3u']->value;
        // }
        // }
        // }
        // $metas[NPR_AUDIO_META_KEY] = implode( ',', $mp3_array );
        // $metas[NPR_AUDIO_M3U_META_KEY] = implode( ',', $m3u_array );
        // }
        // if ( $existing ) {
        // $created = false;
        // $args[ 'ID' ] = $existing->ID;
        // } else {
        // $created = true;
        // }

        // /**
        //  * Filters the $args passed to wp_insert_post()
        //  *
        //  * Allow a site to modify the $args passed to wp_insert_post() prior to post being inserted.
        //  *
        //  * @since 1.7
        //  *
        //  * @param array $args Parameters passed to wp_insert_post()
        //  * @param int $post_id Post ID or NULL if no post ID.
        //  * @param NPRMLEntity $story Story object created during import
        //  * @param bool $created true if not pre-existing, false otherwise
        //  */
        // $args = apply_filters( 'npr_pre_insert_post', $args, $post_id, $story, $created );

        // $post_id = wp_insert_post( $args );

        // //now that we have an id, we can add images
        // //this is the way WP seems to do it, but we couldn't call media_sideload_image or media_ because that returned only the URL
        // //for the attachment, and we want to be able to set the primary image, so we had to use this method to get the attachment ID.
        // if ( ! empty( $story->image ) && is_array( $story->image ) && count( $story->image ) ) {

        // //are there any images saved for this post, probably on update, but no sense looking of the post didn't already exist
        // if ( $existing ) {
        // $image_args = array(
        // 'order'=> 'ASC',
        // 'post_mime_type' => 'image',
        // 'post_parent' => $post_id,
        // 'post_status' => null,
        // 'post_type' => 'attachment',
        // 'post_date'	=> $post_date,
        // );
        // $attached_images = get_children( $image_args );
        // }	
        // foreach ( (array) $story->image as $image ) {
        // $image_url = '';
        // //check the <enlargement> and then the crops, in this order "enlargement", "standard"  if they don't exist, just get the image->src
        // if ( ! empty( $image->enlargement ) ) {
        // $image_url = $image->enlargement->src;
        // } else {
        // if ( ! empty( $image->crop ) && is_array( $image->crop ) ) {
        // foreach ( $image->crop as $crop ) {
        // if ( empty( $crop->type ) ) {
        // continue;
        // }
        // if ( 'enlargement' === $crop->type ) {
        // $image_url = $crop->src;
        // }
        // }
        // if ( empty( $image_url ) ) {
        // foreach ( $image->crop as $crop ) {
        // if ( empty( $crop->type ) ) {
        // continue;
        // }
        // if ( 'standard' === $crop->type ) {
        // $image_url = $crop->src;
        // }
        // }
        // }
        // }
        // }

        // if ( empty( $image_url ) && ! empty( $image->src ) ) {
        // $image_url = $image->src;
        // }
        // nprstory_error_log( 'Got image from: ' . $image_url );
        // // Download file to temp location
        // $tmp = download_url( $image_url );

        // // Set variables for storage
        // // fix file filename for query strings
        // preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $image_url, $matches );
        // $file_array['name'] = basename( $matches[0] );
        // $file_array['tmp_name'] = $tmp;

        // $file_OK = TRUE;
        // // If error storing temporarily, unlink
        // if ( is_wp_error( $tmp ) ) {
        // @unlink($file_array['tmp_name']);
        // $file_array['tmp_name'] = '';
        // $file_OK = FALSE;
        // }

        // // do the validation and storage stuff
        // require_once( ABSPATH . 'wp-admin/includes/image.php'); // needed for wp_read_image_metadata used by media_handle_sideload during cron
        // $id = media_handle_sideload( $file_array, $post_id, $image->title->value );
        // // If error storing permanently, unlink
        // if ( is_wp_error($id) ) {
        // @unlink( $file_array['tmp_name'] );
        // $file_OK = FALSE;
        // } else {
        // $image_post = get_post( $id );
        // if ( ! empty( $attached_images ) ) {
        // foreach( $attached_images as $att_image ) {
        // //see if the filename is very similar
        // $att_guid = explode( '.', $att_image->guid );
        // //so if the already attached image name is part of the name of the file
        // //coming in, ignore the new/temp file, it's probably the same
        // if ( strstr ( $image_post->guid, $att_guid[0] ) ) {
        // @unlink( $file_array['tmp_name'] );
        // wp_delete_attachment( $id );
        // $file_OK = FALSE;
        // }
        // }
        // }
        // }

        // //set the primary image
        // if ( $image->type == 'primary' && $file_OK ) {
        // add_post_meta( $post_id, '_thumbnail_id', $id, true );
        // //get any image meta data and attatch it to the image post
        // $image_metas = array(
        // NPR_IMAGE_CREDIT_META_KEY =>$image->producer->value,
        // NPR_IMAGE_AGENCY_META_KEY =>$image->provider->value,
        // NPR_IMAGE_CAPTION_META_KEY =>$image->caption->value,
        // );
        // foreach ( $image_metas as $k => $v ) {
        // update_post_meta( $post_id, $k, $v );
        // }
        // }
        // }
        // }

        // /**
        //  * Filters the post meta before series of update_post_meta() calls
        //  *
        //  * Allow a site to modify the post meta values prior to
        //  * passing each element via update_post_meta().
        //  *
        //  * @since 1.7
        //  *
        //  * @param array $metas Array of key/value pairs to be updated
        //  * @param int $post_id Post ID or NULL if no post ID.
        //  * @param NPRMLEntity $story Story object created during import
        //  * @param bool $created true if not pre-existing, false otherwise
        //  */
        // $metas = apply_filters( 'npr_pre_update_post_metas', $metas, $post_id, $story, $created );

        // foreach ( $metas as $k => $v ) {
        // update_post_meta( $post_id, $k, $v );
        // }

        // $args = array(
        // 'post_title'   => $story->title,
        // 'post_content' => $story->body,
        // 'post_excerpt' => $story->teaser,
        // 'post_type'    => $pull_post_type,
        // 'ID'   => $post_id,
        // 'post_date'	=> $post_date,
        // );

        // //set author
        // if ( ! empty( $by_line ) ) {
        // $userQuery = new WP_User_Query( array(
        // 'search' => trim( $by_line ),
        // 'search_columns' => array(
        // 'nickname'
        // )
        // )
        // );

        // $user_results = $userQuery->get_results();
        // if ( count( $user_results ) == 1 && isset( $user_results[0]->data->ID) ) {
        // $args['post_author'] = $user_results[0]->data->ID;
        // }
        // }

        // //now set the status
        // if ( ! $existing ) {
        // if ( $publish ) {
        // $args['post_status'] = 'publish';
        // } else {
        // $args['post_status'] = 'draft';
        // }
        // } else {
        // //if the post existed, save its status
        // $args['post_status'] = $existing_status;
        // }

        // /**
        //  * Filters the $args passed to wp_insert_post() used to update
        //  *
        //  * Allow a site to modify the $args passed to wp_insert_post() prior to post being updated.
        //  *
        //  * @since 1.7
        //  *
        //  * @param array $args Parameters passed to wp_insert_post()
        //  * @param int $post_id Post ID or NULL if no post ID.
        //  * @param NPRMLEntity $story Story object created during import
        //  */
        // $args = apply_filters( 'npr_pre_update_post', $args, $post_id, $story );

        // $post_id = wp_insert_post( $args );
        // }

        // //set categories for story
        // $category_ids = array();
        // if ( isset( $story->parent ) ) {
        // if ( is_array( $story->parent ) ) {
        // foreach ( $story->parent as $parent ) {
        // if ( isset( $parent->type ) && 'category' === $parent->type ) {

        // /**
        //  * Filters term name prior to lookup of terms
        //  *
        //  * Allow a site to modify the terms looked-up before adding them to list of categories.
        //  *
        //  * @since 1.7
        //  *
        //  * @param string $term_name Name of term
        //  * @param int $post_id Post ID or NULL if no post ID.
        //  * @param NPRMLEntity $story Story object created during import
        //  */
        // $term_name   = apply_filters( 'npr_resolve_category_term', $parent->title->value, $post_id, $story );
        // $category_id = get_cat_ID( $term_name );

        // if ( ! empty( $category_id ) ) {
        // $category_ids[] = $category_id;
        // }
        // }
        // }
        // } elseif ( isset( $story->parent->type ) && $story->parent->type === 'category') {
        // /*
        // * Filters term name prior to lookup of terms
        // *
        // * Allow a site to modify the terms looked-up before adding them to list of categories.
        // *
        // * @since 1.7
        // *
        // * @param string $term_name Name of term
        // * @param int $post_id Post ID or NULL if no post ID.
        // * @param NPRMLEntity $story Story object created during import
        // */
        // $term_name   = apply_filters('npr_resolve_category_term', $story->parent->title->value, $post_id, $story );
        // $category_id = get_cat_ID( $term_name );
        // if ( ! empty( $category_id) ) {
        // $category_ids[] = $category_id;
        // }
        // }

        // }

        // /*
        // * Filters category_ids prior to setting assigning to the post.
        // *
        // * Allow a site to modify category IDs before assigning to the post.
        // *
        // * @since 1.7
        // *
        // * @param int[] $category_ids Array of Category IDs to assign to post identified by $post_id
        // * @param int $post_id Post ID or NULL if no post ID.
        // * @param NPRMLEntity $story Story object created during import
        // */
        // $category_ids = apply_filters( 'npr_pre_set_post_categories', $category_ids, $post_id, $story );
        // if ( 0 < count( $category_ids ) && is_integer( $post_id ) ) {
        // wp_set_post_categories( $post_id, $category_ids );
        // }


        // }
        // if ( $single_story ) {
        // return isset( $post_id ) ? $post_id : 0;
        // }
        // }
        // return null;
    }

    private function build_query_params($params) {
        $queries = array();
        foreach ( $this->request->params as $k => $v ) {
          $queries[] = "$k=$v";
          $this->request->param[$k] = $v;
        }

        return $queries;
    }

    private function build_request($params, $path, $base) {
        // prevent null value from stomping default.
        $base = $base?: self::NPRAPI_PULL_URL;
        $this->request->params = $params;
        $this->request->path = $path;
        $this->request->base = $base;

        $queries = $this->build_query_params($params);
        
        $request_url = $this->request->base . '/' . $this->request->path . '?' . implode('&', $queries);

        return $request_url;
    }

    private function connect_as_curl($url) {
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     

        $raw = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // parser expects an object, not xml string.
        $response = $this->convert_response($raw, $url);

        if ($http_status != self::NPRAPI_STATUS_OK || $response->code != self::NPRAPI_STATUS_OK) {
            throw new Npr_response_exception("Unable to retrieve story info for {$response->url}.");
        }

        return $response;
    }

    private function convert_response($xmlstring, $url) {
        $response = new Api_response($xmlstring);
        $response->url = $url;

        $xml = simplexml_load_string($xmlstring);

        $response->code = $this->set_response_code($xml);     

        return $response;
    }

    private function map_to_model($parsed_story): Model {
        $mapper = new Model_story_mapper();
        $model = $mapper->map_parsed_story($parsed_story);

        return $model;
    }

    private function set_response_code($simplexml) {
        if (!property_exists($simplexml, 'messages')) {
            return self::NPRAPI_STATUS_OK;
        }

        $messages = $simplexml->messages->message;
        $code = (int)$messages[0]->attributes()->id;
    }
}