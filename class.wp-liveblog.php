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
			add_shortcode( 'wp_liveblog', array( $this, 'shortcode' ) );
		} );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . 'css/wp-liveblog.css', null, '0.1.0' );
		wp_enqueue_script( 'wp-liveblog', WP_LIVEBLOG_PLUGIN_URL . 'js/wp-liveblog.js', null, '0.1.0', true );
	}

	public function shortcode( $atts = [ ], $content = null ) {
		$this->shortcode_instance ++;
		$html = '';
		$this->enqueue_scripts();
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			ob_start(); ?>
			<form class="wp-liveblog-form" id="<?php echo esc_attr( 'wp-liveblog-form-' . $this->shortcode_instance ); ?>">
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

}

WP_Liveblog::get_instance();
