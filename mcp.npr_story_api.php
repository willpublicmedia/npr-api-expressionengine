<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Npr_story_api_mcp
{
    private $pull_server = '';

    private $push_server = '';
    
    public function pull_server($server) {
        $this->pull_server = $server;
    }
    
    public function push_server($server)
    {
        $this->push_server = $server;
    }
}