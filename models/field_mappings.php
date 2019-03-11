<?php 

namespace IllinoisPublicMedia\NprStoryApi\Models;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

class Field_mappings extends Model {
    protected static $_primary_key = 'id';
    protected static $_table_name = 'npr_story_api_field_mappings';

    protected $id;
    protected $custom_settings;
    protected $media_agency_field;
    protected $media_credit_field;
    protected $story_title;
    protected $story_body;
    protected $story_byline;
}