<?php
    return array(
        'author'            => 'Illinois Public Media (WILL)',
        'author_url'        => 'https://gitlab.engr.illinois.edu/willpublicmedia/npr_api_expressionengine',
        'name'              => 'NPR Story API',
        'description'       => "An ExpressionEngine port of NPR's story API Wordpress module (https://github.com/npr/nprapi-wordpress).",
        'namespace'         => 'IllinoisPublicMedia\NprStoryApi',
        'version'           => '0.0.0',
        'settings_exist'    => TRUE,
        'models'            => array(
            'Npr_audio' => 'Libraries\Model\Content\Npr_audio',
            'Npr_audio_format' => 'Libraries\Model\Content\Npr_audio_format',
            'Npr_byline' => 'Libraries\Model\Content\Npr_byline',
            'Npr_html_asset' => 'Libraries\Model\Content\Npr_html_asset',
            'Npr_image' => 'Libraries\Model\Content\Npr_image',
            'Npr_image_crop' => 'Libraries\Model\Content\Npr_image_crop',
            'Npr_permalink' => 'Libraries\Model\Content\Npr_permalink',
            'Npr_organization' => 'Libraries\Model\Content\Npr_organization',
            'Npr_pull_correction' => 'Libraries\Model\Content\Npr_pull_correction',
            'Npr_pull_quote' => 'Libraries\Model\Content\Npr_pull_quote',
            'Npr_related_link' => 'Libraries\Model\Content\Npr_related_link',
            'Npr_story' => 'Libraries\Model\Content\Npr_story',
            'Npr_text_paragraph' => 'Libraries\Model\Content\Npr_text_paragraph',
            'Npr_thumbnail' => 'Libraries\Model\Content\Npr_thumbnail'
        )
    )
?>