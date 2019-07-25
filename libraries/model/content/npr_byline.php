<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for a story byline.
 */
class Npr_byline extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_bylines';

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
    protected $id;

    /**
     * The unique ID for the byline
     */
    protected $byline_id;

    /**
     * Story author's name.
     */
    protected $name;

    protected $story_id;
}