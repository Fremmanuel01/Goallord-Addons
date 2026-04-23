<?php
/**
 * Core plugin bootstrap.
 *
 * @package Goallord\Addons
 */

namespace Goallord\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';
	const MINIMUM_PHP_VERSION       = '7.4';

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * @var Widgets_Manager|null
	 */
	public $widgets_manager = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	public function i18n() {
		load_plugin_textdomain( 'goallord-addons', false, dirname( GOALLORD_ADDONS_BASENAME ) . '/languages/' );
	}

	public function on_plugins_loaded() {
		if ( ! $this->is_compatible() ) {
			return;
		}
		add_action( 'elementor/init', [ $this, 'init' ] );
	}

	private function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return false;
		}
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}
		return true;
	}

	public function init() {
		require_once GOALLORD_ADDONS_PATH . 'includes/helpers/class-helpers.php';
		require_once GOALLORD_ADDONS_PATH . 'includes/class-widgets-manager.php';

		$this->widgets_manager = new Widgets_Manager();

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

		// Per Elementor docs, register CSS/JS on wp_enqueue_scripts; Elementor then
		// enqueues them automatically via each widget's get_style_depends() / get_script_depends().
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		// Editor preview iframe uses wp_enqueue_scripts too, so the above covers the widget rendering.
		// This additional hook ensures the styles are available in contexts where Elementor
		// may enqueue widget assets from the editor panel.
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'register_styles' ] );
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'goallord-addons',
			[
				'title' => esc_html__( 'Goallord Addons', 'goallord-addons' ),
				'icon'  => 'eicon-apps',
			]
		);
	}

	public function register_styles() {
		wp_register_style(
			'goallord-announcement-news',
			GOALLORD_ADDONS_ASSETS . 'css/announcement-news.css',
			[],
			GOALLORD_ADDONS_VERSION
		);
		wp_register_style(
			'goallord-daily-schedule',
			GOALLORD_ADDONS_ASSETS . 'css/daily-schedule.css',
			[],
			GOALLORD_ADDONS_VERSION
		);
		wp_register_style(
			'goallord-advanced-hero',
			GOALLORD_ADDONS_ASSETS . 'css/advanced-hero.css',
			[],
			GOALLORD_ADDONS_VERSION
		);
	}

	public function register_scripts() {
		wp_register_script(
			'goallord-announcement-news',
			GOALLORD_ADDONS_ASSETS . 'js/announcement-news.js',
			[],
			GOALLORD_ADDONS_VERSION,
			true
		);
		wp_register_script(
			'goallord-daily-schedule',
			GOALLORD_ADDONS_ASSETS . 'js/daily-schedule.js',
			[],
			GOALLORD_ADDONS_VERSION,
			true
		);
		wp_register_script(
			'goallord-advanced-hero',
			GOALLORD_ADDONS_ASSETS . 'js/advanced-hero.js',
			[],
			GOALLORD_ADDONS_VERSION,
			true
		);
	}

	public function admin_notice_missing_elementor() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'goallord-addons' ),
			'<strong>' . esc_html__( 'Goallord Addons', 'goallord-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'goallord-addons' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'goallord-addons' ),
			'<strong>' . esc_html__( 'Goallord Addons', 'goallord-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'goallord-addons' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'goallord-addons' ),
			'<strong>' . esc_html__( 'Goallord Addons', 'goallord-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'goallord-addons' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}
