<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story audio file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_audio_format extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_audio_formats';

    protected static $_relationships = array(
        'Audio' => array(
            'type' => 'BelongsTo',
            'model' => 'Npr_audio',
            'from_key' => 'ee_id',
            'to_key' => 'id'
        )
    );

    /**
     * Database primary key.
     */
    protected $id;

    /**
     * Defines whether or not the audio asset is the primary audio for the story.
     */
    protected $type;

    /**
     * Audio format. Options: mp3, rm (real media), wm (windows media).
     */
    protected $format;

    /**
     * The URL for the audio assets.
     */
    protected $url;
}