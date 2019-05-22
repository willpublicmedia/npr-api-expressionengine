<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_story extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories';

    protected static $_relationships = array(
        'Thumbnail' => array(
            'model' => 'Npr_thumbnail',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Toenail' => array(
            'model' => 'Npr_thumbnail',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Organization' => array(
            'model' => 'Npr_organization',
            'type' => 'BelongsTo',
            'from_key' => 'organization',
            'to_key' => 'id'
        ),
        'Audio' => array(
            'model' => 'Npr_audio',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Image' => array(
            'model' => 'Npr_image',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Link' => array(
            'model' => 'Npr_related_link',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'PullQuote' => array(
            'model' => 'Npr_pull_quote',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Text' => array(
            'model' => 'Npr_text_paragraph',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'TextWithHtml' => array(
            'model' => 'Npr_text_paragraph',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'ListText' => array(
            'model' => 'Npr_text_paragraph',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        ),
        'Correction' => array(
            'model' => 'Npr_pull_correction',
            'type' => 'HasMany',
            'from_key' => 'ee_id',
            'to_key' => 'story_id'
        )
    );

    /**
     * Database primary key.
     */
    protected $ee_id;

    /**
     * NPR Story ID.
     */
    protected $id;

    // protected $links -> collection of objects.

    /**
     * The title of the returned story. This is the main title or headline.
     */
    protected $title;

    /**
     * A short, sentence-like description of the returned story.
     */
    protected $subtitle;

    /**
     * An abbreviated title for the returned story, not to exceed 30 characters.
     */
    protected $shortTitle;

    /**
     * The main abstract for the returned story, describing what the story is about.
     */
    protected $teaser;

    /**
     * An abbreviated abstract for the returned story, describing what the story is about.
     */
    protected $miniTeaser;

    /**
     * The main association for the returned story, whether it is to a topic, series, column or some other list in the system.
     */
    protected $slug;

    /**
     * The image URL for a small representative image for the returned story. This image is designed to entice readers to view the story.
     * 
     * No parameters at this level. thumbnail does contain sub-elements called "medium" and "large".
     */
    protected $thumbnail;

    /**
     * Undocumented property.
     */
    protected $thumbnailRights;

    /**
     * The image URL for an even smaller representative image for the returned story. This image is designed to entice readers to view the story.
     * 
     * No parameters at this level. toenail does contain a sub-element called "medium".
     */
    protected $toenail;

    /**
     * The primary date/time associated with the publication of the returned story to NPR.org.
     */
    protected $storyDate;

    /**
     * The date/the returned time the story was initially published to NPR.org, or the last date a significant update was published to NPR.org.
     */
    protected $pubDate;

    /**
     * The date/time the returned story was last modified in any capacity to NPR.org.
     */
    protected $lastModifiedDate;

    /**
     * A comma-delimited list of key terms describing the returned story. This field is seldom used for NPR.org.
     */
    protected $keywords;

    /**
     * A comma-delimited list of key terms that are very closely tied to the returned story.
     */
    protected $priorityKeywords;

    /**
     * The owner organization of the returned story.
     */
    protected $organization;

    /**
     * All available audio associated with the returned story. This will include all formats to which NPR has the rights to distribute.
     */
    protected $audio;

    /**
     * All images associated with the returned story.
     */
    protected $image;

    /**
     * Links to related stories, both on NPR.org and elsewhere.
     */
    protected $link;

    /**
     * Quotes from the returned story that have been identified as particularly compelling by NPR editorial staff.
     */
    protected $pullQuote;

    /**
     * The full text of the returned story without any markup, broken out by paragraph.
     */
    protected $text;

    /**
     * The full text of the returned story, complete with markup, broken out by paragraph.
     */
    protected $textWithHtml;

    /**
     * A supplemental text field used for a variety of reasons. 
     * listText could be a ordered or unordered list of interesting points, a timeline, 
     * an additional highlighted paragraph related to the story, or possibly even an extension 
     * of the text (and textWithHtml) fields.
     */
    protected $listText;

    /**
     * Information about corrections to the story. Not present if there are no corrections.
     */
    protected $correction;
}