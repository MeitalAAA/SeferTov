<?php

namespace Gloo\Modules\JEDV_LD;

use Elementor\Controls_Manager;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'jedv_ld';

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


		add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', [ $this, 'register_conditions' ] );

		$callback = array( $this, 'add_control_heading' );

		add_action( 'elementor/element/column/section_advanced/after_section_end', $callback, 999, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', $callback, 999, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', $callback, 999, 2 );

	}

	public function register_conditions( $conditions_manager ) {


		foreach ( glob( gloo()->modules_path( 'jedv-ld/inc/conditions/*.php' ) ) as $file ) {

			require_once $file;
			$class = basename( $file, '.php' );
			$class = ucwords( str_replace( '-', ' ', $class ) );
			$class = str_replace( ' ', '_', $class );
			$class = "Gloo\Modules\JEDV_LD\\{$class}";

			if ( class_exists( $class ) ) {
				$conditions_manager->register_condition( new $class );
			}
		}


	}

	public function add_control_heading( $element, $section_id ) {

		$control_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), 'jedv_conditions' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$args = array(
			'jedv_course_text' => [
				'type'      => \Elementor\Controls_Manager::HEADING,
				'label'     => 'Enter Course ID (or comma separated multiple IDs) Example : 22 , 55 , 33 , 88',
				'name'      => 'jedv_course_text',
				'condition' => array(
					'jedv_condition' => array( 'gloo-dash-is-subscribed' ),
				),
			]
		);

		$control_data['fields'] = $this->array_insert_after( $control_data['fields'], 'jedv_condition', $args );

		// And then just update the control in the stack/element
		$element->update_control( 'jedv_conditions', $control_data );

	}

	function array_insert_after( array $array, $key, array $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
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