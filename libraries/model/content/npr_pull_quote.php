<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story pull quote as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_pull_quote extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_pull_quotes';

    protected static $_relationships = array(
        'Story' => array(
            'model' => 'Npr_story',
            'type' => 'BelongsTo'
        )
    );

    /**
     * Database primary key.
     */
    protected $id;

    /**
     * The person or people responsible for the quote.
     */
    protected $person;

    /**
     * The date of the quote. This can be anything from a specific moment in time to a year.
     */
    protected $date;

    protected $story;
}