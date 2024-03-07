<?php
/**
 * @package Furllery
 * @version 1.0.0
 */
/*
Plugin Name: Furllery
Plugin URI: https://darkfox.pl
Description: A WordPress gallery plugin with a bit of fur on it!
Author: Reyn with 🧡 and 🍄
Version: 1.5.3
Author URI: https://darkfox.pl
*/

ob_start();

global $furllery_errors;
$furllery_errors = [];

// Ensure some of WP Core is loaded.
if ( ! class_exists( 'Link_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Define plugin's constants.
define( 'DF__FURLLERY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DF__FURLLERY_LIB_DIR', plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR );
define( 'DF__FURLLERY_VIEW_DIR', plugin_dir_path( __FILE__ ) . 'view' . DIRECTORY_SEPARATOR );
define( 'DF__FURLLERY_ASSETS_DIR', plugin_dir_path( __FILE__ ) . 'assets' . DIRECTORY_SEPARATOR );

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
