<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR organization as defined by https://www.npr.org/api/outputReference.php.
 */
class Npr_organization extends Model {
    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_organizations';

    protected static $_relationships = array(
        'Stories' => array(
            'model' => 'Npr_story',
            'type' => 'HasMany'
        )
    );

    /**
     * Database primary key.
     */
    protected $id;

    /**
     * The unique ID of the organization for the returned story.
     */
    protected $orgId;

    /**
     * The organization's abbreviation.
     */
    protected $orgAbbr;

    /**
     * The full name of the owner organization for the returned story.
     */
    protected $name;

    /**
     * Website associated with the owner organization for the returned story.
     */
    protected $website;

    /**
     * Indicates the type of website provided by the owner organization (eg. "Home Page").
     */
    protected $website_type;

    /**
     * NPR stories.
     */
    protected $stories;
}