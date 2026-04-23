<?php
/**
 * Shared helpers used across Goallord Addons widgets.
 *
 * @package Goallord\Addons
 */

namespace Goallord\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helpers {

	/**
	 * Trims text to a word count while preserving unicode.
	 *
	 * @param string $text
	 * @param int    $word_count
	 * @return string
	 */
	public static function trim_words( $text, $word_count ) {
		$text = wp_strip_all_tags( (string) $text );
		if ( $word_count <= 0 ) {
			return $text;
		}
		return wp_trim_words( $text, (int) $word_count, '&hellip;' );
	}

	/**
	 * Allowed tags for inline rich text like titles/descriptions.
	 */
	public static function kses_inline() {
		return [
			'a'      => [ 'href' => [], 'title' => [], 'target' => [], 'rel' => [] ],
			'br'     => [],
			'em'     => [],
			'strong' => [],
			'span'   => [ 'class' => [] ],
			'i'      => [ 'class' => [] ],
		];
	}

	/**
	 * Safe tag whitelist for title HTML tag control.
	 */
	public static function safe_tag( $tag, $default = 'h3' ) {
		$allowed = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
		$tag     = strtolower( (string) $tag );
		return in_array( $tag, $allowed, true ) ? $tag : $default;
	}

	/**
	 * Returns the Elementor placeholder image URL (fallback to WordPress placeholder).
	 */
	public static function placeholder_image() {
		if ( class_exists( '\\Elementor\\Utils' ) && method_exists( '\\Elementor\\Utils', 'get_placeholder_image_src' ) ) {
			return \Elementor\Utils::get_placeholder_image_src();
		}
		return includes_url( 'images/media/default.png' );
	}

	/**
	 * Returns { slug => label } for public post types.
	 * Safe to call during Elementor control registration.
	 */
	public static function get_post_type_options() {
		$options = [];
		if ( ! function_exists( 'get_post_types' ) ) {
			return [ 'post' => 'Post' ];
		}
		$types = get_post_types( [ 'public' => true ], 'objects' );
		foreach ( $types as $slug => $obj ) {
			if ( 'attachment' === $slug ) {
				continue;
			}
			$options[ $slug ] = $obj->labels->singular_name ?: $slug;
		}
		if ( empty( $options ) ) {
			$options['post'] = 'Post';
		}
		return $options;
	}

	/**
	 * Returns { term_id => term_name } for a given taxonomy.
	 * Avoids failing registration when a site has no terms yet.
	 */
	public static function get_term_options( $taxonomy ) {
		if ( ! function_exists( 'get_terms' ) || ! function_exists( 'taxonomy_exists' ) ) {
			return [];
		}
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}
		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => 500,
			]
		);
		$options = [];
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ (int) $term->term_id ] = $term->name;
			}
		}
		return $options;
	}

	/**
	 * Parses a comma/space separated list of IDs into a clean int array.
	 */
	public static function parse_id_list( $raw ) {
		if ( is_array( $raw ) ) {
			$parts = $raw;
		} else {
			$parts = preg_split( '/[\s,]+/', (string) $raw );
		}
		$out = [];
		foreach ( (array) $parts as $p ) {
			$id = (int) $p;
			if ( $id > 0 ) {
				$out[] = $id;
			}
		}
		return array_values( array_unique( $out ) );
	}
}
