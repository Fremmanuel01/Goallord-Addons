<?php
/**
 * Daily Schedule (Daily Rhythm) Widget.
 *
 * Grouped schedule/timeline system with 5 layouts.
 * Uses a two-repeater structure (Groups + Items linked by group_key)
 * to simulate nested repeaters, since Elementor does not natively
 * support nested REPEATER controls.
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
use Goallord\Addons\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Daily_Schedule extends Widget_Base {

	public function get_name() {
		return 'goallord-daily-schedule';
	}

	public function get_title() {
		return esc_html__( 'Goallord Daily Schedule', 'goallord-addons' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'goallord-addons' ];
	}

	public function get_keywords() {
		return [ 'goallord', 'schedule', 'timeline', 'daily', 'rhythm', 'routine', 'hours', 'timetable' ];
	}

	public function get_style_depends() {
		return [ 'goallord-daily-schedule' ];
	}

	public function get_script_depends() {
		return [ 'goallord-daily-schedule' ];
	}

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

		/* ---------- Layout ---------- */
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
				'default' => 'classic',
				'options' => [
					'classic'  => esc_html__( 'Classic 3-Column Rhythm', 'goallord-addons' ),
					'timeline' => esc_html__( 'Vertical Timeline', 'goallord-addons' ),
					'compact'  => esc_html__( 'Compact List', 'goallord-addons' ),
					'cards'    => esc_html__( 'Card-Based', 'goallord-addons' ),
					'flow'     => esc_html__( 'Single Column Flow', 'goallord-addons' ),
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
					'{{WRAPPER}} .goallord-ds__groups' => '--goallord-ds-columns: {{VALUE}};',
				],
				'condition' => [ 'layout' => [ 'classic', 'cards' ] ],
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
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'header_eyebrow',
			[
				'label'     => esc_html__( 'Eyebrow', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'placeholder' => esc_html__( 'OUR DAILY RHYTHM', 'goallord-addons' ),
				'condition' => [ 'show_header' => 'yes' ],
				'dynamic'   => [ 'active' => true ],
			]
		);

		$this->add_control(
			'header_title',
			[
				'label'     => esc_html__( 'Title', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => esc_html__( 'The Daily Rhythm', 'goallord-addons' ),
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
				'default'   => esc_html__( 'A disciplined life ordered toward the glory of God. Every hour is an opportunity for sanctification.', 'goallord-addons' ),
				'condition' => [ 'show_header' => 'yes' ],
				'dynamic'   => [ 'active' => true ],
			]
		);

		$this->add_control(
			'header_title_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
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
					'{{WRAPPER}} .goallord-ds__header' => 'text-align: {{VALUE}};',
				],
				'condition' => [ 'show_header' => 'yes' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Groups (Level 1) ---------- */
		$this->start_controls_section(
			'section_groups',
			[
				'label'       => esc_html__( 'Schedule Groups', 'goallord-addons' ),
				'tab'         => Controls_Manager::TAB_CONTENT,
				'description' => esc_html__( 'Define each group (e.g. Morning Vigil, Academic Duty, Night Rest). Items are assigned to groups by matching Group Key below.', 'goallord-addons' ),
			]
		);

		$groups_rep = new Repeater();

		$groups_rep->add_control(
			'group_key',
			[
				'label'       => esc_html__( 'Group Key', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'morning',
				'description' => esc_html__( 'Short internal key — lowercase, no spaces. Items reference this.', 'goallord-addons' ),
			]
		);

		$groups_rep->add_control(
			'group_title',
			[
				'label'   => esc_html__( 'Title', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Morning Vigil', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$groups_rep->add_control(
			'group_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '05:00 — 08:00', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$groups_rep->add_control(
			'group_icon',
			[
				'label'   => esc_html__( 'Icon', 'goallord-addons' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-sun',
					'library' => 'fa-solid',
				],
			]
		);

		$groups_rep->add_control(
			'group_color',
			[
				'label'   => esc_html__( 'Accent Color (optional)', 'goallord-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '',
				'description' => esc_html__( 'Leave empty to inherit from style tab.', 'goallord-addons' ),
			]
		);

		$groups_rep->add_control(
			'group_show_divider',
			[
				'label'        => esc_html__( 'Show Divider Under Header', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'groups',
			[
				'label'       => esc_html__( 'Groups', 'goallord-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $groups_rep->get_controls(),
				'title_field' => '{{{ group_title }}}',
				'default'     => [
					[
						'group_key'       => 'morning',
						'group_title'     => esc_html__( 'Morning Vigil', 'goallord-addons' ),
						'group_subtitle'  => esc_html__( '05:00 — 08:00', 'goallord-addons' ),
						'group_icon'      => [ 'value' => 'fas fa-sun',  'library' => 'fa-solid' ],
						'group_show_divider' => 'yes',
					],
					[
						'group_key'       => 'academic',
						'group_title'     => esc_html__( 'Academic Duty', 'goallord-addons' ),
						'group_subtitle'  => esc_html__( '08:00 — 17:00', 'goallord-addons' ),
						'group_icon'      => [ 'value' => 'fas fa-book-open', 'library' => 'fa-solid' ],
						'group_show_divider' => 'yes',
					],
					[
						'group_key'       => 'night',
						'group_title'     => esc_html__( 'Night Rest', 'goallord-addons' ),
						'group_subtitle'  => esc_html__( '20:00 — 05:00', 'goallord-addons' ),
						'group_icon'      => [ 'value' => 'fas fa-moon', 'library' => 'fa-solid' ],
						'group_show_divider' => 'yes',
					],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Items (Level 2) ---------- */
		$this->start_controls_section(
			'section_items',
			[
				'label'       => esc_html__( 'Schedule Items', 'goallord-addons' ),
				'tab'         => Controls_Manager::TAB_CONTENT,
				'description' => esc_html__( 'Each item is assigned to a group via Group Key.', 'goallord-addons' ),
			]
		);

		$items_rep = new Repeater();

		$items_rep->add_control(
			'item_group_key',
			[
				'label'       => esc_html__( 'Group Key', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'morning',
				'description' => esc_html__( 'Must match a Group Key defined above.', 'goallord-addons' ),
			]
		);

		$items_rep->add_control(
			'item_time',
			[
				'label'   => esc_html__( 'Time', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '05:30',
				'dynamic' => [ 'active' => true ],
			]
		);

		$items_rep->add_control(
			'item_time_end',
			[
				'label'       => esc_html__( 'End Time (optional)', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '06:00',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$items_rep->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'Title', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Rising & Angelus', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$items_rep->add_control(
			'item_description',
			[
				'label'   => esc_html__( 'Description', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => '',
				'dynamic' => [ 'active' => true ],
			]
		);

		$items_rep->add_control(
			'item_icon',
			[
				'label'   => esc_html__( 'Icon (optional)', 'goallord-addons' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => '',
					'library' => '',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$items_rep->add_control(
			'item_status',
			[
				'label'       => esc_html__( 'Status / Badge (optional)', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Required • Optional • Silent', 'goallord-addons' ),
				'description' => esc_html__( 'Short status tag — e.g. "Silent", "Required".', 'goallord-addons' ),
			]
		);

		$items_rep->add_control(
			'item_highlight',
			[
				'label'        => esc_html__( 'Highlight This Item', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Adds a subtle glow/accent to draw the eye.', 'goallord-addons' ),
			]
		);

		$items_rep->add_control(
			'item_link',
			[
				'label'       => esc_html__( 'Link (optional)', 'goallord-addons' ),
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
				'label'       => esc_html__( 'Items', 'goallord-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items_rep->get_controls(),
				'title_field' => '{{{ item_time }}} &nbsp; {{{ item_title }}}',
				'default'     => [
					[ 'item_group_key' => 'morning',  'item_time' => '05:30', 'item_title' => esc_html__( 'Rising & Angelus',      'goallord-addons' ), 'item_description' => esc_html__( 'Silence is maintained in the corridors.',     'goallord-addons' ) ],
					[ 'item_group_key' => 'morning',  'item_time' => '06:00', 'item_title' => esc_html__( 'Lauds & Meditation',    'goallord-addons' ), 'item_description' => esc_html__( 'Morning prayer in the main chapel.',          'goallord-addons' ) ],
					[ 'item_group_key' => 'morning',  'item_time' => '07:00', 'item_title' => esc_html__( 'Holy Mass',             'goallord-addons' ), 'item_description' => esc_html__( 'The source and summit of our day.',           'goallord-addons' ) ],
					[ 'item_group_key' => 'academic', 'item_time' => '08:30', 'item_title' => esc_html__( 'Lecture Hours',         'goallord-addons' ), 'item_description' => esc_html__( 'Intellectual engagement in the Aula Magna.',  'goallord-addons' ) ],
					[ 'item_group_key' => 'academic', 'item_time' => '13:00', 'item_title' => esc_html__( 'Midday Prayer & Lunch', 'goallord-addons' ), 'item_description' => esc_html__( 'Common meal with spiritual reading.',          'goallord-addons' ) ],
					[ 'item_group_key' => 'academic', 'item_time' => '15:00', 'item_title' => esc_html__( 'Study & Work',          'goallord-addons' ), 'item_description' => esc_html__( 'Library research or manual labor.',            'goallord-addons' ) ],
					[ 'item_group_key' => 'night',    'item_time' => '18:30', 'item_title' => esc_html__( 'Vespers & Rosary',      'goallord-addons' ), 'item_description' => esc_html__( 'Evening liturgical prayer.',                   'goallord-addons' ) ],
					[ 'item_group_key' => 'night',    'item_time' => '21:00', 'item_title' => esc_html__( 'Compline',              'goallord-addons' ), 'item_description' => esc_html__( 'Concluding the day with trust in God.',       'goallord-addons' ) ],
					[ 'item_group_key' => 'night',    'item_time' => '22:00', 'item_title' => esc_html__( 'Great Silence',         'goallord-addons' ), 'item_description' => esc_html__( 'Preparation for rest and reflection.',        'goallord-addons' ) ],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Display Options ---------- */
		$this->start_controls_section(
			'section_display',
			[
				'label' => esc_html__( 'Display Options', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_group_icon',
			[
				'label'        => esc_html__( 'Show Group Icon', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_group_subtitle',
			[
				'label'        => esc_html__( 'Show Group Subtitle', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'Hidden by default to match the reference editorial look. Enable if you want a range like "05:00 — 08:00" next to the group title.', 'goallord-addons' ),
			]
		);

		$this->add_control(
			'show_item_icon',
			[
				'label'        => esc_html__( 'Show Item Icons', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_item_description',
			[
				'label'        => esc_html__( 'Show Item Descriptions', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_item_status',
			[
				'label'        => esc_html__( 'Show Item Status', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_timeline_rail',
			[
				'label'        => esc_html__( 'Show Timeline Rail', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [ 'layout' => 'timeline' ],
			]
		);

		$this->add_control(
			'item_title_tag',
			[
				'label'   => esc_html__( 'Item Title HTML Tag', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'div', 'span' => 'span',
				],
			]
		);

		$this->add_control(
			'group_title_tag',
			[
				'label'   => esc_html__( 'Group Title HTML Tag', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'div',
				],
			]
		);

		$this->add_control(
			'time_separator',
			[
				'label'       => esc_html__( 'Time Range Separator', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '—',
				'description' => esc_html__( 'Used when an item has both start and end time.', 'goallord-addons' ),
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * LAYOUT / SETTINGS
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
			'group_gap',
			[
				'label'      => esc_html__( 'Gap Between Groups', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 160, 'step' => 1 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 48 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ds__groups' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'      => esc_html__( 'Gap Between Items', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80, 'step' => 1 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 28 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ds__items' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_bottom_spacing',
			[
				'label'      => esc_html__( 'Header Bottom Spacing', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 64 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ds__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'group_alignment',
			[
				'label'     => esc_html__( 'Group Alignment', 'goallord-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'goallord-addons' ),   'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'goallord-addons' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'goallord-addons' ),  'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .goallord-ds__group' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * ANIMATION TAB
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
					'{{WRAPPER}}' => '--goallord-ds-anim-duration: {{VALUE}}ms;',
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
					'{{WRAPPER}}' => '--goallord-ds-anim-delay: {{VALUE}}ms;',
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
				'default'   => 100,
				'min'       => 20,
				'max'       => 1000,
				'step'      => 10,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ds-stagger: {{VALUE}}ms;',
				],
				'condition' => [
					'enable_animations' => 'yes',
					'animation_stagger' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_hover_highlight',
			[
				'label'        => esc_html__( 'Hover Highlight on Items', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'goallord-ds-hover--',
			]
		);

		$this->add_control(
			'enable_timeline_grow',
			[
				'label'        => esc_html__( 'Timeline Rail Grow Animation', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [ 'layout' => 'timeline' ],
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
				'selector' => '{{WRAPPER}} .goallord-ds',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => '#0f1434' ],
				],
			]
		);

		$this->add_control(
			'wrapper_overlay_color',
			[
				'label'       => esc_html__( 'Overlay Color', 'goallord-addons' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'description' => esc_html__( 'Optional flat color over the background. Useful with a background image/silhouette.', 'goallord-addons' ),
				'selectors'   => [
					'{{WRAPPER}} .goallord-ds__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wrapper_decor_image',
			[
				'label'       => esc_html__( 'Decorative Background Image', 'goallord-addons' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [ 'url' => '' ],
				'description' => esc_html__( 'Optional silhouette/illustration laid subtly behind the groups.', 'goallord-addons' ),
				'selectors'   => [
					'{{WRAPPER}} .goallord-ds__decor' => 'background-image: url("{{URL}}");',
				],
			]
		);

		$this->add_control(
			'wrapper_decor_opacity',
			[
				'label'     => esc_html__( 'Decorative Opacity', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 0.12 ],
				'selectors' => [
					'{{WRAPPER}} .goallord-ds__decor' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [ 'top' => 96, 'right' => 32, 'bottom' => 96, 'left' => 32, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ds' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control( 'eyebrow_heading', [ 'label' => esc_html__( 'Eyebrow', 'goallord-addons' ), 'type' => Controls_Manager::HEADING ] );

		$this->add_control(
			'eyebrow_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__eyebrow' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__eyebrow',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 12 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 3 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_control( 'title_heading', [ 'label' => esc_html__( 'Title', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_family' => [ 'default' => 'Playfair Display' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 52 ] ],
					'font_weight' => [ 'default' => '700' ],
					'font_style'  => [ 'default' => 'normal' ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.15 ] ],
				],
			]
		);

		$this->add_control( 'subtitle_heading', [ 'label' => esc_html__( 'Subtitle', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(245, 238, 215, 0.72)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__subtitle',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 17 ] ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'subtitle_max_width',
			[
				'label'      => esc_html__( 'Subtitle Max Width', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 1000 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 640 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ds__subtitle' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Group Header ---------- */
		$this->start_controls_section(
			'section_style_group',
			[
				'label' => esc_html__( 'Group Header', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'group_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [
					'{{WRAPPER}} .goallord-ds__group-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .goallord-ds__group-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'group_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 28 ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__group-icon' => 'font-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'group_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Bottom Spacing', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__group-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'group_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5eed7',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__group-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'group_title_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__group-title',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 22 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 1.2 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_control(
			'group_subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(245, 238, 215, 0.55)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__group-subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'group_subtitle_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__group-subtitle',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 13 ] ],
					'font_weight' => [ 'default' => '400' ],
				],
			]
		);

		$this->add_control(
			'group_divider_color',
			[
				'label'     => esc_html__( 'Divider Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(232, 170, 60, 0.35)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__group-divider' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'group_header_spacing',
			[
				'label'      => esc_html__( 'Spacing Below Header', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 28 ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__group-header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Item Time ---------- */
		$this->start_controls_section(
			'section_style_item_time',
			[
				'label' => esc_html__( 'Item — Time', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_time_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item-time' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_time_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__item-time',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 0.8 ] ],
				],
			]
		);

		$this->add_responsive_control(
			'item_time_min_width',
			[
				'label'      => esc_html__( 'Time Column Min-Width', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 72 ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__item-time' => 'min-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Item Title + Description ---------- */
		$this->start_controls_section(
			'section_style_item_text',
			[
				'label' => esc_html__( 'Item — Title & Description', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'item_text_tabs' );

		$this->start_controls_tab( 'item_text_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_control(
			'item_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5eed7',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item-title, {{WRAPPER}} .goallord-ds__item-title a' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'item_text_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_control(
			'item_title_color_hover',
			[
				'label'     => esc_html__( 'Title Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item:hover .goallord-ds__item-title, {{WRAPPER}} .goallord-ds__item:hover .goallord-ds__item-title a' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_title_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__item-title',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 16 ] ],
					'font_weight' => [ 'default' => '600' ],
				],
			]
		);

		$this->add_control(
			'item_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(245, 238, 215, 0.65)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item-desc' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_desc_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__item-desc',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Item Icon & Status ---------- */
		$this->start_controls_section(
			'section_style_item_icon_status',
			[
				'label' => esc_html__( 'Item — Icon & Status', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control( 'item_icon_heading', [ 'label' => esc_html__( 'Item Icon', 'goallord-addons' ), 'type' => Controls_Manager::HEADING ] );

		$this->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [
					'{{WRAPPER}} .goallord-ds__item-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .goallord-ds__item-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_icon_size',
			[
				'label'      => esc_html__( 'Size', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 15 ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__item-icon' => 'font-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control( 'item_status_heading', [ 'label' => esc_html__( 'Item Status', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'item_status_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(232, 170, 60, 0.9)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item-status' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'item_status_bg',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(232, 170, 60, 0.12)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item-status' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_status_typography',
				'selector' => '{{WRAPPER}} .goallord-ds__item-status',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 10 ] ],
					'font_weight'   => [ 'default' => '700' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 1.2 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Timeline Rail ---------- */
		$this->start_controls_section(
			'section_style_timeline',
			[
				'label'     => esc_html__( 'Timeline Rail', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout'              => 'timeline',
					'show_timeline_rail'  => 'yes',
				],
			]
		);

		$this->add_control(
			'timeline_rail_color',
			[
				'label'     => esc_html__( 'Rail Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(232, 170, 60, 0.35)',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__timeline-rail' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'timeline_dot_color',
			[
				'label'     => esc_html__( 'Dot Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__timeline-dot' => 'background-color: {{VALUE}}; box-shadow: 0 0 0 4px rgba(232, 170, 60, 0.15);' ],
			]
		);

		$this->add_control(
			'timeline_rail_width',
			[
				'label'     => esc_html__( 'Rail Thickness', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px' ],
				'range'     => [ 'px' => [ 'min' => 1, 'max' => 8 ] ],
				'default'   => [ 'unit' => 'px', 'size' => 2 ],
				'selectors' => [ '{{WRAPPER}} .goallord-ds__timeline-rail' => 'width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'timeline_dot_size',
			[
				'label'     => esc_html__( 'Dot Size', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px' ],
				'range'     => [ 'px' => [ 'min' => 4, 'max' => 24 ] ],
				'default'   => [ 'unit' => 'px', 'size' => 10 ],
				'selectors' => [ '{{WRAPPER}} .goallord-ds__timeline-dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Item Wrapper / Hover ---------- */
		$this->start_controls_section(
			'section_style_item_wrap',
			[
				'label' => esc_html__( 'Item — Container & Hover', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_hover_bg',
			[
				'label'     => esc_html__( 'Hover Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(232, 170, 60, 0.06)',
				'selectors' => [ '{{WRAPPER}}.goallord-ds-hover--yes .goallord-ds__item:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'item_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Accent', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ds__item--highlight' => 'border-left-color: {{VALUE}}; box-shadow: inset 3px 0 0 {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 12, 'right' => 16, 'bottom' => 12, 'left' => 16, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Cards layout ---------- */
		$this->start_controls_section(
			'section_style_card',
			[
				'label'     => esc_html__( 'Card (Card Layout)', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'layout' => 'cards' ],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .goallord-ds--layout-cards .goallord-ds__group',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => 'rgba(255, 255, 255, 0.04)' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .goallord-ds--layout-cards .goallord-ds__group',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'isLinked' => true, 'unit' => 'px' ] ],
					'color'  => [ 'default' => 'rgba(245, 238, 215, 0.08)' ],
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [ 'top' => 32, 'right' => 28, 'bottom' => 32, 'left' => 28, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds--layout-cards .goallord-ds__group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [ 'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .goallord-ds--layout-cards .goallord-ds__group' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * RENDER
	 * ========================================================= */

	protected function render() {
		$settings = $this->get_settings_for_display();

		$groups_raw = isset( $settings['groups'] ) && is_array( $settings['groups'] ) ? $settings['groups'] : [];
		$items_raw  = isset( $settings['items'] )  && is_array( $settings['items'] )  ? $settings['items']  : [];

		if ( empty( $groups_raw ) && empty( $items_raw ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="goallord-ds goallord-ds--empty">'
					. esc_html__( 'Add Schedule Groups and Items to see them here.', 'goallord-addons' )
					. '</div>';
			}
			return;
		}

		$layout = in_array( $settings['layout'] ?? 'classic', [ 'classic', 'timeline', 'compact', 'cards', 'flow' ], true )
			? $settings['layout']
			: 'classic';

		$group_title_tag = Helpers::safe_tag( $settings['group_title_tag'] ?? 'h3', 'h3' );
		$item_title_tag  = Helpers::safe_tag( $settings['item_title_tag']  ?? 'h4', 'h4' );
		$header_tag      = Helpers::safe_tag( $settings['header_title_tag']?? 'h2', 'h2' );

		$grouped = $this->assemble_groups( $groups_raw, $items_raw );

		$this->add_render_attribute( 'wrapper', 'class', [
			'goallord-ds',
			'goallord-ds--layout-' . $layout,
		] );

		if ( 'yes' === ( $settings['enable_animations'] ?? 'yes' ) ) {
			$this->add_render_attribute( 'wrapper', 'data-goallord-ds-animate', '1' );
			$this->add_render_attribute( 'wrapper', 'data-animation', esc_attr( $settings['animation_style'] ?? 'fade-up' ) );
			$this->add_render_attribute( 'wrapper', 'data-stagger', ( 'yes' === ( $settings['animation_stagger'] ?? 'yes' ) ) ? '1' : '0' );
		}
		if ( 'timeline' === $layout && 'yes' === ( $settings['enable_timeline_grow'] ?? 'yes' ) ) {
			$this->add_render_attribute( 'wrapper', 'data-timeline-grow', '1' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="goallord-ds__decor" aria-hidden="true"></div>
			<div class="goallord-ds__overlay" aria-hidden="true"></div>

			<div class="goallord-ds__inner">
				<?php $this->render_header( $settings, $header_tag ); ?>

				<?php if ( 'timeline' === $layout && 'yes' === ( $settings['show_timeline_rail'] ?? 'yes' ) ) : ?>
					<div class="goallord-ds__timeline-rail" aria-hidden="true"></div>
				<?php endif; ?>

				<div class="goallord-ds__groups">
					<?php $this->render_groups( $grouped, $settings, $group_title_tag, $item_title_tag, $layout ); ?>
				</div>
			</div>
		</div>
		<?php
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
		<div class="goallord-ds__header">
			<?php if ( '' !== $eyebrow ) : ?>
				<div class="goallord-ds__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<<?php echo esc_html( $header_tag ); ?> class="goallord-ds__title">
					<?php echo wp_kses( nl2br( $title ), Helpers::kses_inline() ); ?>
				</<?php echo esc_html( $header_tag ); ?>>
			<?php endif; ?>
			<?php if ( '' !== $subtitle ) : ?>
				<p class="goallord-ds__subtitle"><?php echo wp_kses( nl2br( $subtitle ), Helpers::kses_inline() ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Merges groups + items into ordered group blocks. Items whose group_key
	 * doesn't match any defined group are placed in a synthetic trailing group
	 * so they're never silently dropped.
	 */
	private function assemble_groups( $groups_raw, $items_raw ) {
		$groups_by_key = [];
		$ordered_keys  = [];

		foreach ( $groups_raw as $g ) {
			$key = self::sanitize_key( $g['group_key'] ?? '' );
			if ( '' === $key ) {
				continue;
			}
			if ( isset( $groups_by_key[ $key ] ) ) {
				continue;
			}
			$groups_by_key[ $key ] = [
				'key'          => $key,
				'title'        => (string) ( $g['group_title']    ?? '' ),
				'subtitle'     => (string) ( $g['group_subtitle'] ?? '' ),
				'icon'         => ( isset( $g['group_icon'] ) && is_array( $g['group_icon'] ) ) ? $g['group_icon'] : [],
				'color'        => (string) ( $g['group_color']    ?? '' ),
				'show_divider' => 'yes' === ( $g['group_show_divider'] ?? '' ),
				'items'        => [],
			];
			$ordered_keys[] = $key;
		}

		$orphan_key = '__orphan__';
		foreach ( $items_raw as $i ) {
			$target = self::sanitize_key( $i['item_group_key'] ?? '' );
			if ( ! isset( $groups_by_key[ $target ] ) ) {
				if ( ! isset( $groups_by_key[ $orphan_key ] ) ) {
					$groups_by_key[ $orphan_key ] = [
						'key'          => $orphan_key,
						'title'        => esc_html__( 'Other', 'goallord-addons' ),
						'subtitle'     => '',
						'icon'         => [],
						'color'        => '',
						'show_divider' => false,
						'items'        => [],
					];
					$ordered_keys[] = $orphan_key;
				}
				$target = $orphan_key;
			}
			$groups_by_key[ $target ]['items'][] = $i;
		}

		$out = [];
		foreach ( $ordered_keys as $k ) {
			if ( ! empty( $groups_by_key[ $k ]['items'] ) || $orphan_key !== $k ) {
				$out[] = $groups_by_key[ $k ];
			}
		}
		return $out;
	}

	private static function sanitize_key( $raw ) {
		$k = strtolower( trim( (string) $raw ) );
		$k = preg_replace( '/[^a-z0-9_\-]/', '-', $k );
		return (string) $k;
	}

	private function render_groups( $grouped, $settings, $group_title_tag, $item_title_tag, $layout ) {
		$show_group_icon    = 'yes' === ( $settings['show_group_icon']    ?? 'yes' );
		$show_group_subtitle= 'yes' === ( $settings['show_group_subtitle']?? 'yes' );
		$show_item_icon     = 'yes' === ( $settings['show_item_icon']     ?? 'yes' );
		$show_item_desc     = 'yes' === ( $settings['show_item_description'] ?? 'yes' );
		$show_item_status   = 'yes' === ( $settings['show_item_status']   ?? 'yes' );
		$time_sep           = (string) ( $settings['time_separator'] ?? '—' );

		$group_index = 0;
		$global_item_index = 0;

		foreach ( $grouped as $group ) {
			$group_index++;
			$group_accent = '' !== $group['color'] ? esc_attr( $group['color'] ) : '';

			$style_attr = '';
			if ( '' !== $group_accent ) {
				$style_attr = ' style="--goallord-ds-group-accent: ' . $group_accent . '; --goallord-ds-index: ' . (int) ( $group_index - 1 ) . ';"';
			} else {
				$style_attr = ' style="--goallord-ds-index: ' . (int) ( $group_index - 1 ) . ';"';
			}
			?>
			<div class="goallord-ds__group goallord-ds__group--<?php echo esc_attr( $group['key'] ); ?>"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
				$has_group_icon     = $show_group_icon && ! empty( $group['icon']['value'] );
				$has_group_title    = '' !== trim( $group['title'] );
				$has_group_subtitle = $show_group_subtitle && '' !== trim( $group['subtitle'] );
				$has_group_header   = $has_group_icon || $has_group_title || $has_group_subtitle || ! empty( $group['show_divider'] );
				?>
				<?php if ( $has_group_header ) : ?>
					<div class="goallord-ds__group-header">
						<?php if ( $has_group_icon ) : ?>
							<div class="goallord-ds__group-icon" aria-hidden="true">
								<?php \Elementor\Icons_Manager::render_icon( $group['icon'], [ 'aria-hidden' => 'true' ] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $has_group_title ) : ?>
							<<?php echo esc_html( $group_title_tag ); ?> class="goallord-ds__group-title">
								<?php echo esc_html( $group['title'] ); ?>
							</<?php echo esc_html( $group_title_tag ); ?>>
						<?php endif; ?>

						<?php if ( $has_group_subtitle ) : ?>
							<div class="goallord-ds__group-subtitle"><?php echo esc_html( $group['subtitle'] ); ?></div>
						<?php endif; ?>

						<?php if ( ! empty( $group['show_divider'] ) ) : ?>
							<span class="goallord-ds__group-divider" aria-hidden="true"></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $group['items'] ) ) : ?>
					<ul class="goallord-ds__items" role="list">
						<?php foreach ( $group['items'] as $item ) :
							$global_item_index++;

							$time     = (string) ( $item['item_time']     ?? '' );
							$time_end = (string) ( $item['item_time_end'] ?? '' );
							$title    = (string) ( $item['item_title']    ?? '' );
							$desc     = (string) ( $item['item_description'] ?? '' );
							$status   = (string) ( $item['item_status']   ?? '' );
							$highlight= 'yes' === ( $item['item_highlight'] ?? '' );
							$icon     = ( isset( $item['item_icon'] ) && is_array( $item['item_icon'] ) ) ? $item['item_icon'] : [];
							$has_icon = $show_item_icon && ! empty( $icon['value'] );

							$link_url      = isset( $item['item_link']['url'] ) ? (string) $item['item_link']['url'] : '';
							$link_target   = ! empty( $item['item_link']['is_external'] ) ? '_blank' : '';
							$link_nofollow = ! empty( $item['item_link']['nofollow'] )    ? 'nofollow' : '';

							$time_display = '';
							if ( '' !== trim( $time ) && '' !== trim( $time_end ) ) {
								$time_display = $time . ' ' . $time_sep . ' ' . $time_end;
							} elseif ( '' !== trim( $time ) ) {
								$time_display = $time;
							} elseif ( '' !== trim( $time_end ) ) {
								$time_display = $time_end;
							}

							$li_class = [ 'goallord-ds__item' ];
							if ( $highlight ) {
								$li_class[] = 'goallord-ds__item--highlight';
							}

							$item_style = ' style="--goallord-ds-item-index: ' . (int) ( $global_item_index - 1 ) . ';"';
							?>
							<li class="<?php echo esc_attr( implode( ' ', $li_class ) ); ?>"<?php echo $item_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( 'timeline' === $layout ) : ?>
									<span class="goallord-ds__timeline-dot" aria-hidden="true"></span>
								<?php endif; ?>

								<?php if ( '' !== $time_display ) : ?>
									<span class="goallord-ds__item-time"><?php echo esc_html( $time_display ); ?></span>
								<?php endif; ?>

								<div class="goallord-ds__item-content">
									<div class="goallord-ds__item-head">
										<?php if ( $has_icon ) : ?>
											<span class="goallord-ds__item-icon" aria-hidden="true">
												<?php \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
											</span>
										<?php endif; ?>

										<?php if ( '' !== trim( $title ) ) : ?>
											<<?php echo esc_html( $item_title_tag ); ?> class="goallord-ds__item-title">
												<?php if ( '' !== $link_url ) : ?>
													<a href="<?php echo esc_url( $link_url ); ?>"
														<?php echo $link_target   ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
														<?php echo $link_nofollow ? 'rel="' . esc_attr( $link_nofollow ) . '"' : ''; ?>>
														<?php echo esc_html( $title ); ?>
													</a>
												<?php else : ?>
													<?php echo esc_html( $title ); ?>
												<?php endif; ?>
											</<?php echo esc_html( $item_title_tag ); ?>>
										<?php endif; ?>

										<?php if ( $show_item_status && '' !== trim( $status ) ) : ?>
											<span class="goallord-ds__item-status"><?php echo esc_html( $status ); ?></span>
										<?php endif; ?>
									</div>

									<?php if ( $show_item_desc && '' !== trim( $desc ) ) : ?>
										<p class="goallord-ds__item-desc"><?php echo esc_html( $desc ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php
		}
	}
}
