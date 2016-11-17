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
		add_action( 'init', function () {
			add_shortcode( 'wp_liveblog', array( $this, 'shortcode' ) );
		} );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . 'css/wp-liveblog.css', null, '0.1.0' );
		wp_enqueue_script( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . '/js/wp-liveblog.js', null, '0.1.0', true );
	}

	public function shortcode( $atts = [ ], $content = null ) {
		$html = '';
		$this->enqueue_scripts();
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			ob_start(); ?>
			<form id="post-submission-form">
				<div>
					<label for="post-submission-title">
						<?php esc_html_e( 'Title', 'wp-liveblog' ); ?>
					</label>
					<input type="text" name="post-submission-title" id="post-submission-title" required aria-required="true">
				</div>
				<div>
					<label for="post-submission-excerpt">
						<?php esc_html_e( 'Excerpt', 'wp-liveblog' ); ?>
					</label>
					<textarea rows="2" cols="20" name="post-submission-excerpt" id="post-submission-excerpt" required aria-required="true"></textarea>
				</div>
				<div>
					<label for="post-submission-content">
						<?php esc_html_e( 'Content', 'wp-liveblog' ); ?>
					</label>
					<textarea rows="10" cols="20" name="post-submission-content" id="post-submission-content"></textarea>
				</div>
				<input type="submit" value="<?php esc_attr_e( 'Submit', 'wp-liveblog' ); ?>">
			</form>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}

}

WP_Liveblog::get_instance();
