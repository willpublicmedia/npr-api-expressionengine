<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR organization as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_permalink extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_permalinks';

    protected static $_relationships = array(
        'Story' => array(
            'type' => 'BelongsTo',
            'model' => 'Npr_story',
            'from_key' => 'story_id',
            'to_key' => 'ee_id'
        )
    );

    /**
     * Database primary key.
     */
    protected $id;
    
    /**
     * The actual URL for the related link.
     */
    protected $link;

    /**
     * Determines the nature of the link. Current type values are "html", which points to NPR.org, and "api", which points to this API.
     */
    protected $type;

    protected $story_id;
}