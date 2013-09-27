<?php 
if(class_exists('WP_Theater') && !class_exists('WP_Theater_Settings')){

class WP_Theater_Settings {

	/**
	 * Version constant
	 * @since WP Theater 1.0.0
	 */
	protected $page = '';

	/**
	 * Constructor
	 * @since WP Theater 1.0.0
	 */
	public function WP_Theater_Settings () { __construct(); }

	/**
	 * Constructs
	 * @since WP Theater 1.0.0
	 */
	public function __construct () {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) ); //menu setup
		add_filter( 'plugin_action_links_wp-theater', array( $this, 'plugin_action_links' ), 2, 2 );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/* register menu item */
	public function admin_menu(){
		$this->page = add_options_page(
			'WP Theater Settings',
			'WP Theater',
			'manage_options',
			'wp_theater',
			array($this, 'options_screen')
		);
	}

	/* display page content */
	public function options_screen() {
		global $submenu;
		// access page settings 

		// output 
		?>
		<div class="wrap">
			<?php screen_icon();?>
			<h2>WP Theater Settings</h2>
			<form id="wpmsc_options" action="options.php" method="post">
				<?php
				settings_fields('wp_theater_options_group');
				do_settings_sections('wp_theater'); 
				submit_button('Save', 'primary', 'wp_theater_options_submit');
				?>
			</form>
		</div>
		<?php
	}

	/* settings link in plugin management screen */
	public function plugin_action_links($actions, $file) {
		if(false !== strpos($file, 'wp_theater'))
			$actions['settings'] = '<a href="options-general.php?page=wp_theater">Settings</a>';

		return $actions; 
	}

	/* register settings */
	public function settings_init(){

		register_setting(
			'wp_theater_options_group',
			'wp_theater_options',
			array( $this, 'options_validate' )
		);

		add_settings_section(
			'wpts_genset',
			'General Settings', 
			array( $this, 'wpts_genset_desc' ),
			'wp_theater'
		);

		add_settings_field(
			'wpts_genset_load_css',
			'Load Default CSS?', 
			array( $this, 'default_css_fields' ),
			'wp_theater',
			'wpts_genset'
		);

		add_settings_field(
			'wpts_genset_load_gi',
			'Load Genericons?<br/><em>if not already loaded</em>', 
			array( $this, 'default_genericons_fields' ),
			'wp_theater',
			'wpts_genset'
		);

		add_settings_field(
			'wpts_genset_load_js',
			'Load Default Javascript?', 
			array( $this, 'default_js_fields' ),
			'wp_theater',
			'wpts_genset'
		);

		add_settings_field(
			'wpts_genset_cache_life',
			'Cache Expiration?<br/><em>in seconds</em>', 
			array( $this, 'default_cache_fields' ),
			'wp_theater',
			'wpts_genset'
		);
	}

	/* validate input */
	public function options_validate($input){
		if(isset($input['load_css']))
			$input['load_css'] = (int) $input['load_css'];
		if(isset($input['load_js']))
			$input['load_js'] = (int) $input['load_js'];
		if(isset($input['load_genericons']))
			$input['load_genericons'] = (int) $input['load_genericons'];
		if(isset($input['cache_life']))
			$input['cache_life'] = (int) $input['cache_life'];
		return $input;
	}

	/* description text */
	public function wpts_genset_desc(){
		echo '<p></p>';
	}

	/* filed output */
	public function default_css_fields() {
		$options = get_option('wp_theater_options');
		$load_css = (isset($options['load_css'])) ? $options['load_css'] : '';
		$load_css = (int) $load_css; //sanitise output
		echo '<input type="checkbox" id="load_css" name="wp_theater_options[load_css]" value="1" ' . checked( 1, $load_css, false ) . '>';
	}

	/* filed output */
	public function default_genericons_fields() {
		$options = get_option('wp_theater_options');
		$load_gi = (isset($options['load_genericons'])) ? $options['load_genericons'] : '';
		$load_gi = (int) $load_gi; //sanitise output
		echo '<input type="checkbox" id="load_genericons" name="wp_theater_options[load_genericons]" value="1" ' . checked( 1, $load_gi, false ) . '>';
	}

	/* filed output */
	public function default_js_fields() {
		$options = get_option('wp_theater_options');
		$load_js = (isset($options['load_js'])) ? $options['load_js'] : '';
		$load_js = (int) $load_js; //sanitise output
		echo '<input type="checkbox" id="load_js" name="wp_theater_options[load_js]" value="1" ' . checked( 1, $load_js, false ) . '>';
	}

	/* filed output */
	public function default_cache_fields() {
		$options = get_option('wp_theater_options');
		$cache_life = (isset($options['cache_life'])) ? $options['cache_life'] : '';
		$cache_life = (int) $cache_life; //sanitise output
		echo '<input type="number" id="cache_life" name="wp_theater_options[cache_life]" class="small-text" min="0" step="1" value="' . $cache_life . '">';
	}
	/**/

} // END CLASS
} // END EXISTS CHECK