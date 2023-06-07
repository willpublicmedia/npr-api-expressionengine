<?php
/**
 * @file
 *
 * Defines a class for NPRML creation/transmission and retrieval/parsing
 * Unlike NPRAPI class, Npr_api_expressionengine is ExpressionEngine-specific
 */
namespace IllinoisPublicMedia\NprStoryApi\Libraries\Publishing;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../exceptions/configuration_exception.php';
require_once __DIR__ . '/../exceptions/npr_response_exception.php';
require_once __DIR__ . '/../dto/http/api_response.php';
require_once __DIR__ . '/../mapping/model_story_mapper.php';
use ExpressionEngine\Service\Model\Model;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Dto\Http\Api_response;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Exceptions\Configuration_exception;
use \IllinoisPublicMedia\NprStoryApi\Libraries\Mapping\Model_story_mapper;
use \NPRAPI;

class Npr_api_expressionengine extends NPRAPI
{
    public $notice = [];

    public $request;

    public $response;

    public $stories = [];

    public $xml;

    /**
     *
     * Query a single url.  If there is not an API Key in the query string, append one, but otherwise just do a straight query
     *
     * @param string $url -- the full url to query.
     */
    public function query_by_url($url, $method)
    {
        $this->request->request_url = $url;

        $response = $this->connect_as_curl($url, $method);
        if (isset($response->messages)) {
            return;
        }

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
    public function request($params = array(), $path = 'query', $base = self::NPRAPI_PULL_URL, $method = 'get')
    {
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
        if ($this->response->code != 200) {
            ee('CP/Alert')->makeInline('story-push-response-error')
                ->asAttention()
                ->withTitle('NPR Stories')
                ->addToBody("Received response code " . $this->response->code . " while pushing story to NPR.")
                ->defer();
        }

        if (!$this->response->body) {
            ee('CP/Alert')->makeInline('story-push-response-error')
                ->asAttention()
                ->withTitle('NPR Stories')
                ->addToBody("Error returned from NPR Story API with status code 200 OK but failed to retrieve message body.")
                ->defer();
        }

        try {
            // IPM's mock api response includes headers in body.
            // May not be needed in production.
            $body = $this->response->body;
            $response_xml = simplexml_load_string($body);
            $header_count = 0;
            while ($response_xml === false && $header_count < 3) {
                list($headers, $body) = explode("\r\n\r\n", $this->response->body, 2);
                $this->response->body = $body;
                $response_xml = simplexml_load_string($body);
                $header_count++;
            }
        } catch (\Throwable $th) {
            ee('CP/Alert')->makeInline('story-push-response-error')
                ->asAttention()
                ->withTitle('NPR Stories')
                ->addToBody("Unable to process story api response. " . $th->getMessage())
                ->defer();

            return '';
        }

        if (property_exists($response_xml, 'message')) {
            $code = (int) $response_xml->message->attributes()->id;
            $message = (string) $response_xml->message->text;
            ee('CP/Alert')->makeInline('entries-form')
                ->asAttention()
                ->withTitle("NPR API response code $code")
                ->addToBody($message)
                ->defer();
        }

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
    public function save_clean_response($story)
    {
        $model = $this->map_to_model($story);
        return $model;
    }

    /**
     *
     * Because expression engine doesn't support remote posts, we needed to write a curl version to send delete
     * requests to the NPR API
     *
     * @param  $api_id The NPR Story ID.
     */
    public function send_delete($api_id)
    {
        $settings = ee()->db
            ->limit(1)
            ->get('npr_story_api_settings')
            ->row();

        $params = array(
            'orgId' => $settings->org_id,
            'apiKey' => $settings->api_key,
            'id' => $api_id,
        );

        $path = '/story';
        $base = $settings->push_url;
        $method = 'delete';

        $request_url = $this->build_request($params, $path, $base, $method);
        $response = $this->connect_as_curl($request_url, $method);
        if (!$response || property_exists($response, 'messages')) {
            return;
        }

        $this->remove_push_registry($api_id);
    }

    /**
     *
     * This function will go through the list of stories in the object and check to see if there are updates
     * available from the NPR API if the pubDate on the API is after the pubDate originally stored locally.
     *
     * @param bool $publish
     * @return int|null $post_id or null
     */
    public function update_posts_from_stories($publish = true, $qnum = false)
    {
        throw new \Exception('not implemented');
    }

    private function build_query_params($params)
    {
        $queries = array();
        foreach ($params as $k => $v) {
            $queries[] = "$k=$v";
            $param[$k] = $v;
        }

        return $queries;
    }

    private function build_request($params, $path, $base, $method)
    {
        // prevent null value from stomping default.
        $base = $base ?: self::NPRAPI_PULL_URL;
        $this->request->params = $params;
        $this->request->path = $path;
        $this->request->base = $base;

        $request_url = $this->request->base . '/' . $this->request->path;

        if ($method === 'post') {
            // $this->request->postfields = implode('&', $queries);
            $this->request->postfields = $params['body'];
            unset($params['body']);
        }

        $queries = $this->build_query_params($params);
        $request_url = $request_url . '?' . implode('&', $queries);

        return $request_url;
    }

    private function connect_as_curl($url, $method)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($method === 'post') {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml;charset=UTF-8',
                'Connection: Keep-Alive',
                'Vary: Accept-Encoding',
            ));
            $field_count = count($this->request->params);
            curl_setopt($ch, CURLOPT_POST, $field_count);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request->postfields);
        }

        if ($method === 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $raw = curl_exec($ch);

        //Did an error occur? If so, dump it out.
        if (curl_errno($ch)) {
            $msg = curl_error($ch);

            ee('CP/Alert')->makeInline('entries-form')
                ->asIssue()
                ->withTitle("Unable to connect to NPR Story API")
                ->addToBody($msg)
                ->defer();
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // parser expects an object, not xml string.
        $response = curl_errno($ch) ? $this->create_error_response(curl_error($ch), $url) : $this->convert_response($raw, $url);

        curl_close($ch);

        if ($http_status != self::NPRAPI_STATUS_OK || $response->code != self::NPRAPI_STATUS_OK) {
            $code = property_exists($response, 'code') ? $response->code : $http_status;
            $message = property_exists($response, 'messages') ? $response->messages[0]['message'] : "Error updating $url";

            ee('CP/Alert')->makeInline('entries-form')
                ->asIssue()
                ->withTitle("NPR API response error: $code")
                ->addToBody($message)
                ->defer();
        }

        return $response;
    }

    private function convert_response($xmlstring, $url)
    {
        $response = new Api_response($xmlstring);
        $response->url = $url;

        $xml = simplexml_load_string($xmlstring);

        $data = $this->set_response_code($xml);
        $response->code = $data['code'];

        if (array_key_exists('messages', $data)) {
            $response->messages = $data['messages'];
        }

        return $response;
    }

    private function create_error_response($message, $url)
    {
        $response = new Api_response('');
        $response->url = $url;
        $response->code = 503;
        $response->messages = [$message];

        return $response;
    }

    private function map_to_model($parsed_story): Model
    {
        $mapper = new Model_story_mapper();
        $model = $mapper->map_parsed_story($parsed_story);

        return $model;
    }

    private function remove_push_registry($npr_story_id)
    {
        ee()->db->delete(
            'npr_story_api_pushed_stories',
            array('npr_story_id' => $npr_story_id)
        );
    }

    private function set_response_code($simplexml)
    {
        if (!$simplexml) {
            return array(
                'code' => '503',
                'messages' => array(
                    array(
                        'message' => 'Unable to process XML response.',
                        'level' => '1',
                    ),
                ),
            );
        }

        if (!property_exists($simplexml, 'message')) {
            return array('code' => self::NPRAPI_STATUS_OK);
        }

        $data = array(
            'code' => (int) $simplexml->message->attributes()->id,
            'messages' => array(
                array(
                    'message' => (string) $simplexml->message->text,
                    'level' => (string) $simplexml->message->attributes()->level,
                ),
            ),
        );

        return $data;
    }
}
