<?php
/**
 * Plugin Name:       Goallord Addons
 * Plugin URI:        https://goallord.com/addons
 * Description:       Premium, editorial-grade Elementor widgets. First widget in the suite: Announcement & News.
 * Version:           1.0.0
 * Author:            Goallord
 * Author URI:        https://goallord.com
 * Text Domain:       goallord-addons
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Elementor tested up to: 3.23.0
 * Elementor Pro tested up to: 3.23.0
 *
 * @package Goallord\Addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GOALLORD_ADDONS_VERSION', '1.0.0' );
define( 'GOALLORD_ADDONS_FILE', __FILE__ );
define( 'GOALLORD_ADDONS_BASENAME', plugin_basename( __FILE__ ) );
define( 'GOALLORD_ADDONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GOALLORD_ADDONS_URL', plugin_dir_url( __FILE__ ) );
define( 'GOALLORD_ADDONS_ASSETS', GOALLORD_ADDONS_URL . 'assets/' );

require_once GOALLORD_ADDONS_PATH . 'includes/class-goallord-addons.php';

/**
 * Returns the singleton plugin instance.
 *
 * @return \Goallord\Addons\Plugin
 */
function goallord_addons() {
	return \Goallord\Addons\Plugin::instance();
}

goallord_addons();
