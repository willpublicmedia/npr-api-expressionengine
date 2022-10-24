<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR HTML asset. Not defined in API output spec.
 */
class Npr_html_asset extends Model {
    protected static $_primary_key = 'ee_id';

    protected static $_table_name = 'npr_story_api_stories_html_assets';

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
     * The unique ID for the HTML asset.
     */
    protected $id;

    /**
     * HTML asset data.
     */
    protected $asset;

    protected $story_id;
}