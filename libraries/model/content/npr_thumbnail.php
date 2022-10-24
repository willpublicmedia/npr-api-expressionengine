<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story thumbnail as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_thumbnail extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_thumbnails';

    protected static $_relationships = array(
        'Story' => array(
            'model' => 'Npr_story',
            'type' => 'BelongsTo',
            'from_key' => 'story_id',
            'to_key' => 'ee_id'
        )
    );

    protected $id;

    protected $size;

    protected $link;

    protected $provider;

    /**
     * Undocumented story property.
     */
    protected $rights;

    protected $story_id;
}