<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Npr_story_api_mcp
{
    private $pull_server = '';

    private $push_server = '';
    
    public function pull_server($server) {
        if ($this->validate_server) {
            $this->pull_server = $server;
        }
    }
    
    public function push_server($server)
    {
        if ($this->validate_server) {
            $this->push_server = $server;
        }
    }

    private function validate_server($server) {
        return filter_var($server, FILTER_VALIDATE_URL);
    }
}