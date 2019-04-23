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
use \IllinoisPublicMedia\NprStoryApi\Libraries\Exceptions\Npr_response_exception;

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

        $raw = $this->connect_as_curl($url);
        
        if ( $raw['body'] ) {
            $this->xml = $raw['body'];
        } else {
            $this->notice[] = __( 'No data available.' );
        }

        return $raw;
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

        $params['apiKey'] = '';
        $request_url = $this->build_request($params, $path, $base);

        $raw = $this->query_by_url($request_url);
        $this->response = $raw;
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

        $response_code = $this->parse_response_code($raw);

        if ($http_status != self::NPRAPI_STATUS_OK || $response_code != self::NPRAPI_STATUS_OK) {
            throw new Npr_response_exception("Unable to retrieve story info for {$url}.");
        }

        return $raw;
    }

    private function parse_response_code($xmlstring) {
        $xml = simplexml_load_string($xmlstring);
        if (!property_exists($xml, 'messages')) {
            return self::NPRAPI_STATUS_OK;
        }
        
        $messages = $xml->messages->message;
        $code = (int)$messages[0]->attributes()->id;
        
        return $code;
    }
}