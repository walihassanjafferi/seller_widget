<?php
/*
 * Plugin Name:       One-Time-Code Manager
 * Plugin URI:        https://sellerwidgets.com
 * Description:       Manages the distribution and display of single use codes, also known as one-rime-codes, from the Seller Widgets One-Time-Code Server.
 * Version:           1.0
 * Author:            Wali Hassan Jafferi
 * Author URI:        https://sellerwidgets.com
 * Text Domain:       single-post-meta-manager-locale
 * License:           Private
 * License URI:       https://sellerwidgets.com
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SW_OTCM_PATH', plugin_dir_path( __FILE__ ) );
define( 'SW_OTCM_DIR_PATH', plugin_dir_url( __FILE__ ) );

// include the main plugin class file
require_once( SW_OTCM_PATH . 'classes/class-swotcm-main.php' );

$swotcm = new Seller_Widgets_OTCM();
register_activation_hook( __FILE__, array( 'Seller_Widgets_OTCM', 'create_SWOTCM_db' ) );
