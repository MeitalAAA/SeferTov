<?php

namespace Gloo\Modules\Dynamic_Tags_Everywhere;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	public $controls = [];

	public $slug = 'dynamic_tags_everywhere';

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
		$this->require_new_control_manger();
		$this->set_new_control_manger();
	}

	public function set_new_control_manger() {
		add_action( 'elementor/init', function () {
			if ( isset( $this->controls['gloo_controls_manager'] ) ) {
				\Elementor\Plugin::$instance->controls_manager = $this->controls['gloo_controls_manager'];
			}
		}, 0 // priority
		);
	}

	public function require_new_control_manger() {
		$gloo_controls_manager = gloo()->modules_path( 'dynamic-tags-everywhere/inc/gloo_controls_manager.php' );
		require $gloo_controls_manager;
		$name                                           = pathinfo( $gloo_controls_manager, PATHINFO_FILENAME );
		$class                                          = '\\Elementor\\' . $name;
		$this->controls[ strtolower( $name ) ] = new $class( \Elementor\Plugin::$instance->controls_manager );
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
