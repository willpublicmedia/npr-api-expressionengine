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
use \NPRAPI;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Exceptions\Configuration_exception;

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

        //fill out the $this->request->param array so we can know what params were sent
        // $parsed_url = parse_url($url);
        // if ( ! empty( $parsed_url['query'] ) ) {
        //     $params = explode( '&', $parsed_url['query'] );
        //     if ( ! empty( $params ) ){
        //         foreach ( $params as $p ){
        //             $attrs = explode( '=', $p );
        //             $this->request->param[$attrs[0]] = $attrs[1];
        //         }
        //     }
        // }
        // $response = wp_remote_get( $url );
        // if ( !is_wp_error( $response ) ) {
        //     $this->response = $response;
        //     if ( $response['response']['code'] == self::NPRAPI_STATUS_OK ) {
        //         if ( $response['body'] ) {
        //             $this->xml = $response['body'];
        //         } else {
        //             $this->notice[] = __( 'No data available.' );
        //         }
        //     } else {
        //         nprstory_show_message( 'An error occurred pulling your story from the NPR API.  The API responded with message =' . $response['response']['message'], TRUE );
        //     }
        // } else {
        //     $error_text = '';
        //     if ( ! empty( $response->errors['http_request_failed'][0] ) ) {
        //         $error_text = '<br> HTTP Error response =  '. $response->errors['http_request_failed'][0];
        //     }
        //     nprstory_show_message( 'Error pulling story for url='.$url . $error_text, TRUE );
        //     nprstory_error_log( 'Error retrieving story for url='.$url ); 
        // }
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
        $this->assign_request_params($params, $path, $base);

        $queries = array();
        foreach ( $this->request->params as $k => $v ) {
          $queries[] = "$k=$v";
        }

        $request_url = $this->request->base . '/' . $this->request->path . '?' . implode('&', $queries);
        $this->request->request_url = $request_url;

        $this->query_by_url($request_url);
    }

    private function assign_request_params($params, $path, $base) {
        // prevent null value from stomping default.
        $base = $base?: self::NPRAPI_PULL_URL;
        $this->request->params = $params;
        $this->request->path = $path;
        $this->request->base = $base;
    }
}