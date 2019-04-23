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
use \IllinoisPublicMedia\NprStoryApi\Libraries\Model\Http\Api_response;

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

    private function set_response_code($simplexml) {
        if (!property_exists($simplexml, 'messages')) {
            return self::NPRAPI_STATUS_OK;
        }
        
        $messages = $simplexml->messages->message;
        $code = (int)$messages[0]->attributes()->id;
    }
}