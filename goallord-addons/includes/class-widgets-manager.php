<?php
/**
 * Handles widget registration for the Goallord Addons suite.
 *
 * Add new widgets to $widgets_map below. Each entry maps a class short-name
 * (inside the Goallord\Addons\Widgets namespace) to a file path inside /widgets.
 *
 * @package Goallord\Addons
 */

namespace Goallord\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widgets_Manager {

	/**
	 * Map of widget short-class-name => relative widget file.
	 *
	 * @var array<string,string>
	 */
	private $widgets_map = [
		'Announcement_News' => 'widgets/class-announcement-news.php',
		'Daily_Schedule'    => 'widgets/class-daily-schedule.php',
		'Advanced_Hero'     => 'widgets/class-advanced-hero.php',
	];

	public function __construct() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function register_widgets( $widgets_manager ) {
		foreach ( $this->widgets_map as $class => $file ) {
			$path = GOALLORD_ADDONS_PATH . $file;
			if ( ! file_exists( $path ) ) {
				continue;
			}
			require_once $path;
			$fqcn = '\\Goallord\\Addons\\Widgets\\' . $class;
			if ( class_exists( $fqcn ) ) {
				$widgets_manager->register( new $fqcn() );
			}
		}
	}
}
