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
    public function query_by_url($url, $method) {
        $this->request->request_url = $url;

        $response = $this->connect_as_curl($url, $method);
        
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
     * 
     * @param string $method
     *   The HTTP request method (i.e., get, post, update, put, delete).
     */
    public function request($params = array(), $path = 'query', $base = self::NPRAPI_PULL_URL, $method = 'get') {
        if (!isset($params['apiKey']) || $params['apiKey'] === '') {
            throw new Configuration_exception('NPR API key not found. Configure key in NPR Story API module settings.');
        }

        $request_url = $this->build_request($params, $path, $base, $method);

        $response = $this->query_by_url($request_url, $method);
        $this->response = $response;
    }

    /**
     * Perform housekeeping related to story push.
     */
    public function process_push_response()
    {
        if ($this->response->code != 200)
        {
            throw new Npr_response_exception('Couldn\'t push story. Connection error: ' . $this->response->code);
        }

        if (!$this->response->body)
        {
            throw new Npr_response_exception('Error returned from NPR Story API with status code 200 OK but failed to retreive message body.');
        }
        
        // IPM's mock api response includes headers in body.
        // May not be needed in production.
        list($headers, $body) = explode("\r\n\r\n", $this->response->body, 2);
        $response_xml = simplexml_load_string($body);
        
        $npr_story_id = (string) $response_xml->list->story['id'];
        
        return $npr_story_id;
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
    }

    private function build_query_params($params) {
        $queries = array();
        foreach ( $this->request->params as $k => $v ) {
          $queries[] = "$k=$v";
          $this->request->param[$k] = $v;
        }

        return $queries;
    }

    private function build_request($params, $path, $base, $method) {
        // prevent null value from stomping default.
        $base = $base?: self::NPRAPI_PULL_URL;
        $this->request->params = $params;
        $this->request->path = $path;
        $this->request->base = $base;

        $request_url = $this->request->base . '/' . $this->request->path;
        
        $queries = $this->build_query_params($params);

        if ($method === 'get')
        {
            $request_url = $request_url . '?' . implode('&', $queries);
        } 
        elseif ($method === 'post')
        {
            $this->request->postfields = implode('&', $queries);
        }
        
        return $request_url;
    }

    private function connect_as_curl($url, $method) {
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($method === 'post')
        {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/xml',
                'Connection: Keep-Alive',
                'Accept: application/xml'
                ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request->postfields);
        }

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