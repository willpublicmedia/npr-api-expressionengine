<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story audio file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_audio extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_audio';

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
    protected $ee_id;

    /**
     * The unique ID for the audio asset.
     */
    protected $id;

    /**
     * Defines whether or not the audio asset is the primary audio for the story.
     */
    protected $primary;

    /**
     * The duration of the audio asset. All formats for the audio will have the same duration.
     */
    protected $duration;

    /**
     * The description of the audio asset. A short, sentence-like description of the audio.
     */
    protected $description;

    /**
     * Audio format. Options: mp3, rm (real media), wm (windows media).
     */
    protected $format;

    /**
     * Defines the delivery method for the MP3 files.
     */
    protected $rights;

    /**
     * The URL for the audio assets.
     */
    protected $url;

    protected $story_id;
}