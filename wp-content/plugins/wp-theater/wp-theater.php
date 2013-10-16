<?php
/*
Plugin Name: WP Theater
Plugin URI: http://redshiftstudio . com/wp-theater/
Description: Adds shortcodes that can display embeds, previews, user uploads, playlists, channels and groups from Youtube or Vimeo . 
Author: Kenton Farst
Author URI: http://kent . farst . net
Donate URI: http://redshiftstudio . com/wp-theater/
License: GPLv3
Version: 1.0.9
*/

if( defined( 'ABSPATH' ) && defined( 'WPINC' ) && !class_exists( 'WP_Theater' ) ){

class WP_Theater {

	/**
	 * Version constant
	 * @since WP Theater 1.0.0
	 */
	const VERSION = '1.0.9';

	/**
	 * Plugin directory
	 * @since WP Theater 1.0.8
	 */
	public static $dir;

	/**
	 * Plugin URI
	 * @since WP Theater 1.0.8
	 */
	public static $uri;

	/**
	 * Plugin includes directory
	 * @since WP Theater 1.0.8
	 */
	public static $inc;

	/**
	 * Plugin admin directory -- DOES NOT EXISTS YET
	 * @since WP Theater 1.0.8
	 */
	//public static $admin;

	/**
	 * Preset's class instance
	 * @since WP Theater 1.0.0
	 */
	public static $presets;

	/**
	 * Shortcode's class instance
	 * @since WP Theater 1.0.0
	 */
	public static $shortcodes;

	/**
	 * Setting's class instance
	 * @since WP Theater 1.0.0
	 */
	public static $settings;

	/**
	 * Constructor
	 * @since WP Theater 1.0.0
	 */
	public function WP_Theater() {__construct();}

	/**
	 * Constructs .  .  .  .    . 
	 * @since WP Theater 1.0.0
	 */
	function __construct() {
		// nothing to construct as it's all static methods
	}

	/**
	 * Activation deactivation and uninstall methods
	 *
	 * @since WP Theater 1.0.0
	 */
	public static function activation() {

		// establish default settings
		if ( !get_option( 'wp_theater_options' ) ) {

			$val = array( 
				'load_css' => '1', 
				'load_js' => '1', 
				'load_genericons' => '1', 
				'cache_life' => 14400, 
				'show_activate_notice' => '1', 
			 );
			add_option( 'wp_theater_options', $val );
		}
	}
	public static function deactivation() {}
	public static function uninstall() {
		delete_option( 'wp_theater_options' );
	}

	/**
	 * Inits
	 * @since WP Theater 1.0.0
	 */
	public static function init () {

		register_activation_hook(   __FILE__, array( __CLASS__, 'activation'   ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );
		register_uninstall_hook(    __FILE__, array( __CLASS__, 'uninstall'    ) );

		if ( is_admin() ) {
			require_once( static::$inc . 'class-settings.php' );
			static::$settings = new WP_Theater_Settings();
		} else {
			require_once( static::$inc . 'class-presets.php' );
			require_once( static::$inc . 'class-shortcodes.php' );
			require_once( static::$inc . 'filters.php' );
			static::$presets = new WP_Theater_Presets();
			static::$shortcodes = new WP_Theater_Shortcodes();
		}
	}

	/**
	 * Single call for enqueueing(?) assets
	 * @since WP Theater 1.0.7
	 */
	public static function enqueue_assets () {
		static::enqueue_styles();
		static::enqueue_scripts();
	}

	/**
	 * Enqueues our styles
	 * @since WP Theater 1.0.0
	 */
	public static function enqueue_styles() {
		$options = get_option( 'wp_theater_options' );
		$load_css = ( isset( $options['load_css'] ) ) ? $options['load_css'] : '';

		if( (int) $load_css == 1 )
			wp_enqueue_style( 'wp_theater-styles', static::$uri . 'css/style-min.css', array(), '20130916' );

		// Add Genericons font, used in the main stylesheet IF it's not queued up already
		$load_gi = ( isset( $options['load_genericons'] ) ) ? $options['load_genericons'] : '';
		if ( (int) $load_gi == 1 && !wp_style_is( 'genericons', 'registered' ) && !wp_style_is( 'genericons', 'enqueued' ) )
			wp_enqueue_style( 'genericons', static::$uri . '/fonts/genericons.css', array(), '2.09' );
	}

	/**
	 * Enqueues our scripts
	 * @since WP Theater 1.0.0
	 */
	public static function enqueue_scripts() {
		$options = get_option( 'wp_theater_options' );
		$load_js = ( isset( $options['load_js'] ) ) ? $options['load_js'] : '';
		if( (int) $load_js == 1 )
			wp_enqueue_script( 'wp_theater-scripts', static::$uri . 'js/script-min.js', array( 'jquery' ), '20130916', TRUE );
	}

	/**
	 * Adds notice for admins to concider donating
	 * @since WP Theater 1.0.0
	 */
	//add_action( 'admin_notices', array( __CLASS__, 'admin_notices_donate' ) );
	public function admin_notices_donate() {
    ?>
    <div class="updated">
			<p>Thank you for using WP Theater.  For more details please visit <a href="http://redshiftstudio.com/wp-theater/" rel="author" title="WP Theater on Redshift Studio's website">redshiftstudio.com/wp-theater/</a></p>
			<p>We put a lot of time into this plugin and hope that you'd concider donating to support continued development.<br />
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X3FWTE2FBBTJU" target="_blank" rel="nofollow payment" title="Donate through PayPal"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal Donate" /></a>
			</p>
    </div>
    <?php
	}
  /**/

} /* END CLASS*/

} /* END EXISTS CHECK */

// define the plugin's location variables
WP_Theater::$dir = trailingslashit( plugin_dir_path( __FILE__ ) );
WP_Theater::$uri = trailingslashit( plugin_dir_url( __FILE__ ) );
WP_Theater::$inc = WP_Theater::$dir  .  trailingslashit( 'inc' );
//WP_Theater::$admin = WP_Theater::$dir . trailingslashit( 'admin' );
// start it up
WP_Theater::init();