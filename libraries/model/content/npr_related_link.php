<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story related link as defined by https://www.npr.org/api/outputReference.php.
 */
// Newsroom doesn't pull related link.
// Rewrite model--has many weak permalinks.
class Npr_related_link extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_related_links';

    protected static $_relationships = array(
        'Story' => array(
            'model' => 'Npr_story',
            'type' => 'BelongsTo',
            'from_key' => 'story_id',
            'to_key' => 'ee_id'
        )
    );

    /**
     * Database primary key.
     */
    protected $ee_id;

    /**
     * The unique ID for the link.
     * 
     * Only returned if link type is internal.
     */
    protected $id;

    /**
     * Indicates the type of link. Current possible types are "internal" and "external".
     */
    protected $type;

    /**
     * Link text for the related link.
     */
    protected $caption;

    protected $story_id;
}