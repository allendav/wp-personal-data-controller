<?php
/*
Plugin Name: WP Personal Data Controller
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( plugin_basename( 'classes/class-wppdc.php' ) );
require_once( plugin_basename( 'classes/class-wppdc-admin.php' ) );

// Exporters
require_once( plugin_basename( 'classes/class-wppdc-wordpress-core.php' ) );
