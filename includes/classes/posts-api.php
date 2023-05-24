<?php

if ( ! defined('ABSPATH') )
	die( 'Direct access not permitted.' );

class Posts_API {

	/**
	 * API namespace
	 * @var string
	 */
	private string $namespace = 'react/v1';

	/**
	 * Posts_API constructor.
	 */
	public function __construct() {

	}

	/**
	 * Register post API routes
	 */
	public function register_routes(): void {
		register_rest_route($this->namespace, '/posts', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_posts'),
			'permission_callback' => '__return_true'
		));

		register_rest_route($this->namespace, '/posts/(?P<identifier>[a-zA-Z0-9_-]+)/(?P<type>id|slug)', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_post'),
			'permission_callback' => '__return_true'
		));

		register_rest_route($this->namespace, '/posts', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_post'),
			'permission_callback' => '__return_true'
		));

		register_rest_route($this->namespace, '/posts/update/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_post'),
			'permission_callback' => '__return_true'
		));

		register_rest_route($this->namespace, '/posts/delete/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_post'),
			'permission_callback' => '__return_true',
		));
	}

	/** CRUD Functions */

	/**
	 * Get all posts
	 *
	 * @return WP_REST_Response
	 */
	public function get_posts(): WP_REST_Response {
		// Query posts and return the array
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => -1,
		);

		$posts = get_posts($args);

		// Prepare the response data
		$response_data = array();

		foreach ($posts as $post) {
			$response_data[] = $this->get_post_data($post);
		}

		return new WP_REST_Response($response_data, 200);
	}

	/**
	 * Get a specific post by ID or slug
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_post( WP_REST_Request $request): WP_Error|WP_REST_Response {
		$identifier = $request->get_param('identifier');
		$type = $request->get_param('type');

		$post_id = 0;

		if ($type === 'slug') {
			// If the type is slug, we get the post ID using the slug
			$post = get_page_by_path($identifier, OBJECT, 'post');

			if ($post instanceof WP_Post)
				$post_id = $post->ID;
		} else if ($type === 'id') {
			// If the type is ID, we use the identifier directly
			$post_id = intval($identifier);
		}

		// Get the post by ID and return the response
		$post = get_post($post_id);

		if (!$post)
			return new WP_Error('post_not_found', 'Post not found', array('status' => 404));

		return new WP_REST_Response($this->get_post_data($post), 200);
	}

	/**
	 * Create a post
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_post(WP_REST_Request $request): WP_Error|WP_REST_Response {
		// Gte the post data from request body
		$post_data = $request->get_json_params();

		// Create a new post
		$post_id = wp_insert_post($post_data, true);

		if (is_wp_error($post_id)) {
			return new WP_Error('post_creation_failed', $post_id->get_error_message(), array('status' => 500));
		}

		// Get the new post
		$post = get_post($post_id);

		// return the new post data
		return new WP_REST_Response($this->get_post_data($post), 201);
	}

	/**
	 * Update a post by id
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_post(WP_REST_Request $request): WP_Error|WP_REST_Response {
		$post_id = $request->get_param('id');
		$post_data = $request->get_json_params();

		// check if post exists
		$post = get_post($post_id);
		if (!$post) {
			return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
		}

		// update the data of post
		$updated = wp_update_post(array_merge(['ID' => $post_id], $post_data), true);

		if (is_wp_error($updated)) {
			return new WP_Error('post_update_failed', $updated->get_error_message(), array('status' => 500));
		}

		// Get updated post
		$updated_post = get_post($post_id);

		// return updated post data
		return new WP_REST_Response($this->get_post_data($updated_post), 200);
	}

	/**
	 * Delete a post by id
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_post( WP_REST_Request $request ): WP_Error|WP_REST_Response {
		$post_id = $request->get_param('id');

		// Chck if post exists
		$post = get_post( $post_id );
		if (!$post) {
			return new WP_Error( 'post_not_found', 'Post not found', array( 'status' => 404 ) );
		}

		// Delete the post
		$result = wp_delete_post( $post_id, true );

		if (!$result) {
			return new WP_Error( 'post_not_deleted', 'Post could not be deleted', array( 'status' => 500 ) );
		}

		return new WP_REST_Response( array( 'message' => 'Post deleted successfully' ), 200 );
	}

	/** Util Functions */

	/**
	 * Check authentication for authenticated requests
	 * @return bool
	 */
	private function check_authentication(): bool {
		return true;
	}

	private function get_post_data($post): array {
		$categories = wp_get_post_categories($post->ID, array('fields' => 'all'));

		$post_data = array(
			'id' => $post->ID,
			'slug' => $post->post_name,
			'link' => get_permalink($post->ID),
			'title' => $post->post_title,
			'status' => $post->post_status,
			'featured_image' => get_the_post_thumbnail_url($post->ID),
			'categories' => array(),
			'content' => $post->post_content,
			'meta_fields' => array()
		);

		foreach ($categories as $category) {
			$category_data = array(
				'id' => $category->term_id,
				'title' => $category->name,
				'description' => $category->description
			);

			$post_data['categories'][] = $category_data;
		}

		$meta_fields = get_post_meta($post->ID);

		foreach ($meta_fields as $key => $value) {
			$meta_field_data = array(
				'key' => $key,
				'value' => $value[0]
			);

			$post_data['meta_fields'][] = $meta_field_data;
		}

		return $post_data;
	}
}
