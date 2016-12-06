<?php

/**
 * Class WP_Liveblog
 * Base class for the plugin
 *
 * https://public-api.wordpress.com/rest/v1.1/sites/humbleself.com%2Fwp/posts?type=wp_liveblog_post
 *
 */
class WP_Liveblog {

	private static $instance = false;
	private $shortcode_instance = 0;

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', function () {
			$this->register_custom_post_type();
			$this->register_taxonomy();
			add_shortcode( 'wp_liveblog', array( $this, 'shortcode' ) );
		} );

		// Allow wp rest api to update our custom taxonomy
		add_action( 'rest_api_init', function () {
			register_rest_field( 'wp_liveblog_post',
				'wp_liveblog_instance',
				array(
					'get_callback'    => function ( $object, $field_name, $request ) {
						return get_the_terms( $object['id'], $field_name );
						//return get_post_meta( $object['id'], $field_name );
					},
					'update_callback' => function ( $value, $object, $field_name ) {
						if ( ! $value || ! is_string( $value ) ) {
							return;
						}

						return wp_set_object_terms( $object->ID, strip_tags( $value ), $field_name );
					},
					'schema'          => array(
						'description' => __( 'Instance of WP Liveblog this post belongs to', 'wp-liveblog' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
					),
				) );
		} );

		// Filter for default post type query in WP_REST_Posts_Controller
		add_filter( 'rest_wp_liveblog_post_query', function ( $args, $request ) {
			$query_params = $request->get_query_params();
			if ( ! empty( $query_params['wp_liveblog_instance'] ) ) {
				$args['wp_liveblog_instance'] = $query_params['wp_liveblog_instance'][0];
			}

//			echo '/*';
//			print_r($args);
//			echo '*/';

			return $args;
		}, 10, 2 );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . 'css/wp-liveblog.css', null, '0.1.0' );
		wp_enqueue_script( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . 'js/wp-liveblog.js', null, '0.1.0', true );
		wp_localize_script( 'wp-liveblog', 'WP_LIVEBLOG_SUBMITTER', array(
				'root'            => esc_url_raw( rest_url() ),
				'nonce'           => wp_create_nonce( 'wp_rest' ),
				'success'         => __( 'Thanks for your submission!', 'wp-liveblog' ),
				'failure'         => __( 'Your submission could not be processed.', 'wp-liveblog' ),
				'current_user_id' => get_current_user_id()
			)
		);
	}

	private function register_custom_post_type() {

		$labels = array(
			'name'               => _x( 'Liveblog Posts', 'Post Type General Name', 'wp-liveblog' ),
			'singular_name'      => _x( 'Liveblog Post', 'Post Type Singular Name', 'wp-liveblog' ),
			'menu_name'          => __( 'Liveblog Posts', 'wp-liveblog' ),
			'parent_item_colon'  => __( 'Parent Liveblog Post', 'wp-liveblog' ),
			'all_items'          => __( 'All Liveblog Posts', 'wp-liveblog' ),
			'view_item'          => __( 'View Liveblog Post', 'wp-liveblog' ),
			'add_new_item'       => __( 'Add New Liveblog Post', 'wp-liveblog' ),
			'add_new'            => __( 'Add New', 'wp-liveblog' ),
			'edit_item'          => __( 'Edit Liveblog Post', 'wp-liveblog' ),
			'update_item'        => __( 'Update Liveblog Post', 'wp-liveblog' ),
			'search_items'       => __( 'Search Liveblog Posts', 'wp-liveblog' ),
			'not_found'          => __( 'Not Found', 'wp-liveblog' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'wp-liveblog' ),
		);

		$args = array(
			'label'               => __( 'liveblog posts', 'wp-liveblog' ),
			'description'         => __( 'Liveblog Posts', 'wp-liveblog' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'author',
				'thumbnail',
				'revisions',
				'custom-fields',
			),
			'taxonomies'          => array( 'wp_liveblog_instance', 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'rest_base'           => 'wp_liveblog_posts',
			//'rest_controller_class' => 'WP_Liveblog_Posts_Controller',
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		// Registering your Custom Post Type
		register_post_type( 'wp_liveblog_post', $args );

		// Allow in REST API
		add_filter( 'rest_api_allowed_post_types', function ( $allowed_post_types ) {
			$allowed_post_types[] = 'wp_liveblog_post';

			return $allowed_post_types;
		} );

	}

	private function register_taxonomy() {

		$args = array(
			'hierarchical' => false,
			'label'        => 'WP Liveblog Instances',
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => 'wp_liveblog_instance' ),
			'show_in_rest' => true,
			'rest_base'    => 'wp_liveblog_instance',
		);

		register_taxonomy( 'wp_liveblog_instance', array( 'wp_liveblog_post' ), $args );

	}


	public function shortcode( $atts = [ ], $content = null ) {
		$this->shortcode_instance ++;
		$html = '';
		$this->enqueue_scripts();
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			ob_start(); ?>
			<form class="wp-liveblog-form" id="<?php echo esc_attr( 'wp-liveblog-form-' . $this->shortcode_instance ); ?>">
				<input type="hidden" name="wp-liveblog-instance" id="<?php echo esc_attr( 'wp-liveblog-instance-' . $this->shortcode_instance ); ?>" value="<?php esc_attr( $this->shortcode_instance ); ?>">
				<div>
					<label for="wp-liveblog-title"><?php esc_html_e( 'Title', 'wp-liveblog' ); ?></label>
					<input type="text" name="wp-liveblog-title" id="<?php echo esc_attr( 'wp-liveblog-title-' . $this->shortcode_instance ); ?>" requireds aria-required="true">
				</div>
				<div>
					<label for="wp-liveblog-excerpt"><?php esc_html_e( 'Excerpt', 'wp-liveblog' ); ?></label>
					<textarea rows="2" cols="20" name="wp-liveblog-excerpt" id="<?php echo esc_attr( 'wp-liveblog-excerpt-' . $this->shortcode_instance ); ?>" requireds aria-required="true"></textarea>
				</div>
				<div>
					<label for="wp-liveblog-content"><?php esc_html_e( 'Content', 'wp-liveblog' ); ?></label>
					<textarea rows="10" cols="20" name="wp-liveblog-content" id="<?php echo esc_attr( 'wp-liveblog-content-' . $this->shortcode_instance ); ?>"></textarea>
				</div>
				<input type="submit" value="<?php echo esc_attr_e( 'Submit', 'wp-liveblog' ); ?>">
			</form>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}
}

WP_Liveblog::get_instance();
