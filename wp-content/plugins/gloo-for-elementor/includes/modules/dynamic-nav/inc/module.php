<?php

namespace Gloo\Modules\Dynamic_Nav;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'dynamic_nav';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		// requires elementor pro
		if(!gloo()->has_elementor_pro()){
			return;
		}
		
		require gloo()->modules_path( 'dynamic-nav/inc/functions.php' );
		require gloo()->modules_path( 'dynamic-nav/inc/plugin.php' );
		
		//require gloo()->modules_path( 'dynamic-nav/inc/settings.php' );
		//new Settings();

	}


	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
