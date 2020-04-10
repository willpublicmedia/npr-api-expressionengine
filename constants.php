<?php

namespace IllinoisPublicMedia\NprStoryApi;

if (!defined('BASEPATH')) {
    exit ('No direct script access allowed.');
}

class Constants
{
    const NAME = 'NPR Story API';

    const AUTHOR = 'Illinois Public Media';

    const AUTHOR_URL = 'https://will.illinois.edu';

    const DESCRIPTION = "An ExpressionEngine port of NPR's story API Wordpress module (https://github.com/npr/nprapi-wordpress).";

    const DOCS_URL = 'https://gitlab.engr.illinois.edu/willpublicmedia/npr_api_expressionengine';

    const NAMESPACE = 'IllinoisPublicMedia\NprStoryApi';

    const VERSION = '2.0.1';
}