<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Configuration\Fields;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

class Story_content_definitions
{
    public static $fields = array(
        'audio_files' => array(
            'field_name' => 'audio_files',
            'field_label' => 'Audio Files',
            'field_instructions' => 'All available audio associated with the returned story. This will include all formats to which NPR has the rights to distribute.',
            'field_type' => 'file_grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '',
                'allow_reorder' => 'y',
                'file_grid' => array(
                    'cols' => array(
                        'new_0' => array(
                            'col_type' => "file",
                            'col_label' => "File",
                            'col_name' => "file",
                            'col_instructions' => "",
                            'col_required' => "n",
                            'col_search' => "n",
                            'col_width' => "",
                            'col_settings' => array(
                                'field_content_type' => "all",
                                'allowed_directories' => "all",
                                'show_existing' => "y",
                                'num_existing' => "50"
                            )
                        ),
                        'new_1' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Type',
                            'col_name' => 'audio_type',
                            'col_instructions' => 'The type of audio file.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_2' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Duration',
                            'col_name' => 'audio_duration',
                            'col_instructions' => 'The duration of the audio asset. All formats for the audio will have the same duration.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_3' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Description',
                            'col_name' => 'audio_description',
                            'col_instructions' => 'A short, sentence-like description of the audio.',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_4' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Format',
                            'col_name' => 'audio_format',
                            'col_instructions' => 'The audio format.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_5' => array(
                            'col_type' => 'url',
                            'col_label' => 'Audio URL',
                            'col_name' => 'audio_url',
                            'col_instructions' => 'The URL for the audio asset.',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'url_scheme_placeholder' => 'https://'
                            )
                        ),
                        'new_6' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Rights',
                            'col_name' => 'audio_rights',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_7' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Permissions',
                            'col_name' => 'audio_permissions',
                            'col_instructions' => 'A comma-separated list of allowed uses.',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )                            
                        ),
                        'new_8' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Title',
                            'col_name' => 'audio_title',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_9' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Region',
                            'col_name' => 'audio_region',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_10' => array(
                            'col_type' => 'text',
                            'col_label' => 'Audio Rightsholder',
                            'col_name' => 'audio_rightsholder',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            ) 
                        )
                    )
                )
            )
        ),
        'audio_runby_date' => array(
            'field_name' => 'audio_runby_date',
            'field_label' => 'Audio Run-By Date',
            'field_instructions' => 'Date by which audio should be aired.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'byline' => array(
            'field_name' => 'byline',
            'field_label' => 'Byline',
            'field_instructions' => 'A comma-separated list of story contributors.',
            'field_type' => 'text',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'corrections' => array(
            'field_name' => 'corrections',
            'field_label' => 'Corrections',
            'field_instructions' => 'Information about corrections to the story. Not present if there are no corrections.',
            'field_type' => 'grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '',
                'allow_reorder' => 'y',
                'grid' => array(
                    'cols' => array(
                        'new_0' => array(
                            'col_type' => 'text',
                            'col_label' => 'Correction Text',
                            'col_name' => 'correction_text',
                            'col_instructions' => 'The full text of the correction.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '1024',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_1' => array(
                            'col_type' => 'date',
                            'col_label' => 'Correction Date',
                            'col_name' => 'correction_date',
                            'col_instructions' => 'The date of the latest update to the correction.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'localize' => 'n'
                            )
                        )
                    )
                )
            )
        ),
        'npr_images' => array(
            'field_name' => 'npr_images',
            'field_label' => 'Images',
            'field_instructions' => 'All images associated with the story.',
            'field_type' => 'file_grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '',
                'allow_reorder' => 'y',
                'file_grid' => array(
                    'cols' => array(
                        'new_0' => array(
                            'col_type' => "file",
                            'col_label' => "File",
                            'col_name' => "file",
                            'col_instructions' => "",
                            'col_required' => "n",
                            'col_search' => "n",
                            'col_width' => "",
                            'col_settings' => array(
                                'field_content_type' => "all",
                                'allowed_directories' => "all",
                                'show_existing' => "y",
                                'num_existing' => "50"
                            )
                        ),
                        'new_1' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Type',
                            'col_name' => 'crop_type',
                            'col_instructions' => 'The general crop size (e.g., small).',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_2' => array(
                            'col_type' => 'url',
                            'col_label' => 'Crop Source',
                            'col_name' => 'crop_src',
                            'col_instructions' => 'The URL for the image asset.',
                            'col_required' => 'y',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'url_scheme_placeholder' => 'https://'
                            )
                        ),
                        'new_3' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Height',
                            'col_name' => 'crop_height',
                            'col_instructions' => 'The height of the image in pixels.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'integer'
                            )
                        ),
                        'new_4' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Width',
                            'col_name' => 'crop_width',
                            'col_instructions' => 'The width of the image in pixels.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'integer'
                            )
                        ),
                        'new_5' => array(
                            'col_type' => 'toggle',
                            'col_label' => 'Primary',
                            'col_name' => 'crop_primary',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_default_value' => 0
                            )
                        ),
                        'new_6' => array(
                            'col_type' => 'toggle',
                            'col_label' => 'Has Border',
                            'col_name' => 'crop_has_border',
                            'col_instructions' => 'Indicates if the image has a border in the asset itself.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_default_value' => 0
                            )
                        ),
                        'new_7' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Title',
                            'col_name' => 'crop_title',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_8' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Caption',
                            'col_name' => 'crop_caption',
                            'col_instructions' => "The caption for the image, describing the contents of the image and/or the image's relationship to the returned story.",
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_9' => array(
                            'col_type' => 'text',
                            'col_label' => 'Crop Producer',
                            'col_name' => 'crop_producer',
                            'col_instructions' => 'The actual producer of the image, to whom the image will get credited.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_10' => array(
                            'col_type' => 'text',
                            'col_label' => 'Provider',
                            'col_name' => 'crop_provider',
                            'col_instructions' => 'The owner or provider of the image, which may be independent from the image producer.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_11' => array(
                            'col_type' => 'url',
                            'col_label' => 'Provider URL',
                            'col_name' => 'crop_provider_url',
                            'col_instructions' => 'The URL of the provider. This is used for attribution purposes and must be conveyed with the image.',
                            'col_required' => 'n',
                            'col_search' => 'y',
                            'col_width' => '',
                            'col_settings' => array(
                                'url_scheme_placeholder' => 'https://'
                            )
                        ),
                        'new_12' => array(
                            'col_type' => 'text',
                            'col_label' => 'Copyright',
                            'col_name' => 'crop_copyright',
                            'col_instructions' => 'The copyright information (year) for the image. 	',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'integer'
                            )
                        ),
                    // 
                    // 'link' => $model->link
                    // 'enlargement' => $model->enlargement,
                    // 'enlargementCaption' => $model->enlargementCaption
                    )
                )
            )
        ),
        'keywords' => array(
            'field_name' => 'keywords',
            'field_label' => 'Keywords',
            'field_instructions' => 'A list of key terms describing the returned story. This field is seldom used for NPR.org.',
            // this field type may be specific to IPM.
            'field_type' => 'tagger',
            'field_pre_populate' => 'n',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'last_modified_date' => array(
            'field_name' => 'last_modified_date',
            'field_label' => 'Last Modified Date',
            'field_instructions' => 'Date that a pulled story was last modified.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'mini_teaser' => array(
            'field_name' => 'mini_teaser',
            'field_label' => 'Mini Teaser',
            'field_instructions' => 'An abbreviated abstract for the returned story, describing what the story is about.',
            'field_type' => 'text',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'organization' => array(
            'field_name' => 'organization',
            'field_label' => 'Organization',
            'field_instructions' => 'The owner organization of the story.',
            'field_type' => 'grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '1',
                'allow_reorder' => 'y',
                'grid' => array(
                    'cols' => array(
                        'new_0' => array(
                            'col_type' => 'text',
                            'col_label' => 'Org ID',
                            'col_name' => 'org_id',
                            'col_instructions' => 'The organization\'s unique ID number.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'integer'
                            )
                        ),
                        'new_1' => array(
                            'col_type' => 'text',
                            'col_label' => 'Org Abbreviation',
                            'col_name' => 'org_abbr',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_2' => array(
                            'col_type' => 'text',
                            'col_label' => 'Org Name',
                            'col_name' => 'org_name',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_3' => array(
                            'col_type' => 'url',
                            'col_label' => 'Org Website',
                            'col_name' => 'org_website',
                            'col_instructions' => '',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array()
                        )
                    )
                )
            )
        ),
        'permalinks' => array(
            'field_name' => 'permalinks',
            'field_label' => 'Permalinks',
            'field_instructions' => "Permanent link to story on the story's original website",
            'field_type' => 'text',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'priority_keywords' => array(
            'field_name' => 'priority_keywords',
            'field_label' => 'Priority Keywords',
            'field_instructions' => 'A comma-delimited list of key terms that are very closely tied to the returned story.',
            'field_type' => 'text',
            'field_pre_populate' => 'n',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'pullquotes' => array(
            'field_name' => 'pullquotes',
            'field_label' => 'Pullquotes',
            'field_instructions' => 'Quotes from the story that have been identified as particularly compelling by NPR editorial staff.',
            'field_type' => 'grid',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'grid_min_rows' => 0,
                'grid_max_rows' => '',
                'allow_reorder' => 'y',
                'grid' => array(
                    'cols' => array(
                        'new_0' => array(
                            'col_type' => 'text',
                            'col_label' => 'Person',
                            'col_name' => 'quote_person',
                            'col_instructions' => ' 	The person or people responsible for the quote.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_1' => array(
                            'col_type' => 'text',
                            'col_label' => 'Date',
                            'col_name' => 'quote_date',
                            'col_instructions' => 'The date of the quote. This can be anything from a specific moment in time to a year.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                        'new_2' => array(
                            'col_type' => 'text',
                            'col_label' => 'Text',
                            'col_name' => 'quote_text',
                            'col_instructions' => 'The pullquote text.',
                            'col_required' => 'n',
                            'col_search' => 'n',
                            'col_width' => '',
                            'col_settings' => array(
                                'field_maxl' => '',
                                'field_fmt' => 'none',
                                'field_text_direction' => 'ltr',
                                'field_content_type' => 'all'
                            )
                        ),
                    )
                )
            )
        ),
        'short_title' => array(
            'field_name' => 'short_title',
            'field_label' => 'Short Title',
            'field_instructions' => 'An abbreviated title, not to exceed 30 characters.',
            'field_type' => 'text',
            'field_maxl' => '30',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'pub_date' => array(
            'field_name' => 'pub_date',
            'field_label' => 'Publication Date',
            'field_instructions' => 'Date that a pulled story was originally published.',
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'slug' => array(
            'field_name' => 'slug',
            'field_label' => 'Slug',
            'field_instructions' => "The main association for story, whether it is to a topic, series, column or some other list in the system.",
            'field_type' => 'text',
            'field_maxl' => '128',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'subtitle' => array(
            'field_name' => 'subtitle',
            'field_label' => 'Subtitle',
            'field_instructions' => 'A short, sentence-like description of the story.',
            'field_type' => 'text',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'story_date' => array(
            'field_name' => 'story_date',
            'field_label' => 'Story Date',
            'field_instructions' => "The primary date/time associated with the story's publication to NPR.org.",
            'field_type' => 'date',
            'field_list_items' => '',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        ),
        'teaser' => array(
            'field_name' => 'teaser',
            'field_label' => 'Teaser',
            'field_instructions' => 'The main abstract for the returned story, describing what the story is about.',
            'field_type' => 'textarea',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1,
            'field_settings' => array(
                'field_fmt' => 'none',
                'field_show_fmt' => 'n'
            )
        ),
        'text' => array(
            'field_name' => 'text',
            'field_label' => 'Text',
            'field_instructions' => 'The full text of the story, complete with markup.',
            'field_type' => 'rte',
            'field_maxl' => '',
            'field_list_items' => '',
            'field_pre_populate' => 'n',
            'field_pre_field_id' => 0,
            'field_pre_channel_id' => 0,
            'field_order' => 1
        )
    );
}