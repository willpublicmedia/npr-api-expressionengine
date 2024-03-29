<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story image file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_image extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_images';

    protected static $_typed_columns = array(
        'copyright' => 'int',
        'height' => 'int',
        'width' => 'int'
    );

    protected static $_relationships = array(
        'Story' => array(
            'type' => 'BelongsTo',
            'model' => 'Npr_story',
            'from_key' => 'story_id',
            'to_key' => 'ee_id'
        ),
        'Crop' => array(
            'type' => 'HasMany',
            'model' => 'Npr_image_crop',
            'from_key' => 'ee_id',
            'to_key' => 'image_id'
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
     * Image title.
     */
    protected $title;

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

    /**
     * Undocumented property. URL for image enlargement.
     */
    protected $enlargement;

    /**
     * Undocumented property. Caption for image enlargement.
     */
    protected $enlargementCaption;

    protected $story_id;
}