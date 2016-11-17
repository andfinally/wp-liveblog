<?php

/*
Plugin Name: WP Liveblog
Plugin URI:
Description: Shortcode adding liveblog form and feed to posts. Liveblog uses REST API and custom post type.
Version:     0.1.0
Author:      Andfinally
Author URI:
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'WP_LIVEBLOG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_LIVEBLOG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once( WP_LIVEBLOG_PLUGIN_DIR . 'class.wp-liveblog.php' );
