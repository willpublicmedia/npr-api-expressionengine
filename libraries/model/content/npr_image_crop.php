<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story image file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_image_crop extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_image_crops';

    protected static $_relationships = array(
        'Image' => array(
            'type' => 'BelongsTo',
            'model' => 'Npr_image',
            'from_key' => 'image_id',
            'to_key' => 'ee_id'
        )
    );

    /**
     * Database primary key.
     */
    protected $id;

    protected $image_id;

    /**
     * Crop type.
     */
    protected $type;

    /**
     * Image source URL.
     */
    protected $src;

    /**
     * Image height.
     */
    protected $height;

    /**
     * Image width.
     */
    protected $width;

    /**
     * Optional boolean flag indicating primary crop.
     */
    protected $primary;
}