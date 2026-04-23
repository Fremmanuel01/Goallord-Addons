<?php
/**
 * Announcement & News Widget.
 *
 * Premium editorial-grade widget with 5 layouts, full style controls,
 * and performance-first animations.
 *
 * @package Goallord\Addons
 */

namespace Goallord\Addons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Goallord\Addons\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Announcement_News extends Widget_Base {

	public function get_name() {
		return 'goallord-announcement-news';
	}

	public function get_title() {
		return esc_html__( 'Goallord News & Announcements', 'goallord-addons' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'goallord-addons' ];
	}

	public function get_keywords() {
		return [ 'goallord', 'announcement', 'news', 'posts', 'grid', 'blog', 'editorial', 'post query', 'articles' ];
	}

	public function get_style_depends() {
		return [ 'goallord-announcement-news' ];
	}

	public function get_script_depends() {
		return [ 'goallord-announcement-news' ];
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_animation_controls();
		$this->register_style_controls();
	}

	/* =========================================================
	 * CONTENT TAB
	 * ========================================================= */

	private function register_content_controls() {

		/* ---------- Source ---------- */
		$this->start_controls_section(
			'section_source',
			[
				'label' => esc_html__( 'Content Source', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => esc_html__( 'Source', 'goallord-addons' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'manual',
				'options'     => [
					'manual' => esc_html__( 'Manual (Repeater)', 'goallord-addons' ),
					'posts'  => esc_html__( 'Dynamic — WordPress Posts', 'goallord-addons' ),
				],
				'description' => esc_html__( 'Manual: you enter each item below. Dynamic: pull published posts via WP_Query.', 'goallord-addons' ),
			]
		);

		$this->end_controls_section();

		/* ---------- Layout Selector ---------- */
		$this->start_controls_section(
			'section_layout_picker',
			[
				'label' => esc_html__( 'Layout', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout Style', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'editorial',
				'options' => [
					'editorial' => esc_html__( 'Editorial Grid', 'goallord-addons' ),
					'minimal'   => esc_html__( 'Minimal List', 'goallord-addons' ),
					'featured'  => esc_html__( 'Featured + Grid', 'goallord-addons' ),
					'sidebar'   => esc_html__( 'Sidebar Bulletin', 'goallord-addons' ),
					'timeline'  => esc_html__( 'Timeline', 'goallord-addons' ),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'     => esc_html__( 'Columns', 'goallord-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'   => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .goallord-an__grid' => '--goallord-an-columns: {{VALUE}};',
				],
				'condition' => [
					'layout' => [ 'editorial', 'featured' ],
				],
			]
		);

		$this->add_control(
			'image_ratio',
			[
				'label'   => esc_html__( 'Image Ratio', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => [
					'16-9'  => esc_html__( 'Landscape 16:9', 'goallord-addons' ),
					'4-3'   => esc_html__( 'Classic 4:3', 'goallord-addons' ),
					'1-1'   => esc_html__( 'Square 1:1', 'goallord-addons' ),
					'3-4'   => esc_html__( 'Portrait 3:4', 'goallord-addons' ),
					'21-9'  => esc_html__( 'Cinematic 21:9', 'goallord-addons' ),
				],
				'selectors_dictionary' => [
					'16-9' => '16 / 9',
					'4-3'  => '4 / 3',
					'1-1'  => '1 / 1',
					'3-4'  => '3 / 4',
					'21-9' => '21 / 9',
				],
				'selectors' => [
					'{{WRAPPER}} .goallord-an__image' => '--goallord-an-image-ratio: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'image_size',
				'default' => 'medium_large',
				'exclude' => [ 'custom' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Section Header ---------- */
		$this->start_controls_section(
			'section_header',
			[
				'label' => esc_html__( 'Section Header', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_header',
			[
				'label'        => esc_html__( 'Show Section Header', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'goallord-addons' ),
				'label_off'    => esc_html__( 'Hide', 'goallord-addons' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'header_eyebrow',
			[
				'label'       => esc_html__( 'Eyebrow', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'LATEST UPDATES', 'goallord-addons' ),
				'condition'   => [ 'show_header' => 'yes' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'header_title',
			[
				'label'     => esc_html__( 'Title', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => esc_html__( 'Announcements & News', 'goallord-addons' ),
				'condition' => [ 'show_header' => 'yes' ],
				'dynamic'   => [ 'active' => true ],
			]
		);

		$this->add_control(
			'header_subtitle',
			[
				'label'     => esc_html__( 'Subtitle', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => esc_html__( 'Stay informed with the most recent stories, events, and announcements from our community.', 'goallord-addons' ),
				'condition' => [ 'show_header' => 'yes' ],
				'dynamic'   => [ 'active' => true ],
			]
		);

		$this->add_control(
			'header_title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'goallord-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => [
					'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
					'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
					'div' => 'div', 'p' => 'p',
				],
				'condition' => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'header_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'goallord-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'goallord-addons' ),   'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'goallord-addons' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'goallord-addons' ),  'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__header' => 'text-align: {{VALUE}};',
				],
				'condition' => [ 'show_header' => 'yes' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Repeater Items (manual mode) ---------- */
		$this->start_controls_section(
			'section_items',
			[
				'label'     => esc_html__( 'Items', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'source' => 'manual' ],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_image',
			[
				'label'   => esc_html__( 'Image', 'goallord-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => Utils::get_placeholder_image_src() ],
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_icon',
			[
				'label'       => esc_html__( 'Icon (optional)', 'goallord-addons' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => '',
					'library' => '',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$repeater->add_control(
			'item_category',
			[
				'label'   => esc_html__( 'Category', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'News', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_date',
			[
				'label'       => esc_html__( 'Date', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Apr 23, 2026', 'goallord-addons' ),
				'description' => esc_html__( 'Free-form date text. Use whatever format you prefer.', 'goallord-addons' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_author',
			[
				'label'   => esc_html__( 'Author', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'Title', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'A thoughtful headline that invites the reader in.', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_description',
			[
				'label'   => esc_html__( 'Description', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'A short editorial summary that gives the reader enough context to care about this story.', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_full_content',
			[
				'label'       => esc_html__( 'Full Content (optional)', 'goallord-addons' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => '',
				'description' => esc_html__( 'Longer rich-text content. Rendered inside the Featured hero card, or when "Show Full Content" is enabled.', 'goallord-addons' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_extra_meta',
			[
				'label'       => esc_html__( 'Extra Meta (optional)', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'Optional extra meta line — e.g. read time, location, tag.', 'goallord-addons' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_badge_show',
			[
				'label'        => esc_html__( 'Show Badge', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater->add_control(
			'item_badge_text',
			[
				'label'     => esc_html__( 'Badge Text', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'New', 'goallord-addons' ),
				'condition' => [ 'item_badge_show' => 'yes' ],
				'dynamic'   => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_badge_style',
			[
				'label'   => esc_html__( 'Badge Style', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'goallord-addons' ),
					'success' => esc_html__( 'Success', 'goallord-addons' ),
					'warning' => esc_html__( 'Warning', 'goallord-addons' ),
					'urgent'  => esc_html__( 'Urgent', 'goallord-addons' ),
					'info'    => esc_html__( 'Info', 'goallord-addons' ),
				],
				'condition' => [ 'item_badge_show' => 'yes' ],
			]
		);

		$repeater->add_control(
			'item_cta_text',
			[
				'label'   => esc_html__( 'CTA Text', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'       => esc_html__( 'Link', 'goallord-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'goallord-addons' ),
				'default'     => [
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Announcement Items', 'goallord-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => [
					[
						'item_category'    => esc_html__( 'Campus Life', 'goallord-addons' ),
						'item_date'        => esc_html__( 'Apr 23, 2026', 'goallord-addons' ),
						'item_title'       => esc_html__( 'Spring convocation brings record attendance', 'goallord-addons' ),
						'item_description' => esc_html__( 'Students, faculty, and alumni gathered for a weekend of reflection, prayer, and renewed purpose.', 'goallord-addons' ),
						'item_cta_text'    => esc_html__( 'Read More', 'goallord-addons' ),
						'item_badge_show'  => 'yes',
						'item_badge_text'  => esc_html__( 'Featured', 'goallord-addons' ),
					],
					[
						'item_category'    => esc_html__( 'Academics', 'goallord-addons' ),
						'item_date'        => esc_html__( 'Apr 20, 2026', 'goallord-addons' ),
						'item_title'       => esc_html__( 'New theology curriculum launches this fall', 'goallord-addons' ),
						'item_description' => esc_html__( 'A refreshed three-year program designed around classical formation and modern ministry.', 'goallord-addons' ),
						'item_cta_text'    => esc_html__( 'Read More', 'goallord-addons' ),
					],
					[
						'item_category'    => esc_html__( 'Community', 'goallord-addons' ),
						'item_date'        => esc_html__( 'Apr 15, 2026', 'goallord-addons' ),
						'item_title'       => esc_html__( 'Alumni Sunday draws a generation back home', 'goallord-addons' ),
						'item_description' => esc_html__( 'Graduates from across five decades returned for an afternoon of fellowship and thanksgiving.', 'goallord-addons' ),
						'item_cta_text'    => esc_html__( 'Read More', 'goallord-addons' ),
					],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Query (posts mode) ---------- */
		$this->start_controls_section(
			'section_query',
			[
				'label'     => esc_html__( 'Query', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'source' => 'posts' ],
			]
		);

		$this->add_control(
			'query_post_type',
			[
				'label'   => esc_html__( 'Post Type', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => Helpers::get_post_type_options(),
			]
		);

		$this->add_control(
			'query_posts_per_page',
			[
				'label'   => esc_html__( 'Posts Per Page', 'goallord-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 100,
			]
		);

		$this->add_control(
			'query_offset',
			[
				'label'       => esc_html__( 'Offset', 'goallord-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => esc_html__( 'Skip the first N posts.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'query_orderby',
			[
				'label'   => esc_html__( 'Order By', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'          => esc_html__( 'Publish Date', 'goallord-addons' ),
					'modified'      => esc_html__( 'Modified Date', 'goallord-addons' ),
					'title'         => esc_html__( 'Title', 'goallord-addons' ),
					'menu_order'    => esc_html__( 'Menu Order', 'goallord-addons' ),
					'rand'          => esc_html__( 'Random', 'goallord-addons' ),
					'comment_count' => esc_html__( 'Comment Count', 'goallord-addons' ),
				],
			]
		);

		$this->add_control(
			'query_order',
			[
				'label'   => esc_html__( 'Order', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' => esc_html__( 'Descending', 'goallord-addons' ),
					'ASC'  => esc_html__( 'Ascending', 'goallord-addons' ),
				],
				'condition' => [ 'query_orderby!' => 'rand' ],
			]
		);

		$this->add_control(
			'query_taxonomy_heading',
			[
				'label'     => esc_html__( 'Taxonomies', 'goallord-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'query_category_in',
			[
				'label'       => esc_html__( 'Categories (include)', 'goallord-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Helpers::get_term_options( 'category' ),
				'condition'   => [ 'query_post_type' => 'post' ],
			]
		);

		$this->add_control(
			'query_tag_in',
			[
				'label'       => esc_html__( 'Tags (include)', 'goallord-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Helpers::get_term_options( 'post_tag' ),
				'condition'   => [ 'query_post_type' => 'post' ],
			]
		);

		$this->add_control(
			'query_advanced_heading',
			[
				'label'     => esc_html__( 'Advanced', 'goallord-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'query_post_in',
			[
				'label'       => esc_html__( 'Include Post IDs', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '12, 34, 56',
				'description' => esc_html__( 'Comma-separated post IDs. Overrides taxonomy filters.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'query_post_not_in',
			[
				'label'       => esc_html__( 'Exclude Post IDs', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '12, 34, 56',
				'description' => esc_html__( 'Comma-separated post IDs to exclude.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'query_exclude_current',
			[
				'label'        => esc_html__( 'Exclude Current Post', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Useful for "Related Posts" contexts inside a Single template.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'query_fallback_heading',
			[
				'label'     => esc_html__( 'Fallback', 'goallord-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'query_cta_text',
			[
				'label'   => esc_html__( 'CTA Text', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'goallord-addons' ),
				'description' => esc_html__( 'Used for every dynamic post card.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'query_date_format',
			[
				'label'       => esc_html__( 'Date Format', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'e.g. F j, Y — leave empty to use the site default.', 'goallord-addons' ),
				'description' => esc_html__( 'PHP date format. Empty = site setting.', 'goallord-addons' ),
			]
		);

		$this->end_controls_section();

		/* ---------- Display Toggles ---------- */
		$this->start_controls_section(
			'section_display',
			[
				'label' => esc_html__( 'Display Options', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'        => esc_html__( 'Show Image', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'        => esc_html__( 'Show Icons', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Master switch. Icons only render when set on a specific item.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'show_full_content',
			[
				'label'        => esc_html__( 'Show Full Content', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Always render Full Content when set. When off, it only shows in the Featured hero card.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'        => esc_html__( 'Show Category', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'        => esc_html__( 'Show Date', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'        => esc_html__( 'Show Author', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label'     => esc_html__( 'Meta Separator', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '•',
			]
		);

		$this->add_control(
			'show_badge',
			[
				'label'        => esc_html__( 'Show Badges', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Master switch. Each item also has its own toggle.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => esc_html__( 'Show Description', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'        => esc_html__( 'Show CTA / Read More', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
					'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
					'div' => 'div', 'p' => 'p',
				],
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'       => esc_html__( 'Excerpt Length (words)', 'goallord-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 200,
				'description' => esc_html__( '0 = full text, otherwise limits description to N words.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'cta_icon',
			[
				'label'       => esc_html__( 'CTA Arrow Character', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '→',
				'description' => esc_html__( 'Pure text/unicode. No icon libraries required.', 'goallord-addons' ),
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * LAYOUT / SETTINGS TAB (still content tab for spacing)
	 * ========================================================= */

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout_settings',
			[
				'label' => esc_html__( 'Layout Settings', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => esc_html__( 'Grid Gap', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0, 'max' => 120, 'step' => 1 ],
					'rem' => [ 'min' => 0, 'max' => 10,  'step' => 0.1 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 32 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_bottom_spacing',
			[
				'label'      => esc_html__( 'Header Bottom Spacing', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0, 'max' => 200, 'step' => 1 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 56 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'     => esc_html__( 'Card Text Alignment', 'goallord-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'goallord-addons' ),   'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'goallord-addons' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'goallord-addons' ),  'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'equal_heights',
			[
				'label'     => esc_html__( 'Card Heights', 'goallord-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'stretch',
				'options'   => [
					'stretch' => esc_html__( 'Equal (stretch)', 'goallord-addons' ),
					'start'   => esc_html__( 'Natural (content-based)', 'goallord-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .goallord-an__grid' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * ANIMATION TAB (still content tab as requested)
	 * ========================================================= */

	private function register_animation_controls() {
		$this->start_controls_section(
			'section_animations',
			[
				'label' => esc_html__( 'Animations & Interactions', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_animations',
			[
				'label'        => esc_html__( 'Enable Entrance Animations', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'animation_style',
			[
				'label'   => esc_html__( 'Animation Style', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade-up',
				'options' => [
					'fade'      => esc_html__( 'Fade', 'goallord-addons' ),
					'fade-up'   => esc_html__( 'Fade Up', 'goallord-addons' ),
					'fade-down' => esc_html__( 'Fade Down', 'goallord-addons' ),
					'zoom'      => esc_html__( 'Zoom', 'goallord-addons' ),
					'slide-left'=> esc_html__( 'Slide In Left', 'goallord-addons' ),
					'slide-right'=>esc_html__( 'Slide In Right', 'goallord-addons' ),
				],
				'condition' => [ 'enable_animations' => 'yes' ],
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label'     => esc_html__( 'Duration (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 700,
				'min'       => 100,
				'max'       => 3000,
				'step'      => 50,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-an-anim-duration: {{VALUE}}ms;',
				],
				'condition' => [ 'enable_animations' => 'yes' ],
			]
		);

		$this->add_control(
			'animation_delay',
			[
				'label'     => esc_html__( 'Delay (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 3000,
				'step'      => 50,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-an-anim-delay: {{VALUE}}ms;',
				],
				'condition' => [ 'enable_animations' => 'yes' ],
			]
		);

		$this->add_control(
			'animation_stagger',
			[
				'label'        => esc_html__( 'Stagger Reveal', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [ 'enable_animations' => 'yes' ],
			]
		);

		$this->add_control(
			'animation_stagger_delay',
			[
				'label'     => esc_html__( 'Stagger Delay (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 120,
				'min'       => 20,
				'max'       => 1000,
				'step'      => 10,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-an-stagger: {{VALUE}}ms;',
				],
				'condition' => [
					'enable_animations' => 'yes',
					'animation_stagger' => 'yes',
				],
			]
		);

		$this->add_control(
			'animation_easing',
			[
				'label'   => esc_html__( 'Easing', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'premium',
				'options' => [
					'premium'   => esc_html__( 'Premium (cubic-bezier)', 'goallord-addons' ),
					'ease-out'  => esc_html__( 'Ease Out', 'goallord-addons' ),
					'ease-in-out' => esc_html__( 'Ease In-Out', 'goallord-addons' ),
					'linear'    => esc_html__( 'Linear', 'goallord-addons' ),
				],
				'selectors_dictionary' => [
					'premium'     => 'cubic-bezier(0.22, 1, 0.36, 1)',
					'ease-out'    => 'ease-out',
					'ease-in-out' => 'ease-in-out',
					'linear'      => 'linear',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-an-easing: {{VALUE}};',
				],
				'condition' => [ 'enable_animations' => 'yes' ],
			]
		);

		$this->add_control(
			'hover_heading',
			[
				'label' => esc_html__( 'Hover', 'goallord-addons' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'enable_hover_animations',
			[
				'label'        => esc_html__( 'Enable Card Hover Lift', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'goallord-an-hover--',
			]
		);

		$this->add_control(
			'enable_image_zoom',
			[
				'label'        => esc_html__( 'Enable Image Zoom on Hover', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'goallord-an-zoom--',
			]
		);

		$this->add_control(
			'image_zoom_scale',
			[
				'label'     => esc_html__( 'Image Zoom Scale', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 1, 'max' => 1.15, 'step' => 0.01 ] ],
				'default'   => [ 'size' => 1.05 ],
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-an-zoom-scale: {{SIZE}};',
				],
				'condition' => [ 'enable_image_zoom' => 'yes' ],
			]
		);

		$this->add_control(
			'hover_lift_distance',
			[
				'label'      => esc_html__( 'Card Lift Distance', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 24, 'step' => 1 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 6 ],
				'selectors'  => [
					'{{WRAPPER}}' => '--goallord-an-lift: -{{SIZE}}{{UNIT}};',
				],
				'condition' => [ 'enable_hover_animations' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * STYLE TAB
	 * ========================================================= */

	private function register_style_controls() {

		/* ---------- Wrapper ---------- */
		$this->start_controls_section(
			'section_style_wrapper',
			[
				'label' => esc_html__( 'Wrapper', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'wrapper_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .goallord-an',
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_margin',
			[
				'label'      => esc_html__( 'Margin', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Section Header ---------- */
		$this->start_controls_section(
			'section_style_header',
			[
				'label'     => esc_html__( 'Section Header', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_control(
			'eyebrow_heading',
			[
				'label' => esc_html__( 'Eyebrow', 'goallord-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'eyebrow_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8a6d3b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__eyebrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .goallord-an__eyebrow',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 12 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 2.5 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_control(
			'header_title_heading',
			[
				'label'     => esc_html__( 'Title', 'goallord-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'header_title_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .goallord-an__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 44 ] ],
					'font_weight' => [ 'default' => '700' ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.15 ] ],
				],
			]
		);

		$this->add_control(
			'header_subtitle_heading',
			[
				'label'     => esc_html__( 'Subtitle', 'goallord-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'header_subtitle_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#475569',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_subtitle_typography',
				'selector' => '{{WRAPPER}} .goallord-an__subtitle',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 17 ] ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'header_subtitle_max_width',
			[
				'label'      => esc_html__( 'Subtitle Max Width', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 1000 ], '%' => [ 'min' => 10, 'max' => 100 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 640 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__subtitle' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Card ---------- */
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'card_tabs' );

		/* Normal */
		$this->start_controls_tab( 'card_tab_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .goallord-an__card',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => '#ffffff' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .goallord-an__card',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'isLinked' => true, 'unit' => 'px' ] ],
					'color'  => [ 'default' => '#eef2f7' ],
				],
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .goallord-an__card',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [
						'default' => [
							'horizontal' => 0,
							'vertical'   => 4,
							'blur'       => 18,
							'spread'     => 0,
							'color'      => 'rgba(15, 23, 42, 0.04)',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_inner_padding',
			[
				'label'      => esc_html__( 'Inner Content Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 24, 'right' => 24, 'bottom' => 28, 'left' => 24, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		/* Hover */
		$this->start_controls_tab( 'card_tab_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .goallord-an__card:hover',
			]
		);

		$this->add_control(
			'card_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow_hover',
				'selector' => '{{WRAPPER}} .goallord-an__card:hover',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [
						'default' => [
							'horizontal' => 0,
							'vertical'   => 18,
							'blur'       => 36,
							'spread'     => 0,
							'color'      => 'rgba(15, 23, 42, 0.10)',
						],
					],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* ---------- Image ---------- */
		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__( 'Image', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_image' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_css_filter',
			[
				'label'   => esc_html__( 'CSS Filter', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'      => esc_html__( 'None', 'goallord-addons' ),
					'grayscale' => esc_html__( 'Grayscale', 'goallord-addons' ),
					'sepia'     => esc_html__( 'Sepia', 'goallord-addons' ),
					'warm'      => esc_html__( 'Warm Editorial', 'goallord-addons' ),
				],
				'selectors_dictionary' => [
					'none'      => 'none',
					'grayscale' => 'grayscale(1)',
					'sepia'     => 'sepia(0.4)',
					'warm'      => 'saturate(1.05) contrast(1.02) brightness(1.01)',
				],
				'selectors' => [
					'{{WRAPPER}} .goallord-an__image img' => 'filter: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Icon ---------- */
		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => esc_html__( 'Icon', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_icon' => 'yes' ],
			]
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab( 'icon_tab_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b8860b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__icon'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .goallord-an__icon svg'    => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_tab_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card:hover .goallord-an__icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .goallord-an__card:hover .goallord-an__icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_hover',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card:hover .goallord-an__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 8, 'max' => 80, 'step' => 1 ],
					'em' => [ 'min' => 0.5, 'max' => 5, 'step' => 0.1 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 18 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => esc_html__( 'Spacing Below', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Meta ---------- */
		$this->start_controls_section(
			'section_style_meta',
			[
				'label' => esc_html__( 'Meta (Category / Date / Author)', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_category_color',
			[
				'label'     => esc_html__( 'Category Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b8860b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__category' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .goallord-an__meta',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 12 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 1.5 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label'      => esc_html__( 'Spacing Below', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 12 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Badge ---------- */
		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__( 'Badge', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_badge' => 'yes' ],
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'       => esc_html__( 'Background', 'goallord-addons' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Leave empty to use the per-item variant colors.', 'goallord-addons' ),
				'selectors'   => [
					'{{WRAPPER}} .goallord-an__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Text Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .goallord-an__badge',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 11 ] ],
					'font_weight'   => [ 'default' => '700' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 6, 'right' => 10, 'bottom' => 6, 'left' => 10, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Title ---------- */
		$this->start_controls_section(
			'section_style_item_title',
			[
				'label' => esc_html__( 'Card Title', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'card_title_tabs' );

		$this->start_controls_tab( 'card_title_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_control(
			'item_title_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card-title, {{WRAPPER}} .goallord-an__card-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'card_title_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_control(
			'item_title_color_hover',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b8860b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__card:hover .goallord-an__card-title, {{WRAPPER}} .goallord-an__card:hover .goallord-an__card-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_title_typography',
				'selector' => '{{WRAPPER}} .goallord-an__card-title',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 22 ] ],
					'font_weight' => [ 'default' => '700' ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.3 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'item_title_spacing',
			[
				'label'      => esc_html__( 'Spacing Below', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 12 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__card-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Description ---------- */
		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__( 'Description', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_description' => 'yes' ],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#475569',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .goallord-an__description',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 15 ] ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'      => esc_html__( 'Spacing Below', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Read More / CTA ---------- */
		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Read More / CTA', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_button' => 'yes' ],
			]
		);

		$this->start_controls_tabs( 'cta_tabs' );

		$this->start_controls_tab( 'cta_tab_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_control(
			'cta_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_bg',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__cta' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'cta_tab_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_control(
			'cta_color_hover',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b8860b',
				'selectors' => [
					'{{WRAPPER}} .goallord-an__cta:hover, {{WRAPPER}} .goallord-an__card:hover .goallord-an__cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_bg_hover',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .goallord-an__cta:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .goallord-an__cta',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
					'font_weight' => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 0.3 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'cta_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-an__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Divider (list / sidebar / timeline) ---------- */
		$this->start_controls_section(
			'section_style_divider',
			[
				'label' => esc_html__( 'Divider', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'description' => esc_html__( 'Divider between cards — applies to Minimal List, Sidebar Bulletin, and Timeline layouts.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'divider_enable',
			[
				'label'        => esc_html__( 'Show Divider', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'goallord-an-divider--',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => esc_html__( 'Style', 'goallord-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'goallord-addons' ),
					'dashed' => esc_html__( 'Dashed', 'goallord-addons' ),
					'dotted' => esc_html__( 'Dotted', 'goallord-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .goallord-an--layout-minimal .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-sidebar .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-timeline .goallord-an__card' => 'border-bottom-style: {{VALUE}};',
				],
				'condition' => [ 'divider_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => [
					'{{WRAPPER}} .goallord-an--layout-minimal .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-sidebar .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-timeline .goallord-an__card' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [ 'divider_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label'     => esc_html__( 'Thickness', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px' ],
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 6, 'step' => 1 ] ],
				'default'   => [ 'unit' => 'px', 'size' => 1 ],
				'selectors' => [
					'{{WRAPPER}} .goallord-an--layout-minimal .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-sidebar .goallord-an__card,
					 {{WRAPPER}} .goallord-an--layout-timeline .goallord-an__card' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 'divider_enable' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * RENDER
	 * ========================================================= */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $this->get_items_for_render( $settings );

		if ( empty( $items ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$msg = ( 'posts' === ( $settings['source'] ?? 'manual' ) )
					? esc_html__( 'No posts match this query. Adjust the Query settings or switch Source to Manual.', 'goallord-addons' )
					: esc_html__( 'Add items from the "Items" panel to see them here.', 'goallord-addons' );
				echo '<div class="goallord-an goallord-an--empty">' . $msg . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			return;
		}

		$layout     = in_array( $settings['layout'] ?? 'editorial', [ 'editorial', 'minimal', 'featured', 'sidebar', 'timeline' ], true )
			? $settings['layout']
			: 'editorial';
		$title_tag  = Helpers::safe_tag( $settings['title_tag'] ?? 'h3', 'h3' );
		$header_tag = Helpers::safe_tag( $settings['header_title_tag'] ?? 'h2', 'h2' );

		$this->add_render_attribute( 'wrapper', 'class', [
			'goallord-an',
			'goallord-an--layout-' . $layout,
		] );

		if ( 'yes' === ( $settings['enable_animations'] ?? 'yes' ) ) {
			$this->add_render_attribute( 'wrapper', 'data-goallord-an-animate', '1' );
			$this->add_render_attribute( 'wrapper', 'data-animation', esc_attr( $settings['animation_style'] ?? 'fade-up' ) );
			$this->add_render_attribute( 'wrapper', 'data-stagger', ( 'yes' === ( $settings['animation_stagger'] ?? 'yes' ) ) ? '1' : '0' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php $this->render_header( $settings, $header_tag ); ?>

			<div class="goallord-an__grid" role="list">
				<?php $this->render_items( $items, $settings, $title_tag, $layout ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Returns the list of items to render, normalized to a single shape
	 * regardless of source (manual repeater or WP_Query).
	 */
	private function get_items_for_render( $settings ) {
		$source = ( 'posts' === ( $settings['source'] ?? 'manual' ) ) ? 'posts' : 'manual';

		if ( 'posts' === $source ) {
			return $this->fetch_dynamic_items( $settings );
		}

		$raw = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : [];
		$out = [];
		foreach ( $raw as $r ) {
			$out[] = $this->normalize_manual_item( $r );
		}
		return $out;
	}

	private function normalize_manual_item( $raw ) {
		return [
			'image_id'     => isset( $raw['item_image']['id'] )  ? (int) $raw['item_image']['id']  : 0,
			'image_url'    => isset( $raw['item_image']['url'] ) ? (string) $raw['item_image']['url'] : '',
			'title'        => (string) ( $raw['item_title']        ?? '' ),
			'description'  => (string) ( $raw['item_description']  ?? '' ),
			'full_content' => (string) ( $raw['item_full_content'] ?? '' ),
			'category'     => (string) ( $raw['item_category']     ?? '' ),
			'date'         => (string) ( $raw['item_date']         ?? '' ),
			'author'       => (string) ( $raw['item_author']       ?? '' ),
			'extra_meta'   => (string) ( $raw['item_extra_meta']   ?? '' ),
			'icon'         => ( isset( $raw['item_icon'] ) && is_array( $raw['item_icon'] ) ) ? $raw['item_icon'] : [],
			'badge_show'   => 'yes' === ( $raw['item_badge_show']  ?? '' ),
			'badge_text'   => (string) ( $raw['item_badge_text']   ?? '' ),
			'badge_style'  => (string) ( $raw['item_badge_style']  ?? 'default' ),
			'cta_text'     => (string) ( $raw['item_cta_text']     ?? __( 'Read More', 'goallord-addons' ) ),
			'link_url'     => isset( $raw['item_link']['url'] ) ? (string) $raw['item_link']['url'] : '',
			'link_target'  => ! empty( $raw['item_link']['is_external'] ) ? '_blank' : '',
			'link_nofollow'=> ! empty( $raw['item_link']['nofollow'] )    ? 'nofollow' : '',
		];
	}

	/**
	 * Runs WP_Query with the user's query settings and maps each post
	 * to the normalized item shape.
	 */
	private function fetch_dynamic_items( $settings ) {
		if ( ! function_exists( 'get_posts' ) ) {
			return [];
		}

		$args = $this->build_query_args( $settings );

		$query = new \WP_Query( $args );
		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return [];
		}

		$out = [];
		foreach ( $query->posts as $post ) {
			$out[] = $this->normalize_post_item( $post, $settings );
		}
		wp_reset_postdata();
		return $out;
	}

	private function build_query_args( $settings ) {
		$post_type  = (string) ( $settings['query_post_type'] ?? 'post' );
		$per_page   = max( 1, (int) ( $settings['query_posts_per_page'] ?? 6 ) );
		$offset     = max( 0, (int) ( $settings['query_offset'] ?? 0 ) );
		$orderby    = (string) ( $settings['query_orderby'] ?? 'date' );
		$order      = ( 'ASC' === strtoupper( (string) ( $settings['query_order'] ?? 'DESC' ) ) ) ? 'ASC' : 'DESC';
		$cat_ids    = Helpers::parse_id_list( $settings['query_category_in'] ?? [] );
		$tag_ids    = Helpers::parse_id_list( $settings['query_tag_in'] ?? [] );
		$include    = Helpers::parse_id_list( $settings['query_post_in'] ?? '' );
		$exclude    = Helpers::parse_id_list( $settings['query_post_not_in'] ?? '' );
		$excl_curr  = 'yes' === ( $settings['query_exclude_current'] ?? '' );

		$args = [
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'offset'              => $offset,
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'suppress_filters'    => false,
		];

		if ( ! empty( $include ) ) {
			$args['post__in'] = $include;
			$args['orderby']  = 'post__in';
		}

		if ( $excl_curr ) {
			$current = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
			if ( $current > 0 ) {
				$exclude[] = $current;
			}
		}
		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = array_values( array_unique( $exclude ) );
		}

		if ( 'post' === $post_type ) {
			if ( ! empty( $cat_ids ) ) {
				$args['category__in'] = $cat_ids;
			}
			if ( ! empty( $tag_ids ) ) {
				$args['tag__in'] = $tag_ids;
			}
		}

		return $args;
	}

	private function normalize_post_item( $post, $settings ) {
		$post_id = (int) $post->ID;

		$date_format = trim( (string) ( $settings['query_date_format'] ?? '' ) );
		if ( '' === $date_format && function_exists( 'get_option' ) ) {
			$date_format = (string) get_option( 'date_format', 'F j, Y' );
		}

		$category = '';
		if ( function_exists( 'get_the_category' ) ) {
			$cats = get_the_category( $post_id );
			if ( ! empty( $cats ) && isset( $cats[0]->name ) ) {
				$category = (string) $cats[0]->name;
			}
		}
		if ( '' === $category && function_exists( 'get_post_type_object' ) ) {
			$pt = get_post_type_object( $post->post_type );
			if ( $pt && isset( $pt->labels->singular_name ) ) {
				$category = (string) $pt->labels->singular_name;
			}
		}

		$excerpt = '';
		if ( function_exists( 'has_excerpt' ) && has_excerpt( $post_id ) && function_exists( 'get_the_excerpt' ) ) {
			$excerpt = (string) get_the_excerpt( $post_id );
		} else {
			$raw_content = isset( $post->post_content ) ? (string) $post->post_content : '';
			if ( function_exists( 'strip_shortcodes' ) ) {
				$raw_content = strip_shortcodes( $raw_content );
			}
			$excerpt = wp_strip_all_tags( $raw_content );
		}

		$cta_text_default = function_exists( '__' ) ? __( 'Read More', 'goallord-addons' ) : 'Read More';

		return [
			'image_id'     => function_exists( 'get_post_thumbnail_id' ) ? (int) get_post_thumbnail_id( $post_id ) : 0,
			'image_url'    => '',
			'title'        => function_exists( 'get_the_title' ) ? (string) get_the_title( $post_id ) : (string) $post->post_title,
			'description'  => $excerpt,
			'full_content' => '',
			'category'     => $category,
			'date'         => function_exists( 'get_the_date' ) ? (string) get_the_date( $date_format, $post_id ) : '',
			'author'       => function_exists( 'get_the_author_meta' ) ? (string) get_the_author_meta( 'display_name', $post->post_author ) : '',
			'extra_meta'   => '',
			'icon'         => [],
			'badge_show'   => false,
			'badge_text'   => '',
			'badge_style'  => 'default',
			'cta_text'     => (string) ( $settings['query_cta_text'] ?? $cta_text_default ),
			'link_url'     => function_exists( 'get_permalink' ) ? (string) get_permalink( $post_id ) : '',
			'link_target'  => '',
			'link_nofollow'=> '',
		];
	}

	private function render_header( $settings, $header_tag ) {
		if ( 'yes' !== ( $settings['show_header'] ?? 'yes' ) ) {
			return;
		}
		$eyebrow  = trim( (string) ( $settings['header_eyebrow']  ?? '' ) );
		$title    = trim( (string) ( $settings['header_title']    ?? '' ) );
		$subtitle = trim( (string) ( $settings['header_subtitle'] ?? '' ) );

		if ( '' === $eyebrow && '' === $title && '' === $subtitle ) {
			return;
		}
		?>
		<div class="goallord-an__header">
			<?php if ( '' !== $eyebrow ) : ?>
				<div class="goallord-an__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>

			<?php if ( '' !== $title ) : ?>
				<<?php echo esc_html( $header_tag ); ?> class="goallord-an__title">
					<?php echo wp_kses( nl2br( $title ), Helpers::kses_inline() ); ?>
				</<?php echo esc_html( $header_tag ); ?>>
			<?php endif; ?>

			<?php if ( '' !== $subtitle ) : ?>
				<p class="goallord-an__subtitle"><?php echo wp_kses( nl2br( $subtitle ), Helpers::kses_inline() ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_items( $items, $settings, $title_tag, $layout ) {
		$show_image        = 'yes' === ( $settings['show_image']        ?? 'yes' );
		$show_icon         = 'yes' === ( $settings['show_icon']         ?? 'yes' );
		$show_category     = 'yes' === ( $settings['show_category']     ?? 'yes' );
		$show_date         = 'yes' === ( $settings['show_date']         ?? 'yes' );
		$show_author       = 'yes' === ( $settings['show_author']       ?? 'no' );
		$show_badge        = 'yes' === ( $settings['show_badge']        ?? 'yes' );
		$show_description  = 'yes' === ( $settings['show_description']  ?? 'yes' );
		$show_button       = 'yes' === ( $settings['show_button']       ?? 'yes' );
		$show_full_content = 'yes' === ( $settings['show_full_content'] ?? '' );
		$excerpt_length    = (int) ( $settings['excerpt_length'] ?? 0 );
		$meta_sep          = (string) ( $settings['meta_separator'] ?? '•' );
		$cta_icon          = (string) ( $settings['cta_icon'] ?? '→' );
		$image_size        = $settings['image_size_size'] ?? 'medium_large';

		$index = 0;
		foreach ( $items as $item ) {
			$index++;

			$card_class = [ 'goallord-an__card', 'goallord-an__card--' . $index ];

			if ( 'featured' === $layout && 1 === $index ) {
				$card_class[] = 'goallord-an__card--featured';
			}

			$image_id  = (int) ( $item['image_id']  ?? 0 );
			$image_url = (string) ( $item['image_url'] ?? '' );
			$has_image = $show_image && ( $image_id > 0 || '' !== $image_url );

			if ( ! $has_image ) {
				$card_class[] = 'goallord-an__card--no-image';
			}

			$link_url      = (string) ( $item['link_url']      ?? '' );
			$link_target   = (string) ( $item['link_target']   ?? '' );
			$link_nofollow = (string) ( $item['link_nofollow'] ?? '' );

			$title_text   = (string) ( $item['title']       ?? '' );
			$desc_text    = (string) ( $item['description'] ?? '' );
			if ( $excerpt_length > 0 ) {
				$desc_text = Helpers::trim_words( $desc_text, $excerpt_length );
			}
			$category    = (string) ( $item['category']     ?? '' );
			$date        = (string) ( $item['date']         ?? '' );
			$author      = (string) ( $item['author']       ?? '' );
			$extra_meta  = (string) ( $item['extra_meta']   ?? '' );
			$cta_text    = (string) ( $item['cta_text']     ?? __( 'Read More', 'goallord-addons' ) );
			$full_content= (string) ( $item['full_content'] ?? '' );

			$icon     = ( isset( $item['icon'] ) && is_array( $item['icon'] ) ) ? $item['icon'] : [];
			$has_icon = $show_icon && ! empty( $icon ) && ! empty( $icon['value'] );

			$is_featured_hero    = ( 'featured' === $layout && 1 === $index );
			$render_full_content = '' !== trim( wp_strip_all_tags( $full_content ) )
				&& ( $show_full_content || $is_featured_hero );

			$badge_on    = $show_badge && ! empty( $item['badge_show'] );
			$badge_text  = (string) ( $item['badge_text']  ?? '' );
			$badge_style = (string) ( $item['badge_style'] ?? 'default' );

			$style_delay = '';
			if ( 'yes' === ( $settings['enable_animations'] ?? 'yes' ) && 'yes' === ( $settings['animation_stagger'] ?? 'yes' ) ) {
				$style_delay = 'style="--goallord-an-index: ' . (int) ( $index - 1 ) . ';"';
			}
			?>
			<article class="<?php echo esc_attr( implode( ' ', $card_class ) ); ?>" role="listitem" <?php echo $style_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( $has_image ) : ?>
					<div class="goallord-an__image">
						<?php
						if ( ! empty( $link_url ) ) {
							printf(
								'<a href="%s" class="goallord-an__image-link" aria-label="%s"%s%s>',
								esc_url( $link_url ),
								esc_attr( $title_text ),
								$link_target ? ' target="' . esc_attr( $link_target ) . '"' : '',
								$link_nofollow ? ' rel="' . esc_attr( $link_nofollow ) . '"' : ''
							);
						}

						if ( $image_id > 0 ) {
							echo wp_get_attachment_image(
								$image_id,
								$image_size,
								false,
								[
									'class'   => 'goallord-an__image-tag',
									'loading' => 'lazy',
									'alt'     => esc_attr( $title_text ),
								]
							);
						} else {
							printf(
								'<img src="%s" alt="%s" class="goallord-an__image-tag" loading="lazy" />',
								esc_url( $image_url ),
								esc_attr( $title_text )
							);
						}

						if ( $badge_on && '' !== trim( $badge_text ) ) {
							printf(
								'<span class="goallord-an__badge goallord-an__badge--%s">%s</span>',
								esc_attr( $badge_style ),
								esc_html( $badge_text )
							);
						}

						if ( ! empty( $link_url ) ) {
							echo '</a>';
						}
						?>
					</div>
				<?php elseif ( $badge_on && '' !== trim( $badge_text ) ) : ?>
					<div class="goallord-an__badge-wrap">
						<span class="goallord-an__badge goallord-an__badge--<?php echo esc_attr( $badge_style ); ?>"><?php echo esc_html( $badge_text ); ?></span>
					</div>
				<?php endif; ?>

				<div class="goallord-an__body">
					<?php if ( $has_icon ) : ?>
						<div class="goallord-an__icon" aria-hidden="true">
							<?php \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
						</div>
					<?php endif; ?>

					<?php
					$has_any_meta = ( $show_category && '' !== trim( $category ) )
						|| ( $show_date && '' !== trim( $date ) )
						|| ( $show_author && '' !== trim( $author ) )
						|| ( '' !== trim( $extra_meta ) );
					?>
					<?php if ( $has_any_meta ) : ?>
						<div class="goallord-an__meta">
							<?php
							$meta_parts = [];
							if ( $show_category && '' !== trim( $category ) ) {
								$meta_parts[] = '<span class="goallord-an__category">' . esc_html( $category ) . '</span>';
							}
							if ( $show_date && '' !== trim( $date ) ) {
								$meta_parts[] = '<span class="goallord-an__date">' . esc_html( $date ) . '</span>';
							}
							if ( $show_author && '' !== trim( $author ) ) {
								$meta_parts[] = '<span class="goallord-an__author">' . esc_html( $author ) . '</span>';
							}
							if ( '' !== trim( $extra_meta ) ) {
								$meta_parts[] = '<span class="goallord-an__meta-extra">' . esc_html( $extra_meta ) . '</span>';
							}
							if ( ! empty( $meta_parts ) ) {
								$sep = '<span class="goallord-an__meta-sep" aria-hidden="true">' . esc_html( $meta_sep ) . '</span>';
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo implode( ' ' . $sep . ' ', $meta_parts );
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== trim( $title_text ) ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="goallord-an__card-title">
							<?php if ( ! empty( $link_url ) ) : ?>
								<a href="<?php echo esc_url( $link_url ); ?>"
									<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
									<?php echo $link_nofollow ? 'rel="' . esc_attr( $link_nofollow ) . '"' : ''; ?>>
									<?php echo esc_html( $title_text ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $title_text ); ?>
							<?php endif; ?>
						</<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( $show_description && '' !== trim( $desc_text ) ) : ?>
						<p class="goallord-an__description"><?php echo esc_html( $desc_text ); ?></p>
					<?php endif; ?>

					<?php if ( $render_full_content ) : ?>
						<div class="goallord-an__full-content">
							<?php
							// parse_text_editor runs wpautop + do_shortcode, matching Elementor's native Text Editor widget.
							echo $this->parse_text_editor( $full_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>
					<?php endif; ?>

					<?php if ( $show_button && '' !== trim( $cta_text ) ) : ?>
						<?php if ( ! empty( $link_url ) ) : ?>
							<a class="goallord-an__cta" href="<?php echo esc_url( $link_url ); ?>"
								<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
								<?php echo $link_nofollow ? 'rel="' . esc_attr( $link_nofollow ) . '"' : ''; ?>>
								<span class="goallord-an__cta-text"><?php echo esc_html( $cta_text ); ?></span>
								<span class="goallord-an__cta-icon" aria-hidden="true"><?php echo esc_html( $cta_icon ); ?></span>
							</a>
						<?php else : ?>
							<span class="goallord-an__cta goallord-an__cta--static">
								<span class="goallord-an__cta-text"><?php echo esc_html( $cta_text ); ?></span>
								<span class="goallord-an__cta-icon" aria-hidden="true"><?php echo esc_html( $cta_icon ); ?></span>
							</span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</article>
			<?php
		}
	}
}
