<?php

if ( ! defined('ABSPATH') )
	die( 'Direct access not permitted.' );

if ( ! class_exists( 'WP_List_Table' ) )
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Link_Status_Table extends WP_List_Table {
	private $links;

	public function __construct() {
		parent::__construct( array(
			'singular' => 'link',
			'plural'   => 'links',
			'ajax'     => false
		) );
	}

	public function get_columns(): array {
		return array(
			'url'     => 'URL',
			'status'  => 'Status',
			'posts'   => 'Posts'
		);
	}

	public function prepare_items(): void {
		// Define column headers
		$this->_column_headers = array($this->get_columns());

		// Use found broken links as table data
		$this->items = $this->links;
	}

	public function column_default($item, $column_name) {
		return match ( $column_name ) {
			'url' => $item['url'],
			'status' => $this->get_status_text( $item['status'] ),
			'posts' => $this->generate_posts_links( $item['posts'] ),
			default => '',
		};
	}

	public function set_links($links): void {
		$this->links = $links;
	}

	private function get_status_text($status): string {
		switch ($status) {
			case 'insecure':
				return 'Enlace inseguro';
			case 'no-protocol':
				return 'Enlace sin protocolo';
			case 'malformed':
				return 'Enlace malformado';
			default:
				// Extract status code
				if (preg_match('/^status-code: (\d+)$/', $status, $matches))
					return 'Enlace con estatus:'.' '.$matches[1];

				return '';
		}
	}

	private function generate_posts_links($posts): string {
		$links = array();

		foreach ($posts as $post_id) {
			// Get post title and link
			$post_title = get_the_title($post_id);
			$post_link = get_permalink($post_id);

			// Push into links array
			$links[] = "<a href=\"$post_link\">$post_title</a>";
		}

		return implode(', ', $links);
	}
}
