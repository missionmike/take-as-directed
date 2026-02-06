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

/**
 * Output favicon link (book icon) in the head.
 */
function take_as_directed_favicon()
{
	$favicon_url = get_stylesheet_directory_uri() . '/favicon.svg';
	echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_url) . '" />' . "\n";
}
add_action('wp_head', 'take_as_directed_favicon', 1);

/**
 * Output og:author and article publish date meta tags in the head (singular posts/pages).
 */
function take_as_directed_og_author_and_date()
{
	if (!is_singular()) {
		return;
	}
	$post_id = get_queried_object_id();
	$author_name = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
	$published = get_the_date('c', $post_id);
	if ($author_name) {
		echo '<meta property="og:author" content="' . esc_attr($author_name) . '" />' . "\n";
		echo '<meta name="author" content="' . esc_attr($author_name) . '" />' . "\n";
	}
	if ($published) {
		echo '<meta property="article:published_time" content="' . esc_attr($published) . '" />' . "\n";
	}
}
add_action('wp_head', 'take_as_directed_og_author_and_date', 5);

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
			"alternateName": "Mission Mike",
			"url": "<?php echo esc_url($current_url); ?>",
			"image": "https://takeasdirected.missionmike.dev/wp-content/uploads/2026/01/family-gaming-photo-with-memphis.jpg",
			"jobTitle": ["Chief Technology Officer", "Senior Software Engineer", "Web Architect", "Content Creator"],
			"description": "Michael R. Dinerstein (Mission Mike) is a technically-inclined creative with a love for family, life, innovation and expression. When not working on tech, you can find him producing YouTube content with his family.",
			"sameAs": [
				"https://www.linkedin.com/in/michaeldinerstein/",
				"https://github.com/missionmike",
				"https://www.ampdresume.com/r/michael-dinerstein",
				"https://www.instagram.com/missionmike.dev/",
				"https://www.instagram.com/sleepyslawths/",
				"https://www.facebook.com/missionmike.dev/",
				"https://www.facebook.com/sleepyslawths/",
				"https://www.x.com/@missionmikedev/",
				"https://www.tiktok.com/@missionmike",
				"https://www.tiktok.com/@sleepyslawths",
				"https://www.youtube.com/@missionmike.d",
				"https://www.youtube.com/@sleepyslawths",
				"https://www.youtube.com/@sleepyslawthgaming",
				"https://www.youtube.com/@michaelndad"
			],
			"knowsAbout": [
				"Software Engineering",
				"WordPress Development",
				"Next.js",
				"React",
				"JavaScript",
				"TypeScript",
				"Cloud Infrastructure",
				"Web Performance Optimization",
				"SEO",
				"Writing",
				"Video Production",
				"Content Creation",
				"Social Media Management"
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
