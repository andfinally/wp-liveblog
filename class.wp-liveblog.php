<?php

/**
 * Class WP_Liveblog
 * Base class for the plugin
 */
class WP_Liveblog {

	private static $instance = false;

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'add_shortcode' ) );
	}

	public function add_shortcode() {
		function wp_liveblog_shortcode( $atts = [ ], $content = null ) {
			$html = '<div class="wp-shortcode">XXXXXXX</div>';
			return $html;
		}

		add_shortcode( 'wp_liveblog', 'wp_liveblog_shortcode' );
	}

}

WP_LIVEBLOG::get_instance();
