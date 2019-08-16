<?php
    require_once (__DIR__ . '/constants.php');
    use IllinoisPublicMedia\NprStoryApi\Constants;

    return array(
        'author'            => Constants::AUTHOR,
        'author_url'        => Constants::AUTHOR_URL,
        'name'              => Constants::NAME,
        'description'       => Constants::DESCRIPTION,
        'namespace'         => Constants::NAMESPACE,
        'version'           => Constants::VERSION,
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
        ),
        'models.dependencies' => array(
            'Npr_story' => array(
                'ee:ChannelEntry'
            )
        )
    )
?>