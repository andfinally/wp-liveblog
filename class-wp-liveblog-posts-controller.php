<?php

/**
 * Class WP_Liveblog_Posts_Controller
 * http://humbleself.com/wp/wp-json/wp_liveblog/v1/wp_liveblog_posts
 * http://humbleself.com/wp/wp-json/wp_liveblog/v1/wp_liveblog_posts?wp_liveblog_instance=1
 */
class WP_Liveblog_Posts_Controller extends WP_REST_Posts_Controller {

	public function register_routes() {

		$version   = '1';
		$namespace = 'wp_liveblog/v' . $version;
		$base      = 'wp_liveblog_posts';

		register_rest_route( $namespace, '/' . $base . '/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_items' ),
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>\d+)', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_item' ),
				//'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'     => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
					)
				),
			)
		);
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {

		$args              = array();
		$args['post_type'] = 'wp_liveblog_post';

		// get parameters from request
		$params = $request->get_query_params();
		if ( ! empty( $params['wp_liveblog_instance'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'wp_liveblog_instance',
					'field'    => 'slug',
					'terms'    => $params['wp_liveblog_instance'],
				),
			);
		}

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {
			$post->meta = $params;
		}

		$data = $this->prepare_item_for_response( $posts, $request );

		// return a response or error
		if ( ! empty( $data ) ) {
			return new WP_REST_Response( $data, 200 );
		} else {
			return new WP_Error( 'code', __( 'message', 'text-domain' ) );
		}

	}

}

add_action( 'rest_api_init', function () {
	$controller = new WP_Liveblog_Posts_Controller( 'wp_liveblog_post' );
	$controller->register_routes();
} );
