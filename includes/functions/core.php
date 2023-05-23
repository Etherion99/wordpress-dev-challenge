<?php

if ( ! defined('ABSPATH') )
	die( 'Direct access not permitted.' );

// menu
function create_menu_UI(): void {
	ob_start();
	include ETHERION_TOOLS_PATH.'/assets/templates/menu-UI.php';
	$content = ob_get_clean();

	echo $content;
}

// link analysis
function find_broken_links(): array {
	// Get all posts
	$query = new WP_Query(array(
		'post_type' => 'post',
		'posts_per_page' => 100,
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'last-link-analysis',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'last-link-analysis',
				'value'   => strtotime('-5 seconds', current_time('timestamp')),
				'compare' => '<=',
			)
		),
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	));

	// Array to store the found links
	$links = array();

	// Loop through the posts
	while ($query->have_posts()) {
		$query->the_post();

		$links = array();

		// Get the content of the post
		$content = get_the_content();

		// Use a regular expression to find <a> links
		preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $content, $matches);

		// Add the found links to the array
		if (!empty($matches[1])) {
			$post_links = $matches[1];

			foreach ($post_links as $link) {
				$status = get_link_status($link);

				if ($status !== 'valid') {
					$link_data = array(
						'url' => $link,
						'status' => $status
					);

					$links[] = $link_data;
				}
			}
		}

		update_post_meta(get_the_ID(), 'last-link-analysis', current_time('timestamp'));
		update_post_meta(get_the_ID(), 'last-link-result', $links);
	}

	// Restore the original query configuration
	wp_reset_postdata();

	return $links;
}

function get_link_status($url): string {
	// Check if the link is malformed
	$parsed_url = parse_url($url);
	if (!$parsed_url || !isset($parsed_url['scheme']) || !isset($parsed_url['host'])) {
		return 'malformed';
	}

	// Check if the link is insecure
	if ( str_starts_with( $url, 'http://' ) ) {
		return 'insecure';
	}

	// Check if the protocol is not specified
	if ( ! str_starts_with( $url, 'http://' ) && ! str_starts_with( $url, 'https://' ) ) {
		return 'no-protocol';
	}

	// Check the status code of the link
	$headers = @get_headers($url);

	if ($headers && preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $headers[0], $matches)) {
		$status_code = intval($matches[1]);

		if ($status_code < 200 || $status_code >= 300) {
			return 'status-code: '.$status_code;
		}
	}

	return 'valid';
}

function get_link_results(): array {
	$posts = get_posts(array(
		'post_type' => 'post',
		'posts_per_page' => -1,
	));

	$link_results = array();

	foreach ($posts as $post) {
		$last_link_result = get_post_meta($post->ID, 'last-link-result', true);
		var_dump($post->ID, $last_link_result);
		echo '<br><br>';

		if (empty($last_link_result)) {
			continue;
		}

		foreach ($last_link_result as $result) {
			$url = $result['url'];
			$status = $result['status'];
			$id = $post->ID;

			if (!isset($link_results[$url])) {
				$link_results[$url] = array(
					'url' => $url,
					'status' => $status,
					'posts' => array($id),
				);
			} else {
				$link_results[$url]['posts'][] = $id;
			}
		}
	}

	return $link_results;
}