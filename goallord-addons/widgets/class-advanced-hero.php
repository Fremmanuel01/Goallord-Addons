<?php
/**
 * Advanced Hero Section widget.
 *
 * Single hero or multi-slide carousel. Each slide is a self-contained
 * layout block with badge, headline (with highlight span), subheading,
 * description, two CTAs, optional media, and a fully independent
 * background system (color / gradient / image / self-hosted video).
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

class Advanced_Hero extends Widget_Base {

	public function get_name() {
		return 'goallord-advanced-hero';
	}

	public function get_title() {
		return esc_html__( 'Goallord Advanced Hero', 'goallord-addons' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return [ 'goallord-addons' ];
	}

	public function get_keywords() {
		return [ 'goallord', 'hero', 'banner', 'slider', 'carousel', 'landing', 'cta', 'showcase' ];
	}

	public function get_style_depends() {
		return [ 'goallord-advanced-hero' ];
	}

	public function get_script_depends() {
		return [ 'goallord-advanced-hero' ];
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_settings_controls();
		$this->register_style_controls();
	}

	/* =========================================================
	 * CONTENT TAB
	 * ========================================================= */

	private function register_content_controls() {

		/* ---------- Mode ---------- */
		$this->start_controls_section(
			'section_mode',
			[
				'label' => esc_html__( 'Mode', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'mode',
			[
				'label'   => esc_html__( 'Hero Mode', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => [
					'single' => esc_html__( 'Single Hero', 'goallord-addons' ),
					'slider' => esc_html__( 'Slider (multiple slides)', 'goallord-addons' ),
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Slides Repeater ---------- */
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$rep = new Repeater();

		$rep->start_controls_tabs( 'slide_tabs' );

		/* -- Slide: Layout tab -- */
		$rep->start_controls_tab( 'slide_tab_layout', [ 'label' => esc_html__( 'Layout', 'goallord-addons' ) ] );

		$rep->add_control(
			'slide_layout',
			[
				'label'   => esc_html__( 'Slide Layout', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'centered',
				'options' => [
					'centered'    => esc_html__( 'Centered', 'goallord-addons' ),
					'split-left'  => esc_html__( 'Split (text left · media right)', 'goallord-addons' ),
					'split-right' => esc_html__( 'Split (media left · text right)', 'goallord-addons' ),
					'fullbg'      => esc_html__( 'Full Background Immersive', 'goallord-addons' ),
					'minimal'     => esc_html__( 'Minimal', 'goallord-addons' ),
				],
			]
		);

		$rep->add_control(
			'slide_content_align',
			[
				'label'   => esc_html__( 'Content Alignment', 'goallord-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [ 'title' => esc_html__( 'Left', 'goallord-addons' ),   'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'goallord-addons' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'goallord-addons' ),  'icon' => 'eicon-text-align-right' ],
				],
				'default' => 'center',
			]
		);

		$rep->end_controls_tab();

		/* -- Slide: Content tab -- */
		$rep->start_controls_tab( 'slide_tab_content', [ 'label' => esc_html__( 'Content', 'goallord-addons' ) ] );

		$rep->add_control(
			'slide_badge_show',
			[
				'label'        => esc_html__( 'Show Badge', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$rep->add_control(
			'slide_badge_text',
			[
				'label'     => esc_html__( 'Badge Text', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'NEW RELEASE', 'goallord-addons' ),
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_badge_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_badge_icon',
			[
				'label'     => esc_html__( 'Badge Icon (optional)', 'goallord-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [ 'value' => '', 'library' => '' ],
				'skin'      => 'inline',
				'label_block' => false,
				'condition' => [ 'slide_badge_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_headline',
			[
				'label'       => esc_html__( 'Headline', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => esc_html__( 'Build something people remember.', 'goallord-addons' ),
				'description' => esc_html__( 'Wrap any phrase in {{ }} to style it as the accent highlight. Example: "Build {{something}} great."', 'goallord-addons' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$rep->add_control(
			'slide_subheading',
			[
				'label'   => esc_html__( 'Subheading', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'The landing experience, rebuilt', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$rep->add_control(
			'slide_description',
			[
				'label'   => esc_html__( 'Description', 'goallord-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'A modern hero system for teams that care about pace, polish, and conversion. No bloat, no compromise.', 'goallord-addons' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$rep->add_control(
			'slide_trust_text',
			[
				'label'       => esc_html__( 'Trust / Micro Text', 'goallord-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Loved by 2,400+ creators · 4.9 · No credit card', 'goallord-addons' ),
				'description' => esc_html__( 'Small secondary line under the CTAs.', 'goallord-addons' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$rep->end_controls_tab();

		/* -- Slide: CTAs tab -- */
		$rep->start_controls_tab( 'slide_tab_ctas', [ 'label' => esc_html__( 'CTAs', 'goallord-addons' ) ] );

		$rep->add_control(
			'slide_cta1_show',
			[
				'label'        => esc_html__( 'Show Primary CTA', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$rep->add_control(
			'slide_cta1_text',
			[
				'label'     => esc_html__( 'Primary Text', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Get Started', 'goallord-addons' ),
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_cta1_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_cta1_icon',
			[
				'label'     => esc_html__( 'Primary Icon', 'goallord-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ],
				'skin'      => 'inline',
				'label_block' => false,
				'condition' => [ 'slide_cta1_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_cta1_link',
			[
				'label'     => esc_html__( 'Primary Link', 'goallord-addons' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '', 'is_external' => false, 'nofollow' => false ],
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_cta1_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_cta2_show',
			[
				'label'        => esc_html__( 'Show Secondary CTA', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$rep->add_control(
			'slide_cta2_text',
			[
				'label'     => esc_html__( 'Secondary Text', 'goallord-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Watch demo', 'goallord-addons' ),
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_cta2_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_cta2_icon',
			[
				'label'     => esc_html__( 'Secondary Icon', 'goallord-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [ 'value' => 'fas fa-play', 'library' => 'fa-solid' ],
				'skin'      => 'inline',
				'label_block' => false,
				'condition' => [ 'slide_cta2_show' => 'yes' ],
			]
		);

		$rep->add_control(
			'slide_cta2_link',
			[
				'label'     => esc_html__( 'Secondary Link', 'goallord-addons' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '', 'is_external' => false, 'nofollow' => false ],
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_cta2_show' => 'yes' ],
			]
		);

		$rep->end_controls_tab();

		/* -- Slide: Media tab -- */
		$rep->start_controls_tab( 'slide_tab_media', [ 'label' => esc_html__( 'Media', 'goallord-addons' ) ] );

		$rep->add_control(
			'slide_media_type',
			[
				'label'   => esc_html__( 'Media Type', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'none'  => esc_html__( 'None', 'goallord-addons' ),
					'image' => esc_html__( 'Image', 'goallord-addons' ),
					'video' => esc_html__( 'Video (self-hosted)', 'goallord-addons' ),
				],
				'description' => esc_html__( 'Only shown in Split layouts. For Centered, Full-BG, or Minimal layouts, use the Background tab\'s image option instead.', 'goallord-addons' ),
			]
		);

		$rep->add_control(
			'slide_media_image',
			[
				'label'     => esc_html__( 'Image', 'goallord-addons' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => Utils::get_placeholder_image_src() ],
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_media_type' => 'image' ],
			]
		);

		$rep->add_control(
			'slide_media_video_url',
			[
				'label'       => esc_html__( 'Video URL (mp4/webm)', 'goallord-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://.../hero.mp4',
				'default'     => [ 'url' => '' ],
				'condition'   => [ 'slide_media_type' => 'video' ],
			]
		);

		$rep->add_control(
			'slide_media_video_poster',
			[
				'label'     => esc_html__( 'Video Poster Image', 'goallord-addons' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => '' ],
				'condition' => [ 'slide_media_type' => 'video' ],
			]
		);

		$rep->end_controls_tab();

		/* -- Slide: Background tab -- */
		$rep->start_controls_tab( 'slide_tab_bg', [ 'label' => esc_html__( 'Background', 'goallord-addons' ) ] );

		$rep->add_control(
			'slide_bg_type',
			[
				'label'   => esc_html__( 'Background Type', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'gradient',
				'options' => [
					'none'     => esc_html__( 'None', 'goallord-addons' ),
					'color'    => esc_html__( 'Solid Color', 'goallord-addons' ),
					'gradient' => esc_html__( 'Gradient', 'goallord-addons' ),
					'image'    => esc_html__( 'Image', 'goallord-addons' ),
					'video'    => esc_html__( 'Video', 'goallord-addons' ),
				],
			]
		);

		$rep->add_control(
			'slide_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f1434',
				'condition' => [ 'slide_bg_type' => 'color' ],
			]
		);

		$rep->add_control(
			'slide_bg_gradient_from',
			[
				'label'     => esc_html__( 'Gradient From', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f1434',
				'condition' => [ 'slide_bg_type' => 'gradient' ],
			]
		);

		$rep->add_control(
			'slide_bg_gradient_to',
			[
				'label'     => esc_html__( 'Gradient To', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e2a7a',
				'condition' => [ 'slide_bg_type' => 'gradient' ],
			]
		);

		$rep->add_control(
			'slide_bg_gradient_angle',
			[
				'label'     => esc_html__( 'Gradient Angle', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'deg' ],
				'range'     => [ 'deg' => [ 'min' => 0, 'max' => 360 ] ],
				'default'   => [ 'unit' => 'deg', 'size' => 135 ],
				'condition' => [ 'slide_bg_type' => 'gradient' ],
			]
		);

		$rep->add_control(
			'slide_bg_image',
			[
				'label'     => esc_html__( 'Background Image', 'goallord-addons' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => '' ],
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'slide_bg_type' => 'image' ],
			]
		);

		$rep->add_control(
			'slide_bg_video_url',
			[
				'label'       => esc_html__( 'Background Video URL (mp4)', 'goallord-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://.../bg.mp4',
				'default'     => [ 'url' => '' ],
				'condition'   => [ 'slide_bg_type' => 'video' ],
			]
		);

		$rep->add_control(
			'slide_bg_parallax',
			[
				'label'        => esc_html__( 'Subtle Parallax', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => [ 'slide_bg_type' => [ 'image' ] ],
			]
		);

		$rep->add_control(
			'slide_overlay_color',
			[
				'label'   => esc_html__( 'Overlay Color', 'goallord-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(15, 20, 52, 0.55)',
				'description' => esc_html__( 'Darken the background for legibility. Set to transparent to disable.', 'goallord-addons' ),
			]
		);

		$rep->end_controls_tab();

		$rep->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Slides', 'goallord-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ slide_headline || "Slide" }}}',
				'default'     => [
					[
						'slide_layout'        => 'centered',
						'slide_content_align' => 'center',
						'slide_badge_show'    => 'yes',
						'slide_badge_text'    => esc_html__( 'NEW · v1.0', 'goallord-addons' ),
						'slide_headline'      => esc_html__( 'Build something people {{remember}}.', 'goallord-addons' ),
						'slide_subheading'    => esc_html__( 'Premium hero sections, engineered for conversion.', 'goallord-addons' ),
						'slide_description'   => esc_html__( 'A modern hero system for teams that care about pace, polish, and performance. Swap layouts, tune motion, ship faster.', 'goallord-addons' ),
						'slide_trust_text'    => esc_html__( 'Loved by 2,400+ creators  ·  4.9  ·  No credit card', 'goallord-addons' ),
						'slide_cta1_show'     => 'yes',
						'slide_cta1_text'     => esc_html__( 'Get Started', 'goallord-addons' ),
						'slide_cta1_icon'     => [ 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ],
						'slide_cta2_show'     => 'yes',
						'slide_cta2_text'     => esc_html__( 'Watch demo', 'goallord-addons' ),
						'slide_cta2_icon'     => [ 'value' => 'fas fa-play', 'library' => 'fa-solid' ],
						'slide_media_type'    => 'none',
						'slide_bg_type'       => 'gradient',
						'slide_bg_gradient_from'  => '#0f1434',
						'slide_bg_gradient_to'    => '#1e2a7a',
						'slide_bg_gradient_angle' => [ 'unit' => 'deg', 'size' => 135 ],
						'slide_overlay_color' => 'rgba(15, 20, 52, 0.25)',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * SETTINGS TAB (still content, but ordered after slides)
	 * ========================================================= */

	private function register_settings_controls() {

		/* ---------- Height / Alignment ---------- */
		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Hero Settings', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'hero_height',
			[
				'label'      => esc_html__( 'Height', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'vh', 'px', 'rem' ],
				'range'      => [
					'vh' => [ 'min' => 30, 'max' => 100 ],
					'px' => [ 'min' => 200, 'max' => 1200 ],
				],
				'default'    => [ 'unit' => 'vh', 'size' => 85 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label'      => esc_html__( 'Content Max Width', 'goallord-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 400, 'max' => 1400 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 860 ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah__content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 60, 'right' => 32, 'bottom' => 60, 'left' => 32, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah__content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Slider Settings ---------- */
		$this->start_controls_section(
			'section_slider',
			[
				'label'     => esc_html__( 'Slider', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'mode' => 'slider' ],
			]
		);

		$this->add_control(
			'slider_transition',
			[
				'label'   => esc_html__( 'Transition', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__( 'Slide (horizontal)', 'goallord-addons' ),
					'fade'  => esc_html__( 'Fade', 'goallord-addons' ),
				],
			]
		);

		$this->add_control(
			'slider_transition_ms',
			[
				'label'   => esc_html__( 'Transition Speed (ms)', 'goallord-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 700,
				'min'     => 200,
				'max'     => 3000,
				'step'    => 50,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ah-transition: {{VALUE}}ms;',
				],
			]
		);

		$this->add_control(
			'slider_autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slider_autoplay_ms',
			[
				'label'     => esc_html__( 'Autoplay Interval (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6000,
				'min'       => 1500,
				'max'       => 20000,
				'step'      => 500,
				'condition' => [ 'slider_autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_pause_on_hover',
			[
				'label'        => esc_html__( 'Pause on Hover', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [ 'slider_autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label'        => esc_html__( 'Infinite Loop', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slider_show_arrows',
			[
				'label'        => esc_html__( 'Show Arrows', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slider_show_dots',
			[
				'label'        => esc_html__( 'Show Dots', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slider_swipe',
			[
				'label'        => esc_html__( 'Touch / Swipe', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slider_keyboard',
			[
				'label'        => esc_html__( 'Keyboard Arrows', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/* ---------- Animation ---------- */
		$this->start_controls_section(
			'section_animation',
			[
				'label' => esc_html__( 'Content Animation', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'anim_enable',
			[
				'label'        => esc_html__( 'Enable Entrance Animation', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'anim_style',
			[
				'label'   => esc_html__( 'Animation Style', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade-up',
				'options' => [
					'fade'        => esc_html__( 'Fade', 'goallord-addons' ),
					'fade-up'     => esc_html__( 'Fade Up', 'goallord-addons' ),
					'fade-down'   => esc_html__( 'Fade Down', 'goallord-addons' ),
					'slide-left'  => esc_html__( 'Slide In Left', 'goallord-addons' ),
					'slide-right' => esc_html__( 'Slide In Right', 'goallord-addons' ),
					'zoom'        => esc_html__( 'Zoom', 'goallord-addons' ),
				],
				'condition' => [ 'anim_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'anim_duration',
			[
				'label'     => esc_html__( 'Duration (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 800,
				'min'       => 200,
				'max'       => 3000,
				'step'      => 50,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ah-anim-duration: {{VALUE}}ms;',
				],
				'condition' => [ 'anim_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'anim_delay',
			[
				'label'     => esc_html__( 'Base Delay (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 3000,
				'step'      => 50,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ah-anim-delay: {{VALUE}}ms;',
				],
				'condition' => [ 'anim_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'anim_stagger',
			[
				'label'        => esc_html__( 'Stagger Elements', 'goallord-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [ 'anim_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'anim_stagger_ms',
			[
				'label'     => esc_html__( 'Stagger Step (ms)', 'goallord-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 120,
				'min'       => 20,
				'max'       => 600,
				'step'      => 10,
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ah-stagger: {{VALUE}}ms;',
				],
				'condition' => [
					'anim_enable'  => 'yes',
					'anim_stagger' => 'yes',
				],
			]
		);

		$this->add_control(
			'anim_easing',
			[
				'label'   => esc_html__( 'Easing', 'goallord-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'premium',
				'options' => [
					'premium'     => esc_html__( 'Premium (cubic-bezier)', 'goallord-addons' ),
					'ease-out'    => esc_html__( 'Ease Out', 'goallord-addons' ),
					'ease-in-out' => esc_html__( 'Ease In-Out', 'goallord-addons' ),
					'linear'      => esc_html__( 'Linear', 'goallord-addons' ),
				],
				'selectors_dictionary' => [
					'premium'     => 'cubic-bezier(0.22, 1, 0.36, 1)',
					'ease-out'    => 'ease-out',
					'ease-in-out' => 'ease-in-out',
					'linear'      => 'linear',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--goallord-ah-easing: {{VALUE}};',
				],
				'condition' => [ 'anim_enable' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * STYLE TAB
	 * ========================================================= */

	private function register_style_controls() {

		/* ---------- Content / Text ---------- */
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Text Styles', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control( 'headline_heading', [ 'label' => esc_html__( 'Headline', 'goallord-addons' ), 'type' => Controls_Manager::HEADING ] );

		$this->add_control(
			'headline_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__headline' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'headline_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__highlight' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .goallord-ah__headline',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 64 ] ],
					'font_weight' => [ 'default' => '800' ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.08 ] ],
				],
			]
		);

		$this->add_control( 'subheading_heading', [ 'label' => esc_html__( 'Subheading', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'subheading_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.8)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__subheading' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subheading_typography',
				'selector' => '{{WRAPPER}} .goallord-ah__subheading',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
					'font_weight'   => [ 'default' => '600' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 2.5 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->add_control( 'description_heading', [ 'label' => esc_html__( 'Description', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.72)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__description' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .goallord-ah__description',
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
					'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
				],
			]
		);

		$this->add_control( 'trust_heading', [ 'label' => esc_html__( 'Trust / Micro Text', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'trust_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.55)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__trust' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		/* ---------- Badge ---------- */
		$this->start_controls_section(
			'section_style_badge',
			[
				'label' => esc_html__( 'Badge', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_bg',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.12)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__badge' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__badge' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__badge' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .goallord-ah__badge',
				'fields_options' => [
					'typography'    => [ 'default' => 'yes' ],
					'font_size'     => [ 'default' => [ 'unit' => 'px', 'size' => 11 ] ],
					'font_weight'   => [ 'default' => '700' ],
					'letter_spacing'=> [ 'default' => [ 'unit' => 'px', 'size' => 1.5 ] ],
					'text_transform'=> [ 'default' => 'uppercase' ],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- CTAs ---------- */
		$this->register_cta_style( 'cta1', esc_html__( 'Primary CTA', 'goallord-addons' ), [
			'bg_default'          => '#e8aa3c',
			'color_default'       => '#0f1434',
			'bg_hover_default'    => '#ffffff',
			'color_hover_default' => '#0f1434',
		] );

		$this->register_cta_style( 'cta2', esc_html__( 'Secondary CTA', 'goallord-addons' ), [
			'bg_default'          => 'rgba(255,255,255,0.1)',
			'color_default'       => '#ffffff',
			'bg_hover_default'    => 'rgba(255,255,255,0.2)',
			'color_hover_default' => '#ffffff',
		] );

		/* ---------- Media ---------- */
		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__( 'Media', 'goallord-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'media_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'media_shadow',
				'selector' => '{{WRAPPER}} .goallord-ah__media',
				'fields_options' => [
					'box_shadow_type' => [ 'default' => 'yes' ],
					'box_shadow'      => [
						'default' => [
							'horizontal' => 0,
							'vertical'   => 30,
							'blur'       => 60,
							'spread'     => 0,
							'color'      => 'rgba(0, 0, 0, 0.3)',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		/* ---------- Navigation ---------- */
		$this->start_controls_section(
			'section_style_nav',
			[
				'label'     => esc_html__( 'Navigation', 'goallord-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'mode' => 'slider' ],
			]
		);

		$this->add_control( 'nav_arrows_heading', [ 'label' => esc_html__( 'Arrows', 'goallord-addons' ), 'type' => Controls_Manager::HEADING ] );

		$this->add_control(
			'nav_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__arrow' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nav_arrow_bg',
			[
				'label'     => esc_html__( 'Arrow Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.12)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__arrow' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nav_arrow_size',
			[
				'label'     => esc_html__( 'Arrow Size', 'goallord-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units'=> [ 'px' ],
				'range'     => [ 'px' => [ 'min' => 28, 'max' => 80 ] ],
				'default'   => [ 'unit' => 'px', 'size' => 48 ],
				'selectors' => [
					'{{WRAPPER}} .goallord-ah__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'nav_dots_heading', [ 'label' => esc_html__( 'Dots', 'goallord-addons' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

		$this->add_control(
			'nav_dot_color',
			[
				'label'     => esc_html__( 'Dot Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.35)',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__dot' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nav_dot_active_color',
			[
				'label'     => esc_html__( 'Active Dot Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8aa3c',
				'selectors' => [ '{{WRAPPER}} .goallord-ah__dot.is-active' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Helper to register a CTA's normal/hover style tab block.
	 */
	private function register_cta_style( $key, $label, $defaults ) {
		$this->start_controls_section(
			'section_style_' . $key,
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( $key . '_tabs' );

		$this->start_controls_tab( $key . '_tab_normal', [ 'label' => esc_html__( 'Normal', 'goallord-addons' ) ] );

		$this->add_control(
			$key . '_bg',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $defaults['bg_default'],
				'selectors' => [ '{{WRAPPER}} .goallord-ah__cta--' . esc_attr( $key ) => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			$key . '_color',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $defaults['color_default'],
				'selectors' => [ '{{WRAPPER}} .goallord-ah__cta--' . esc_attr( $key ) => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( $key . '_tab_hover', [ 'label' => esc_html__( 'Hover', 'goallord-addons' ) ] );

		$this->add_control(
			$key . '_bg_hover',
			[
				'label'     => esc_html__( 'Background', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $defaults['bg_hover_default'],
				'selectors' => [ '{{WRAPPER}} .goallord-ah__cta--' . esc_attr( $key ) . ':hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			$key . '_color_hover',
			[
				'label'     => esc_html__( 'Color', 'goallord-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $defaults['color_hover_default'],
				'selectors' => [ '{{WRAPPER}} .goallord-ah__cta--' . esc_attr( $key ) . ':hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $key . '_typography',
				'selector' => '{{WRAPPER}} .goallord-ah__cta--' . $key,
				'fields_options' => [
					'typography'  => [ 'default' => 'yes' ],
					'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 15 ] ],
					'font_weight' => [ 'default' => '600' ],
				],
			]
		);

		$this->add_responsive_control(
			$key . '_padding',
			[
				'label'      => esc_html__( 'Padding', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 14, 'right' => 28, 'bottom' => 14, 'left' => 28, 'isLinked' => false, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah__cta--' . $key => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$key . '_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'goallord-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'isLinked' => true, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .goallord-ah__cta--' . $key => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* =========================================================
	 * RENDER
	 * ========================================================= */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = isset( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : [];

		if ( empty( $slides ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="goallord-ah goallord-ah--empty">'
					. esc_html__( 'Add a slide to see the hero.', 'goallord-addons' )
					. '</div>';
			}
			return;
		}

		$mode            = ( 'slider' === ( $settings['mode'] ?? 'single' ) && count( $slides ) > 1 ) ? 'slider' : 'single';
		$transition      = in_array( $settings['slider_transition'] ?? 'slide', [ 'slide', 'fade' ], true ) ? $settings['slider_transition'] : 'slide';
		$show_arrows     = 'yes' === ( $settings['slider_show_arrows'] ?? 'yes' );
		$show_dots       = 'yes' === ( $settings['slider_show_dots']   ?? 'yes' );

		$this->add_render_attribute( 'wrapper', 'class', [
			'goallord-ah',
			'goallord-ah--mode-' . $mode,
			'goallord-ah--transition-' . $transition,
		] );

		if ( 'yes' === ( $settings['anim_enable'] ?? 'yes' ) ) {
			$this->add_render_attribute( 'wrapper', 'data-goallord-ah-animate', '1' );
			$this->add_render_attribute( 'wrapper', 'data-animation', esc_attr( $settings['anim_style'] ?? 'fade-up' ) );
			$this->add_render_attribute( 'wrapper', 'data-stagger', ( 'yes' === ( $settings['anim_stagger'] ?? 'yes' ) ) ? '1' : '0' );
		}

		if ( 'slider' === $mode ) {
			$this->add_render_attribute( 'wrapper', 'data-slider', '1' );
			$this->add_render_attribute( 'wrapper', 'data-autoplay',   ( 'yes' === ( $settings['slider_autoplay']       ?? 'yes' ) ) ? '1' : '0' );
			$this->add_render_attribute( 'wrapper', 'data-autoplay-ms', (int) ( $settings['slider_autoplay_ms']          ?? 6000 ) );
			$this->add_render_attribute( 'wrapper', 'data-pause-hover', ( 'yes' === ( $settings['slider_pause_on_hover']?? 'yes' ) ) ? '1' : '0' );
			$this->add_render_attribute( 'wrapper', 'data-loop',       ( 'yes' === ( $settings['slider_loop']          ?? 'yes' ) ) ? '1' : '0' );
			$this->add_render_attribute( 'wrapper', 'data-swipe',      ( 'yes' === ( $settings['slider_swipe']         ?? 'yes' ) ) ? '1' : '0' );
			$this->add_render_attribute( 'wrapper', 'data-keyboard',   ( 'yes' === ( $settings['slider_keyboard']      ?? 'yes' ) ) ? '1' : '0' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			role="region" aria-roledescription="hero" aria-label="<?php esc_attr_e( 'Hero', 'goallord-addons' ); ?>">

			<div class="goallord-ah__viewport" aria-live="polite">
				<div class="goallord-ah__track">
					<?php
					$slide_index = 0;
					foreach ( $slides as $slide ) {
						$this->render_slide( $slide, $settings, $slide_index );
						$slide_index++;
					}
					?>
				</div>
			</div>

			<?php if ( 'slider' === $mode && $show_arrows ) : ?>
				<button type="button" class="goallord-ah__arrow goallord-ah__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'goallord-addons' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
				</button>
				<button type="button" class="goallord-ah__arrow goallord-ah__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'goallord-addons' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8.59 16.59 10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>
				</button>
			<?php endif; ?>

			<?php if ( 'slider' === $mode && $show_dots ) : ?>
				<div class="goallord-ah__dots" aria-label="<?php esc_attr_e( 'Slide navigation', 'goallord-addons' ); ?>">
					<?php for ( $i = 0; $i < count( $slides ); $i++ ) : ?>
						<button type="button"
							class="goallord-ah__dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
							aria-label="<?php printf( esc_attr__( 'Go to slide %d', 'goallord-addons' ), $i + 1 ); ?>"
							<?php echo 0 === $i ? 'aria-current="true"' : ''; ?>
							data-slide-to="<?php echo (int) $i; ?>"></button>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_slide( $slide, $settings, $index ) {
		$layout       = in_array( $slide['slide_layout'] ?? 'centered', [ 'centered', 'split-left', 'split-right', 'fullbg', 'minimal' ], true )
			? $slide['slide_layout']
			: 'centered';
		$content_align = (string) ( $slide['slide_content_align'] ?? 'center' );

		$slide_classes = [
			'goallord-ah__slide',
			'goallord-ah__slide--layout-' . $layout,
			'goallord-ah__slide--align-' . $content_align,
		];
		if ( 0 === $index ) {
			$slide_classes[] = 'is-active';
		}

		/* --- Background styles via inline style on .bg --- */
		$bg_type  = (string) ( $slide['slide_bg_type'] ?? 'none' );
		$bg_style = '';
		switch ( $bg_type ) {
			case 'color':
				$bg_style = 'background-color: ' . esc_attr( $slide['slide_bg_color'] ?? '#0f1434' ) . ';';
				break;
			case 'gradient':
				$from  = esc_attr( $slide['slide_bg_gradient_from'] ?? '#0f1434' );
				$to    = esc_attr( $slide['slide_bg_gradient_to']   ?? '#1e2a7a' );
				$angle = isset( $slide['slide_bg_gradient_angle']['size'] ) ? (int) $slide['slide_bg_gradient_angle']['size'] : 135;
				$bg_style = 'background: linear-gradient(' . $angle . 'deg, ' . $from . ', ' . $to . ');';
				break;
			case 'image':
				if ( ! empty( $slide['slide_bg_image']['url'] ) ) {
					$url      = esc_url( $slide['slide_bg_image']['url'] );
					$bg_style = 'background-image: url("' . $url . '"); background-size: cover; background-position: center;';
				}
				break;
		}

		$overlay_color = (string) ( $slide['slide_overlay_color'] ?? '' );
		$parallax = 'yes' === ( $slide['slide_bg_parallax'] ?? '' ) && 'image' === $bg_type;

		/* --- CTAs, badge, media --- */
		$badge_on     = 'yes' === ( $slide['slide_badge_show'] ?? '' );
		$badge_text   = (string) ( $slide['slide_badge_text'] ?? '' );
		$badge_icon   = ( isset( $slide['slide_badge_icon'] ) && is_array( $slide['slide_badge_icon'] ) ) ? $slide['slide_badge_icon'] : [];

		$headline  = (string) ( $slide['slide_headline']    ?? '' );
		$subhead   = (string) ( $slide['slide_subheading']  ?? '' );
		$desc      = (string) ( $slide['slide_description'] ?? '' );
		$trust     = (string) ( $slide['slide_trust_text']  ?? '' );

		$cta1_on   = 'yes' === ( $slide['slide_cta1_show'] ?? '' );
		$cta1_text = (string) ( $slide['slide_cta1_text'] ?? '' );
		$cta1_icon = ( isset( $slide['slide_cta1_icon'] ) && is_array( $slide['slide_cta1_icon'] ) ) ? $slide['slide_cta1_icon'] : [];
		$cta1_url  = isset( $slide['slide_cta1_link']['url'] ) ? $slide['slide_cta1_link']['url'] : '';
		$cta1_tg   = ! empty( $slide['slide_cta1_link']['is_external'] ) ? ' target="_blank"' : '';
		$cta1_rel  = ! empty( $slide['slide_cta1_link']['nofollow'] )    ? ' rel="nofollow noopener"' : '';

		$cta2_on   = 'yes' === ( $slide['slide_cta2_show'] ?? '' );
		$cta2_text = (string) ( $slide['slide_cta2_text'] ?? '' );
		$cta2_icon = ( isset( $slide['slide_cta2_icon'] ) && is_array( $slide['slide_cta2_icon'] ) ) ? $slide['slide_cta2_icon'] : [];
		$cta2_url  = isset( $slide['slide_cta2_link']['url'] ) ? $slide['slide_cta2_link']['url'] : '';
		$cta2_tg   = ! empty( $slide['slide_cta2_link']['is_external'] ) ? ' target="_blank"' : '';
		$cta2_rel  = ! empty( $slide['slide_cta2_link']['nofollow'] )    ? ' rel="nofollow noopener"' : '';

		$media_type  = (string) ( $slide['slide_media_type'] ?? 'none' );
		$media_img   = isset( $slide['slide_media_image']['id'] ) ? (int) $slide['slide_media_image']['id'] : 0;
		$media_img_u = isset( $slide['slide_media_image']['url'] ) ? (string) $slide['slide_media_image']['url'] : '';
		$media_vid   = isset( $slide['slide_media_video_url']['url'] ) ? (string) $slide['slide_media_video_url']['url'] : '';
		$media_pos   = isset( $slide['slide_media_video_poster']['url'] ) ? (string) $slide['slide_media_video_poster']['url'] : '';

		$bg_vid      = isset( $slide['slide_bg_video_url']['url'] ) ? (string) $slide['slide_bg_video_url']['url'] : '';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $slide_classes ) ); ?>"
			data-slide-index="<?php echo (int) $index; ?>"
			role="group"
			aria-roledescription="slide"
			aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">

			<div class="goallord-ah__bg<?php echo $parallax ? ' goallord-ah__bg--parallax' : ''; ?>"
				<?php echo $bg_style ? 'style="' . $bg_style . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				aria-hidden="true">
				<?php if ( 'video' === $bg_type && '' !== $bg_vid ) : ?>
					<video class="goallord-ah__bg-video" autoplay muted loop playsinline preload="metadata">
						<source src="<?php echo esc_url( $bg_vid ); ?>" type="video/mp4">
					</video>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $overlay_color ) : ?>
				<div class="goallord-ah__overlay" aria-hidden="true" style="background-color: <?php echo esc_attr( $overlay_color ); ?>;"></div>
			<?php endif; ?>

			<div class="goallord-ah__content">
				<div class="goallord-ah__content-inner">

					<?php if ( $badge_on && '' !== trim( $badge_text ) ) : ?>
						<div class="goallord-ah__badge" data-anim-order="1">
							<?php if ( ! empty( $badge_icon['value'] ) ) : ?>
								<span class="goallord-ah__badge-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $badge_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
							<?php endif; ?>
							<?php echo esc_html( $badge_text ); ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== trim( $subhead ) ) : ?>
						<div class="goallord-ah__subheading" data-anim-order="2"><?php echo esc_html( $subhead ); ?></div>
					<?php endif; ?>

					<?php if ( '' !== trim( $headline ) ) : ?>
						<h1 class="goallord-ah__headline" data-anim-order="3"><?php echo $this->render_headline( $headline ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<?php endif; ?>

					<?php if ( '' !== trim( $desc ) ) : ?>
						<p class="goallord-ah__description" data-anim-order="4"><?php echo wp_kses( nl2br( $desc ), Helpers::kses_inline() ); ?></p>
					<?php endif; ?>

					<?php if ( ( $cta1_on && '' !== $cta1_text ) || ( $cta2_on && '' !== $cta2_text ) ) : ?>
						<div class="goallord-ah__ctas" data-anim-order="5">
							<?php if ( $cta1_on && '' !== $cta1_text ) : ?>
								<?php $href1 = '' !== $cta1_url ? ' href="' . esc_url( $cta1_url ) . '"' : ''; ?>
								<a class="goallord-ah__cta goallord-ah__cta--cta1"<?php echo $href1 . $cta1_tg . $cta1_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<span class="goallord-ah__cta-text"><?php echo esc_html( $cta1_text ); ?></span>
									<?php if ( ! empty( $cta1_icon['value'] ) ) : ?>
										<span class="goallord-ah__cta-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $cta1_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php if ( $cta2_on && '' !== $cta2_text ) : ?>
								<?php $href2 = '' !== $cta2_url ? ' href="' . esc_url( $cta2_url ) . '"' : ''; ?>
								<a class="goallord-ah__cta goallord-ah__cta--cta2"<?php echo $href2 . $cta2_tg . $cta2_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php if ( ! empty( $cta2_icon['value'] ) ) : ?>
										<span class="goallord-ah__cta-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $cta2_icon, [ 'aria-hidden' => 'true' ] ); ?></span>
									<?php endif; ?>
									<span class="goallord-ah__cta-text"><?php echo esc_html( $cta2_text ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== trim( $trust ) ) : ?>
						<div class="goallord-ah__trust" data-anim-order="6"><?php echo esc_html( $trust ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( in_array( $layout, [ 'split-left', 'split-right' ], true ) && 'none' !== $media_type ) : ?>
					<div class="goallord-ah__media" data-anim-order="7">
						<?php if ( 'image' === $media_type && ( $media_img > 0 || '' !== $media_img_u ) ) : ?>
							<?php if ( $media_img > 0 ) : ?>
								<?php echo wp_get_attachment_image( $media_img, 'large', false, [ 'class' => 'goallord-ah__media-img', 'loading' => 'lazy' ] ); ?>
							<?php else : ?>
								<img class="goallord-ah__media-img" src="<?php echo esc_url( $media_img_u ); ?>" alt="" loading="lazy" />
							<?php endif; ?>
						<?php elseif ( 'video' === $media_type && '' !== $media_vid ) : ?>
							<video class="goallord-ah__media-video"
								<?php echo $media_pos ? 'poster="' . esc_url( $media_pos ) . '"' : ''; ?>
								controls playsinline preload="metadata">
								<source src="<?php echo esc_url( $media_vid ); ?>" type="video/mp4">
							</video>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders a headline string, converting any {{ phrase }} substring
	 * into <span class="goallord-ah__highlight"> for the accent color.
	 */
	private function render_headline( $text ) {
		$text = (string) $text;

		// Escape everything first, then re-insert the highlight markup.
		$parts = preg_split( '/\{\{(.+?)\}\}/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		if ( ! is_array( $parts ) || count( $parts ) <= 1 ) {
			return esc_html( $text );
		}

		$out = '';
		foreach ( $parts as $i => $part ) {
			if ( $i % 2 === 1 ) {
				$out .= '<span class="goallord-ah__highlight">' . esc_html( $part ) . '</span>';
			} else {
				$out .= esc_html( $part );
			}
		}
		return $out;
	}
}
