<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    $plugin_info = array(
        'pi_name' => 'NPR Story Api',
        'pi_version' => '0.0.0',
        'pi_author' => 'Illinois Public Media',
        'pi_author_url' => 'https://gitlab.engr.illinois.edu/willpublicmedia/npr_api_expressionengine',
        'pi_description' => "An ExpressionEngine port of NPR's story API Wordpress module (https://github.com/npr/nprapi-wordpress).",
        'pi_usage' => Npr_story_api::usage()
    );

    class Npr_story_api {
        public $return_data;

        public function __construct() {
            $this->return_data = 'Hello NPR.';
        }

        public static function usage() {
            ob_start();  ?>
See https://github.com/npr/nprapi-wordpress.
<?php

            $buffer = ob_get_contents();
            ob_end_clean();
    
            return $buffer;        
        }

    }
?>