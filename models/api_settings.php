<?php 

namespace IllinoisPublicMedia\NprStoryApi\Models;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

class Api_settings extends Model {
    protected static $_primary_key = 'id';
    protected static $_table_name = 'npr_story_api_settings';

    protected $id;
    protected $api_key;
    protected $npr_permissions;
    protected $npr_pull_post_type;
    protected $npr_push_post_type;
    protected $org_id;
    protected $pull_url;
    protected $push_url;
}