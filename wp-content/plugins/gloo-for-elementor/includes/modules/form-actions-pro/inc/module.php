<?php

namespace Gloo\Modules\Form_Actions_Pro;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'form_actions_pro';

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


		require gloo()->modules_path( 'form-actions-pro/inc/autoload.php' );
		

		\Gloo\Modules\Form_Actions_Pro\Plugin::instance();
		
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
