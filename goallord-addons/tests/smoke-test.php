<?php
/**
 * Goallord Addons — Smoke Test
 *
 * Runs outside WordPress. Stubs the minimum WP / Elementor surface the plugin
 * touches at load time, then requires all plugin PHP files, and finally
 * introspects classes via Reflection to prove the widget wiring is intact.
 *
 * Usage:
 *   php tests/smoke-test.php
 *
 * Exit code: 0 on success, 1 on any failure.
 */

// Ensure we run in isolation — no real WP bootstrap.
if ( defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "This smoke test must run outside WordPress.\n" );
	exit( 1 );
}

// ----- 1. Stubs -----------------------------------------------------------

define( 'ABSPATH', __DIR__ . '/' );

if ( ! function_exists( 'esc_html__' ) )       { function esc_html__( $t, $d = null ) { return $t; } }
if ( ! function_exists( 'esc_html' ) )         { function esc_html( $t ) { return $t; } }
if ( ! function_exists( 'esc_attr' ) )         { function esc_attr( $t ) { return $t; } }
if ( ! function_exists( 'esc_url' ) )          { function esc_url( $t ) { return $t; } }
if ( ! function_exists( '__' ) )               { function __( $t, $d = null ) { return $t; } }
if ( ! function_exists( 'wp_kses' ) )          { function wp_kses( $t, $a ) { return $t; } }
if ( ! function_exists( 'wp_kses_post' ) )     { function wp_kses_post( $t ) { return $t; } }
if ( ! function_exists( 'wp_strip_all_tags' ) ){ function wp_strip_all_tags( $t ) { return strip_tags( (string) $t ); } }
if ( ! function_exists( 'wp_trim_words' ) )    { function wp_trim_words( $t, $n, $m = null ) { return $t; } }
if ( ! function_exists( 'wp_get_attachment_image' ) ) { function wp_get_attachment_image( ...$a ) { return ''; } }
if ( ! function_exists( 'nl2br' ) )            {} // php builtin
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $t ) { return $t; } }
if ( ! function_exists( 'get_post_types' ) )  { function get_post_types( $a = [], $fmt = 'names' ) { return [ 'post' => (object) [ 'labels' => (object) [ 'singular_name' => 'Post' ] ] ]; } }
if ( ! function_exists( 'taxonomy_exists' ) ) { function taxonomy_exists( $t ) { return true; } }
if ( ! function_exists( 'get_terms' ) )       { function get_terms( $args = [] ) { return []; } }
if ( ! function_exists( 'plugin_dir_path' ) )  { function plugin_dir_path( $f ) { return dirname( $f ) . '/'; } }
if ( ! function_exists( 'plugin_dir_url' ) )   { function plugin_dir_url( $f )  { return 'https://example.test/' . basename( dirname( $f ) ) . '/'; } }
if ( ! function_exists( 'plugin_basename' ) )  { function plugin_basename( $f ) { return basename( dirname( $f ) ) . '/' . basename( $f ); } }
if ( ! function_exists( 'includes_url' ) )     { function includes_url( $p = '' ) { return 'https://example.test/wp-includes/' . $p; } }
if ( ! function_exists( 'load_plugin_textdomain' ) ) { function load_plugin_textdomain( ...$a ) { return true; } }
if ( ! function_exists( 'wp_register_style' ) ){ function wp_register_style( ...$a ) { return true; } }
if ( ! function_exists( 'wp_register_script' ) ){function wp_register_script( ...$a ) { return true; } }
if ( ! function_exists( 'wp_enqueue_style' ) ) { function wp_enqueue_style( ...$a ) { return true; } }
if ( ! function_exists( 'wp_enqueue_script' ) ){ function wp_enqueue_script( ...$a ) { return true; } }
if ( ! function_exists( 'did_action' ) )       { function did_action( $h ) { return 1; } }
if ( ! function_exists( 'add_action' ) )       { function add_action( ...$a ) { return true; } }
if ( ! function_exists( 'add_filter' ) )       { function add_filter( ...$a ) { return true; } }
if ( ! function_exists( 'printf' ) )           {} // php builtin
if ( ! function_exists( 'sprintf' ) )          {} // php builtin

// Elementor stubs — classes the widget uses.
if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
	eval( '
	namespace Elementor;
	class Widget_Base {
		protected $render_attrs = [];
		public function get_name() { return ""; }
		public function get_title() { return ""; }
		public function get_icon() { return ""; }
		public function get_categories() { return []; }
		public function get_keywords() { return []; }
		public function get_style_depends() { return []; }
		public function get_script_depends() { return []; }
		protected function register_controls() {}
		protected function render() {}
		public function start_controls_section(...$a) {}
		public function end_controls_section() {}
		public function start_controls_tabs(...$a) {}
		public function end_controls_tabs() {}
		public function start_controls_tab(...$a) {}
		public function end_controls_tab() {}
		public function add_control(...$a) {}
		public function add_responsive_control(...$a) {}
		public function add_group_control(...$a) {}
		public function add_render_attribute(...$a) {}
		public function get_render_attribute_string(...$a) { return ""; }
		public function get_settings_for_display() { return []; }
		public function parse_text_editor( $t ) { return $t; }
	}
	class Controls_Manager {
		const TAB_CONTENT = "content";
		const TAB_STYLE   = "style";
		const TEXT        = "text";
		const TEXTAREA    = "textarea";
		const NUMBER      = "number";
		const SELECT      = "select";
		const SWITCHER    = "switcher";
		const CHOOSE      = "choose";
		const COLOR       = "color";
		const SLIDER      = "slider";
		const DIMENSIONS  = "dimensions";
		const URL         = "url";
		const MEDIA       = "media";
		const ICONS       = "icons";
		const WYSIWYG     = "wysiwyg";
		const REPEATER    = "repeater";
		const HEADING     = "heading";
		const SELECT2     = "select2";
	}
	class Repeater {
		public function add_control(...$a) {}
		public function get_controls() { return []; }
	}
	class Utils {
		public static function get_placeholder_image_src() { return "https://example.test/placeholder.png"; }
	}
	class Group_Control_Typography  { public static function get_type() { return "typography"; } }
	class Group_Control_Box_Shadow  { public static function get_type() { return "box-shadow"; } }
	class Group_Control_Border      { public static function get_type() { return "border"; } }
	class Group_Control_Background  { public static function get_type() { return "background"; } }
	class Group_Control_Image_Size  { public static function get_type() { return "image-size"; } }
	class Icons_Manager             { public static function render_icon( ...$a ) {} }
	' );
}

if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
	define( 'ELEMENTOR_VERSION', '3.23.0' );
}

// ----- 2. Load plugin -----------------------------------------------------

$plugin_root   = dirname( __DIR__ );
$main_file     = $plugin_root . '/goallord-addons.php';
$widget_file   = $plugin_root . '/widgets/class-announcement-news.php';
$plugin_class  = $plugin_root . '/includes/class-goallord-addons.php';
$manager_class = $plugin_root . '/includes/class-widgets-manager.php';
$helpers_class = $plugin_root . '/includes/helpers/class-helpers.php';

$report = [];
$fail   = 0;

function check( $label, $ok, array &$report, &$fail ) {
	$status   = $ok ? 'PASS' : 'FAIL';
	$report[] = sprintf( '  [%s] %s', $status, $label );
	if ( ! $ok ) {
		$fail++;
	}
}

// File existence.
check( 'main plugin file present',      file_exists( $main_file ),     $report, $fail );
check( 'plugin core class file present', file_exists( $plugin_class ),  $report, $fail );
check( 'widgets manager file present',   file_exists( $manager_class ), $report, $fail );
check( 'helpers file present',           file_exists( $helpers_class ), $report, $fail );
check( 'widget class file present',      file_exists( $widget_file ),   $report, $fail );
check( 'Daily_Schedule widget file present', file_exists( $plugin_root . '/widgets/class-daily-schedule.php' ), $report, $fail );
check( 'Announcement CSS asset present',     file_exists( $plugin_root . '/assets/css/announcement-news.css' ), $report, $fail );
check( 'Announcement JS asset present',      file_exists( $plugin_root . '/assets/js/announcement-news.js' ),   $report, $fail );
check( 'Daily Schedule CSS asset present',   file_exists( $plugin_root . '/assets/css/daily-schedule.css' ), $report, $fail );
check( 'Daily Schedule JS asset present',    file_exists( $plugin_root . '/assets/js/daily-schedule.js' ),   $report, $fail );
check( 'readme.txt present',             file_exists( $plugin_root . '/readme.txt' ), $report, $fail );

// Syntax check by requiring each file.
$syntax_ok = true;
try {
	require_once $plugin_class;
	require_once $manager_class;
	require_once $helpers_class;
	require_once $widget_file;
	require_once $plugin_root . '/widgets/class-daily-schedule.php';
	require_once $main_file; // triggers goallord_addons() -> Plugin::instance()
} catch ( \Throwable $e ) {
	$syntax_ok = false;
	$report[]  = '  [FAIL] syntax/load error: ' . $e->getMessage();
	$fail++;
}
check( 'all plugin PHP files loaded without fatal errors', $syntax_ok, $report, $fail );

// Constant defines.
check( 'GOALLORD_ADDONS_VERSION defined',  defined( 'GOALLORD_ADDONS_VERSION' ),  $report, $fail );
check( 'GOALLORD_ADDONS_PATH defined',     defined( 'GOALLORD_ADDONS_PATH' ),     $report, $fail );
check( 'GOALLORD_ADDONS_URL defined',      defined( 'GOALLORD_ADDONS_URL' ),      $report, $fail );
check( 'GOALLORD_ADDONS_BASENAME defined', defined( 'GOALLORD_ADDONS_BASENAME' ), $report, $fail );
check( 'GOALLORD_ADDONS_FILE defined',     defined( 'GOALLORD_ADDONS_FILE' ),     $report, $fail );
check( 'GOALLORD_ADDONS_ASSETS defined',   defined( 'GOALLORD_ADDONS_ASSETS' ),   $report, $fail );

// Class presence.
check( 'Plugin class defined',            class_exists( '\\Goallord\\Addons\\Plugin' ),                $report, $fail );
check( 'Widgets_Manager class defined',   class_exists( '\\Goallord\\Addons\\Widgets_Manager' ),       $report, $fail );
check( 'Helpers class defined',           class_exists( '\\Goallord\\Addons\\Helpers' ),               $report, $fail );
check( 'Announcement_News widget class defined', class_exists( '\\Goallord\\Addons\\Widgets\\Announcement_News' ), $report, $fail );
check( 'Daily_Schedule widget class defined',    class_exists( '\\Goallord\\Addons\\Widgets\\Daily_Schedule' ),    $report, $fail );

// Required widget methods.
if ( class_exists( '\\Goallord\\Addons\\Widgets\\Announcement_News' ) ) {
	$ref = new \ReflectionClass( '\\Goallord\\Addons\\Widgets\\Announcement_News' );
	foreach ( [ 'get_name', 'get_title', 'get_icon', 'get_categories', 'get_keywords', 'get_style_depends', 'get_script_depends', 'register_controls', 'render' ] as $method ) {
		check( "Announcement_News::{$method}() exists", $ref->hasMethod( $method ), $report, $fail );
	}
	foreach ( [ 'get_items_for_render', 'normalize_manual_item', 'fetch_dynamic_items', 'build_query_args', 'normalize_post_item' ] as $method ) {
		check( "Announcement_News::{$method}() exists (query pipeline)", $ref->hasMethod( $method ), $report, $fail );
	}
	$inst = $ref->newInstance();
	check( "get_name() returns 'goallord-announcement-news'", 'goallord-announcement-news' === $inst->get_name(), $report, $fail );
	check( "get_title() contains 'Goallord'",                 false !== strpos( $inst->get_title(), 'Goallord' ), $report, $fail );
	check( "get_categories() returns ['goallord-addons']",    [ 'goallord-addons' ] === $inst->get_categories(), $report, $fail );
	check( "get_style_depends() references registered handle", in_array( 'goallord-announcement-news', (array) $inst->get_style_depends(), true ), $report, $fail );
	check( "get_script_depends() references registered handle", in_array( 'goallord-announcement-news', (array) $inst->get_script_depends(), true ), $report, $fail );
}

// Daily_Schedule widget — shape & methods.
if ( class_exists( '\\Goallord\\Addons\\Widgets\\Daily_Schedule' ) ) {
	$ds_ref = new \ReflectionClass( '\\Goallord\\Addons\\Widgets\\Daily_Schedule' );
	foreach ( [ 'get_name', 'get_title', 'get_icon', 'get_categories', 'register_controls', 'render' ] as $method ) {
		check( "Daily_Schedule::{$method}() exists", $ds_ref->hasMethod( $method ), $report, $fail );
	}
	foreach ( [ 'assemble_groups', 'render_groups', 'render_header' ] as $method ) {
		check( "Daily_Schedule::{$method}() exists", $ds_ref->hasMethod( $method ), $report, $fail );
	}
	$ds_inst = $ds_ref->newInstance();
	check( "Daily_Schedule::get_name() = 'goallord-daily-schedule'", 'goallord-daily-schedule' === $ds_inst->get_name(), $report, $fail );
	check( "Daily_Schedule::get_title() contains 'Daily Schedule'",  false !== strpos( $ds_inst->get_title(), 'Daily Schedule' ), $report, $fail );
	check( "Daily_Schedule::get_style_depends() references handle",  in_array( 'goallord-daily-schedule', (array) $ds_inst->get_style_depends(), true ),  $report, $fail );
	check( "Daily_Schedule::get_script_depends() references handle", in_array( 'goallord-daily-schedule', (array) $ds_inst->get_script_depends(), true ), $report, $fail );
}

// Helpers — new query support methods.
if ( class_exists( '\\Goallord\\Addons\\Helpers' ) ) {
	check( 'Helpers::get_post_type_options() returns array', is_array( \Goallord\Addons\Helpers::get_post_type_options() ), $report, $fail );
	check( 'Helpers::get_term_options("category") returns array', is_array( \Goallord\Addons\Helpers::get_term_options( 'category' ) ), $report, $fail );
	check( 'Helpers::parse_id_list("1, 2, 3") = [1,2,3]', [ 1, 2, 3 ] === \Goallord\Addons\Helpers::parse_id_list( '1, 2, 3' ), $report, $fail );
	check( 'Helpers::parse_id_list("") = []',             [] === \Goallord\Addons\Helpers::parse_id_list( '' ),             $report, $fail );
	check( 'Helpers::parse_id_list([1,2,1]) dedups',      [ 1, 2 ] === \Goallord\Addons\Helpers::parse_id_list( [ 1, 2, 1 ] ), $report, $fail );
}

// Plugin singleton.
if ( class_exists( '\\Goallord\\Addons\\Plugin' ) ) {
	$instance = \Goallord\Addons\Plugin::instance();
	check( 'Plugin::instance() returns a Plugin instance', $instance instanceof \Goallord\Addons\Plugin, $report, $fail );
	check( 'Plugin::MINIMUM_ELEMENTOR_VERSION is 3.5.0', '3.5.0' === \Goallord\Addons\Plugin::MINIMUM_ELEMENTOR_VERSION, $report, $fail );
	check( 'Plugin::MINIMUM_PHP_VERSION is 7.4',         '7.4'   === \Goallord\Addons\Plugin::MINIMUM_PHP_VERSION,      $report, $fail );
}

// Text domain string sanity — the plugin should use 'goallord-addons' consistently.
$widget_src = file_get_contents( $widget_file );
$td_count   = substr_count( $widget_src, "'goallord-addons'" );
check( 'widget file uses goallord-addons text domain consistently (>= 50 occurrences)', $td_count >= 50, $report, $fail );

// Asset file non-empty.
check( 'CSS file > 200 bytes',  filesize( $plugin_root . '/assets/css/announcement-news.css' ) > 200, $report, $fail );
check( 'JS file > 200 bytes',   filesize( $plugin_root . '/assets/js/announcement-news.js' )  > 200, $report, $fail );

// ----- 3. Report ----------------------------------------------------------

echo "Goallord Addons — smoke test\n";
echo str_repeat( '-', 60 ) . "\n";
echo implode( "\n", $report ) . "\n";
echo str_repeat( '-', 60 ) . "\n";
echo sprintf( "%d checks, %d failures\n", count( $report ), $fail );

exit( $fail === 0 ? 0 : 1 );
