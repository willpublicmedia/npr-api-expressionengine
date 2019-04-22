<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Publishing;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

require_once(__DIR__ . '/../../vendor/autoload.php');
use \NPRAPI;

class Npr_api_expressionengine extends NPRAPI {
    public function request($params = array(), $path = 'query', $base = self::NPRAPI_PULL_URL) {
        $this->assign_request_params($params, $path, $base);
        
        $queries = array();
        foreach ( $this->request->params as $k => $v ) {
          $queries[] = "$k=$v";
        }

        $request_url = $this->request->base . '/' . $this->request->path . '?' . implode('&', $queries);
        $this->request->request_url = $request_url;

        print_r($request_url);
        // $this->query_by_url($request_url);
    }

    private function assign_request_params($params, $path, $base) {
        // prevent null value from stomping default.
        $base = $base?: self::NPRAPI_PULL_URL;
        $this->request->params = $params;
        $this->request->path = $path;
        $this->request->base = $base;
    }
}