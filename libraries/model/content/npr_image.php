<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story image file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_image extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_images';

    protected static $_relationships = array(
        'Story' => array(
            'model' => 'Npr_story',
            'type' => 'BelongsTo'
        )
    );

    /**
     * Database primary key.
     */
    protected $ee_id;

    /**
     * The unique ID for the audio asset.
     */
    protected $id;

    /**
     * TBD
     */
    protected $type;

    /**
     * The width of the image in pixels.
     */
    protected $width;

    /**
     * The source URL for the image.
     */
    protected $src;

    /**
     * Indicates if the image has a border in the asset itself.
     * Note: NPR output reference says 'addBorder'; API output uses 'hasBorder'.
     */
    protected $hasBorder;

    /**
     * The caption for the image, describing the contents of the image and/or the image's relationship to the returned story.
     */
    protected $caption;

    /**
     * The URL to which the image should link. Should be link->url.
     */
    protected $link;

    /**
     * The actual producer of the image, to whom the image will get credited.
     */
    protected $producer;

    /**
     * The owner or provider of the image, which may be independent from the image producer.
     */
    protected $provider;

    /**
     * The URL of the provider. This is used for attribution purposes and must convey with the image.
     */
    protected $providerUrl;

    /**
     * The copyright information (year) for the image.
     */
    protected $copyright;
}