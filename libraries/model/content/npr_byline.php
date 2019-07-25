<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Model\Content;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Object model for an NPR story byline.
 */
class Npr_byline extends Model {
    private $api_settings;

    public function __construct()
    {
        $this->api_settings = $this->load_settings();
        parent::__construct();
    }

    protected static $_primary_key = 'id';

    protected static $_table_name = 'npr_story_api_stories_bylines';

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
    protected $id;

    /**
     * The unique ID for the byline.
     */
    protected $byline_id;

    /**
     * Story author's name.
     */
    protected $name;

    /**
     * Author's unique ID.
     */
    protected $personId;

    protected $story_id;

    /**
     * Generate the api link for the author's work.
     */
    protected $_api_link;

    protected function get___api_link()
    {
        $api_url = $this->settings['pull_url'];
        $api_key = $this->settings['api_key'];

        return $api_url . "/query?id={$this->personId}&meta=inherit&apiKey={$api_key}";
    }

    private function load_settings()
    {
        $settings = ee()->db->select('*')
            ->from('npr_story_api_settings')
            ->get()
            ->result_array();

        if (isset($settings[0])) {
            $settings = $settings[0];
        }
        
        return $settings;
    }
}