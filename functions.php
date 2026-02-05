<?php
/**
 * Pierian Child Theme functions and definitions
 *
 * @package Pierian_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue parent and child theme styles.
 */
function pierian_child_enqueue_styles() {
	wp_enqueue_style(
		'pierian-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'pierian' )->get( 'Version' )
	);
	wp_enqueue_style(
		'pierian-child-style',
		get_stylesheet_uri(),
		array( 'pierian-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'pierian_child_enqueue_styles' );
