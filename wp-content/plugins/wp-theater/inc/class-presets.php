<?php 
if(class_exists('WP_Theater') && !class_exists('WP_Theater_Presets')){

class WP_Theater_Presets {

	/**
	 * The shortcode's registered presets
	 * @since WP Theater 1.0.0
	 */
	protected $presets = array();

	/**
	 * Constructor
	 * @since WP Theater 1.0.0
	 */
	public function WP_Theater_Presets () { __construct(); }

	/**
	 * Constructs
	 * @since WP Theater 1.0.0
	 */
	public function __construct () {
	}

	/**
	 * Determines if a preset exists with a given name
	 * @since WP Theater 1.0.0
	 *
	 * @param string $name The preset's unique name
	 *
	 * @return bool TRUE if the preset exists, FALSE if not.
	 */
	public function has_preset ( $name ) { return isset( $this->presets[$name] ); }

	/**
	 * Get all presets for the media shortcode
	 * @since WP Theater 1.0.0
	 *
	 * @return array An array of the presets
	 */
	public function get_presets () { return $this->presets; }

	/**
	 * Get a single preset's array of attributes
	 * @since WP Theater 1.0.0
	 *
	 * @param string $name The preset's unique name
	 *
	 * @return array A single preset's array.
	 */
	public function get_preset ( $name ) { return apply_filters( 'wp_theater-get_preset', $this->presets[$name] ); }

	/**
	 * Set/Add/Update the value for a given preset name
	 * @since WP Theater 1.0.0
	 *
	 * @param string $name The preset's unique name
	 * @param array $arr The preset's unique name
	 */
	public function set_preset ( $name, $arr ) { $this->presets[$name] = apply_filters( 'wp_theater-set_preset', $arr, $name ); }

	/**
	 * Get the array of preset attributes for the media shortcode
	 * @since WP Theater 1.0.0
	 *
	 * @param string $name The preset's unique name
	 */
	public function delete_preset ( $name ) { unset( $this->presets[$name] ); }
	/**/
	// save_preset ();
	// load_preset ();

} // END CLASS
} // END EXISTS CHECK