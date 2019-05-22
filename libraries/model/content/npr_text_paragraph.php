<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story pull quote as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_text_paragraph extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_text_paragraphs';

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
     * The num parameter indicates the order of the paragraphs relative to each other under their corresponding text element.
     */
    protected $num;

    /**
     * The text elements are broken up at the paragraph level.
     */
    protected $text;

    /**
     * The tag parameter is used only by listText.
     * 
     * Tag defines the intention of the editor in grouping the related items of the listText. Possible values include "p".
     * 
     */
    protected $tag;
    
    protected $story_id;
}