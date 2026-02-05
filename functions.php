<?php

/**
 * Pierian Child Theme functions and definitions
 *
 * @package Pierian_Child
 */

defined('ABSPATH') || exit;

/**
 * Enqueue parent and child theme styles.
 */
function pierian_child_enqueue_styles()
{
	$version = '1.0.0';

	wp_enqueue_style(
		'pierian-style',
		get_template_directory_uri() . '/style.css',
		array(),
		$version
	);
	wp_enqueue_style(
		'pierian-child-style',
		get_stylesheet_uri(),
		array('pierian-style'),
		$version
	);
}
add_action('wp_enqueue_scripts', 'pierian_child_enqueue_styles');


add_action('wp_head', 'take_as_directed_custom_styles_inline');
function take_as_directed_custom_styles_inline()
{
	echo '<style type="text/css">
        .wp-block-post-content p {
		  text-indent: 2rem;
		}
		
		.shiftnav.shiftnav-skin-light ul.shiftnav-menu li.menu-item.current-menu-item > .shiftnav-target, 
		.shiftnav.shiftnav-skin-light ul.shiftnav-menu li.menu-item ul.sub-menu .current-menu-item > .shiftnav-target {
		  background-color: gray;
		}
		
		.wp-block-navigation .wp-block-navigation-item.current-menu-item {
		  background-color: lightgray;
		}
    </style>';
}

/**
 * Output Person JSON-LD schema in the footer (runs on every page regardless of template).
 */
function take_as_directed_person_schema()
{
	$current_url = is_singular()
		? get_permalink(get_queried_object_id())
		: home_url(isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
	$current_url = esc_url($current_url);
?>
	<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "Person",
			"@id": "https://takeasdirected.missionmike.dev/about-the-author/#person",
			"name": "Michael Dinerstein",
			"url": "<?php echo esc_url($current_url); ?>",
			"image": "https://takeasdirected.missionmike.dev/path-to-author-photo.jpg",
			"jobTitle": "Software Engineer",
			"description": "",
			"sameAs": [
				"https://www.linkedin.com/in/michaeldinerstein/",
				"https://github.com/missionmike"
			],
			"knowsAbout": [
				"WordPress Development",
				"Next.js",
				"React",
				"JavaScript",
				"TypeScript",
				"Cloud Infrastructure",
				"Web Performance Optimization",
				"SEO",
				"Writing"
			],
			"mainEntityOfPage": {
				"@type": "WebPage",
				"@id": "<?php echo esc_url($current_url); ?>"
			}
		}
	</script>
<?php
}
add_action('wp_footer', 'take_as_directed_person_schema');
