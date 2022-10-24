<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story audio file as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_audio extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_audio';

    protected static $_typed_columns = array(
        'permissions' => 'json'
    );

    protected static $_relationships = array(
        'Story' => array(
            'type' => 'BelongsTo',
            'model' => 'Npr_story',
            'from_key' => 'story_id',
            'to_key' => 'ee_id'
        ),
        'Format' => array(
            'type' => 'HasMany',
            'model' => 'Npr_audio_format',
            'from_key' => 'ee_id',
            'to_key' => 'audio_id'
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
    protected $type;

    /**
     * The duration of the audio asset. All formats for the audio will have the same duration.
     */
    protected $duration;

    /**
     * The description of the audio asset. A short, sentence-like description of the audio.
     */
    protected $description;

    /**
     * Defines the delivery method for the MP3 files.
     */
    protected $rights;

    protected $story_id;

    /**
     * Undocumented field. Serialized permissions elements.
     */
    protected $permissions;

    
    /**
     * Undocumented field. Audio title.
     */
    protected $title;

    /**
     * Undocumented field. Playable region.
     */
    protected $region;

    /**
     * Undocumented field. Audio rights holder.
     */
    protected $rightsholder;
}