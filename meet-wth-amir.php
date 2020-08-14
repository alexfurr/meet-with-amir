<?php
/*
Plugin Name: Meet with Amir
Description: Simple calendar booking system for meeting with Amir
Version: 0.1
Author: Alex Furr
*/

// Global defines
define( 'MWA_PLUGIN_URL', plugins_url('meet-with-amir' , dirname( __FILE__ )) );
define( 'MWA_PLUGIN_PATH', plugin_dir_path(__FILE__) );

include_once( MWA_PLUGIN_PATH . '/functions.php');
include_once( MWA_PLUGIN_PATH . '/classes/class-draw.php');
include_once( MWA_PLUGIN_PATH . '/classes/class-utils.php');
include_once( MWA_PLUGIN_PATH . '/classes/class-db.php');
include_once( MWA_PLUGIN_PATH . '/classes/class-actions.php');
include_once( MWA_PLUGIN_PATH . '/classes/class-queries.php');



?>
