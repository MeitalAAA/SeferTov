<?php

namespace Gloo\Modules\Fluid_Dynamics;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	public $control_overrides = [];

	public $slug = 'fluid_dynamics';

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

//		require gloo()->modules_path( 'fluid-dynamics/inc/settings.php' );
//		new Settings();


		// Include Custom Control Manager
		$this->set_control_manager();

		// Override Controls Manager
		add_action( 'elementor/init', function () {
			if ( isset( $this->control_overrides['gloo_controls_manager'] ) ) {
				\Elementor\Plugin::$instance->controls_manager = $this->control_overrides['gloo_controls_manager'];
			}
		}, 0 );

	}

	public function set_control_manager() {

		$gloo_controls_manager = gloo()->modules_path( 'fluid-dynamics/inc/gloo_controls_manager.php' );
		require $gloo_controls_manager;
		$name                                           = pathinfo( $gloo_controls_manager, PATHINFO_FILENAME );
		$class                                          = '\\Elementor\\' . $name;
		$controls_manager                               = \Elementor\Plugin::$instance->controls_manager;
		$this->control_overrides[ strtolower( $name ) ] = new $class( $controls_manager );

//		$overrides = glob( gloo()->modules_path( 'fluid-dynamics/inc/gloo_controls_manager.php' ) );
//		// include all classes
//		foreach ( $overrides as $key => $value ) {
//			require_once $value;
//		}
		// instance all classes
//		foreach ( $overrides as $key => $value ) {
//			$name  = pathinfo( $value, PATHINFO_FILENAME );
//			$class = '\\Elementor\\' . $name;
//
//			switch ( $name ) {
//				case 'DCE_Widgets_Manager':
//					//$_widget_types = \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
//					$this->control_overrides[ strtolower( $name ) ] = new $class( array() );
//					break;
//				case 'Gloo_Controls_Manager':
//					$controls_manager                               = \Elementor\Plugin::$instance->controls_manager;
//					$this->control_overrides[ strtolower( $name ) ] = new $class( $controls_manager );
//					break;
//				default:
//					$this->control_overrides[ strtolower( $name ) ] = new $class();
//			}
//		}

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
