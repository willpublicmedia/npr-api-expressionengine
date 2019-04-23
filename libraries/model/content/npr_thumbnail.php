<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story thumbnail as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_story extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_thumbnails';

    protected static $_relationships = array(
        'Story' => array(
            'model' => 'Npr_story',
            'type' => 'BelongsTo'
        )
    );

    protected $id;

    protected $size;

    protected $link;

    protected $provider;

    protected $story;
}