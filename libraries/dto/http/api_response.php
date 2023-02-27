<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Dto\Http;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Api_response {
    public $body;

    public $code;

    public $messages;

    public $raw;
    
    public $url;

    public function __construct($xmlstring) {
        $this->raw = $xmlstring;
        $this->body = $xmlstring;
    }
}