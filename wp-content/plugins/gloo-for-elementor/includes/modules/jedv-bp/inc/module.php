<?php

namespace Gloo\Modules\JEDV_BP;

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

	public $slug = 'jedv_bp';

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

        add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', [ $this, 'register_conditions']);

    }

    public function register_conditions($conditions_manager){

		foreach ( glob( gloo()->modules_path( 'jedv-bp/inc/conditions/*.php' ) ) as $file ) {

			require_once $file;
			$class = basename( $file, '.php' );
			$class = ucwords( str_replace( '-', ' ', $class ) );
			$class = str_replace( ' ', '_', $class );
            $class = "Gloo\Modules\JEDV_BP\\{$class}";

            if ( class_exists( $class ) ) {
                $conditions_manager->register_condition( new $class );
            }
		}
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
