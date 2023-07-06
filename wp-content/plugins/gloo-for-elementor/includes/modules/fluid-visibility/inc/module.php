<?php

namespace Gloo\Modules\Fluid_Visibility;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'fluid_visibility';

	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

	/**
	 * @var Settings
	 */
	public $settings = null;


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


		require gloo()->modules_path( 'fluid-visibility/inc/autoload.php' );


		require gloo()->modules_path( 'fluid-visibility/inc/settings.php' );

		$this->settings = new Settings();

		

		// \Gloo\Modules\Fluid_Visibility\Plugin::instance();
		
//		add_action( 'elementor/element/column/layout/after_section_end', [
//			$this,
//			'add_fluid_visibility_option'
//		], 30, 2 );
//		add_action( 'elementor/element/section/section_layout/after_section_end', [
//			$this,
//			'add_fluid_visibility_option'
//		], 10, 2 );

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
