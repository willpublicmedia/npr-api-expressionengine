<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for a story byline.
 */
class Npr_byline extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_bylines';

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
     * The unique ID for the byline
     */
    protected $byline_id;

    /**
     * Story author's name.
     */
    protected $name;
    
    /**
     * Author's ID.
     */
    protected $personId;    

    protected $story_id;

    /**
     * Generate the api link for the author's work.
     */
    protected $_api_link;

    protected function get___api_link()
    {
        // $api_url . '/query?id=' . $this->personId . '&meta=inherit&apiKey=' . $apiKey
        return "https://api.npr.org/query?id={$this->personId}&meta=inherit";
    }
}