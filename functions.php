<?php

/**
 * Pierian Child Theme functions and definitions
 *
 * @package Pierian_Child
 */

defined('ABSPATH') || exit;

// JSON encode flags (PHP 5.4+; defined here for linter and edge-case compatibility).
if (!defined('JSON_UNESCAPED_SLASHES')) {
	define('JSON_UNESCAPED_SLASHES', 64);
}
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

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
 * Force og:type to "book" so LinkedIn and other scrapers see chapters as book content, not article.
 * Runs at priority 15 to override theme/plugin defaults.
 */
function take_as_directed_og_type_book()
{
	if (!is_singular()) {
		return;
	}
	echo '<meta property="og:type" content="book" />' . "\n";
}
add_action('wp_head', 'take_as_directed_og_type_book', 15);

/**
 * Output og:author and chapter publish date meta tags in the head (singular posts/pages).
 * Uses article:published_time (OG has no chapter type; article is correct for content pages).
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
			"alternateName": ["Mission Mike", "mission mike", "missionmike"],
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
				"Frontend Development",
				"Backend Development",
				"Full Stack Development",
				"Database Development",
				"API Development",
				"Cloud Development",
				"DevOps",
				"Docker",
				"Kubernetes",
				"CI/CD",
				"Git",
				"GitHub",
				"GitLab",
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

/**
 * Output Book and Chapter JSON-LD schema in the footer on singular pages.
 * Each page is a chapter; the book is the site.
 */
function take_as_directed_book_chapter_schema()
{
	if (!is_singular()) {
		return;
	}
	$post_id = get_queried_object_id();
	$post = get_post($post_id);
	if (!$post) {
		return;
	}
	$chapter_url = get_permalink($post_id);
	$book_name = get_bloginfo('name');
	$book_url = home_url('/');
	$author_name = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
	$published = get_the_date('c', $post_id);

	// Chapter position: use menu order if set, else order among published pages
	$position = $post->menu_order;
	if ($position === 0) {
		$siblings = get_posts([
			'post_type' => $post->post_type,
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby' => 'menu_order title',
			'order' => 'ASC',
			'fields' => 'ids',
		]);
		$idx = array_search($post_id, $siblings, true);
		$position = $idx !== false ? $idx + 1 : 1;
	} else {
		$position = $position ?: 1;
	}

	$chapter_schema = [
		'@context' => 'https://schema.org',
		'@type' => 'Chapter',
		'name' => get_the_title($post_id),
		'url' => esc_url($chapter_url),
		'position' => (int) $position,
		'isPartOf' => [
			'@type' => 'Book',
			'@id' => esc_url($book_url) . '#book',
			'name' => $book_name,
			'url' => esc_url($book_url),
		],
	];
	if ($author_name) {
		$chapter_schema['author'] = [
			'@type' => 'Person',
			'name' => $author_name,
		];
	}
	if ($published) {
		$chapter_schema['datePublished'] = $published;
	}

	// Front matter: Preface, Author's Note, Introduction (positions 1–3).
	$front_matter_genres = [
		1 => 'Preface',
		2 => "Author's Note",
		3 => 'Introduction',
	];
	if (isset($front_matter_genres[$position])) {
		$chapter_schema['genre'] = $front_matter_genres[$position];
	}

	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode($chapter_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	echo "\n" . '</script>' . "\n";
}
add_action('wp_footer', 'take_as_directed_book_chapter_schema', 5);

/**
 * Output Book JSON-LD schema on the homepage (defines the book and its chapters).
 */
function take_as_directed_book_schema()
{
	if (!is_front_page()) {
		return;
	}
	$book_name = get_bloginfo('name');
	$book_url = home_url('/');
	$book_id = esc_url($book_url) . '#book';

	$chapters = get_posts([
		'post_type' => 'page',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'menu_order title',
		'order' => 'ASC',
	]);
	$author_name = get_the_author_meta('display_name', get_post_field('post_author', $chapters[0]->ID ?? 0));

	$front_matter_genres = [
		1 => 'Preface',
		2 => "Author's Note",
		3 => 'Introduction',
	];
	$has_part = [];
	foreach ($chapters as $i => $ch) {
		$position = $i + 1;
		$part = [
			'@type' => 'Chapter',
			'name' => get_the_title($ch),
			'url' => get_permalink($ch),
			'position' => $position,
		];
		if (isset($front_matter_genres[$position])) {
			$part['genre'] = $front_matter_genres[$position];
		}
		$has_part[] = $part;
	}

	$book_schema = [
		'@context' => 'https://schema.org',
		'@type' => 'Book',
		'@id' => $book_id,
		'name' => $book_name,
		'url' => esc_url($book_url),
		'hasPart' => $has_part,
	];
	if ($author_name) {
		$book_schema['author'] = [
			'@type' => 'Person',
			'name' => $author_name,
		];
	}

	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode($book_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	echo "\n" . '</script>' . "\n";
}
add_action('wp_footer', 'take_as_directed_book_schema', 5);
