<?php

/**
 * Class WP_Liveblog
 * Base class for the plugin
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

//		function allow_post_type( $allowed_post_types ) {
//			$allowed_post_types[] = 'wp_liveblog_post';
//
//			return $allowed_post_types;
//		}
//
//		add_filter( 'rest_api_allowed_post_types', 'allow_post_type' );
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
					<input type="text" name="wp-liveblog-title" id="<?php echo esc_attr( 'wp-liveblog-title-' . $this->shortcode_instance ); ?>" required aria-required="true">
				</div>
				<div>
					<label for="wp-liveblog-excerpt"><?php esc_html_e( 'Excerpt', 'wp-liveblog' ); ?></label>
					<textarea rows="2" cols="20" name="wp-liveblog-excerpt" id="<?php echo esc_attr( 'wp-liveblog-excerpt-' . $this->shortcode_instance ); ?>" required aria-required="true"></textarea>
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
			// You can associate this CPT with a taxonomy or custom taxonomy.
			'taxonomies'          => array( 'wp_liveblog_instance', 'category', 'post_tag' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'rest_base'           => 'wp_liveblog_posts',
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
		register_taxonomy(
			'wp_liveblog_instance',
			'wp_liveblog_post',
			array(
				'hierarchical' => true,
				'label'        => 'WP Liveblog Instance',
				'query_var'    => true,
				'rewrite'      => array(
					'slug'       => 'wp-liveblog-instance',
					'with_front' => false,
				),
				'show_in_rest' => true,
			)
		);
	}
}

WP_Liveblog::get_instance();
