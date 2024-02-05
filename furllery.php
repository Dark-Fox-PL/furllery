<?php
/**
 * @package Furllery
 * @version 1.0.0
 */
/*
Plugin Name: Furllery
Plugin URI: https://darkfox.pl
Description: A WordPress gallery plugin with a bit of fur on it!
Author: Dark Fox
Version: 1.0.0
Author URI: https://darkfox.pl
*/

define( 'DF__FURLLERY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DF__FURLLERY_LIB_DIR',  plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR );
define( 'DF__FURLLERY_VIEW_DIR',  plugin_dir_path( __FILE__ ) . 'view' . DIRECTORY_SEPARATOR );

// Load dependencies.
require_once( DF__FURLLERY_LIB_DIR . 'setup.php' );
require_once( DF__FURLLERY_LIB_DIR . 'db.php' );
require_once( DF__FURLLERY_LIB_DIR . 'admin.php' );

//Pre init hooks.
register_activation_hook( __FILE__, 'df_furllery_do_activate' );

// Load main class.
require_once( DF__FURLLERY_LIB_DIR . 'main.php' );

// Run plugin.
new Furllery;
