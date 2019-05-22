<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story pull quote as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_pull_correction extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_corrections';

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
     * Any title given to the correction. Often empty.
     */
    protected $correctionTitle;

    /**
     * The full text of the correction.
     */
    protected $correctionText;

    /**
     * The date of the latest update to the correction.
     */
    protected $correctionDate;

    protected $story_id;
}