<?php

namespace Elementor;

use \Elementor\Controls_Manager;

/**
 * Description of Gloo_Controls_Manager
 */
class Gloo_Controls_Manager extends Controls_Manager {

	public static function get_control_types() {
		return apply_filters( 'gloo/fluid_dynamics/dynamic_tag_control_types', [
			Controls_Manager::TEXT,
			Controls_Manager::TEXTAREA,
			Controls_Manager::WYSIWYG,
			Controls_Manager::NUMBER,
			Controls_Manager::URL,
			Controls_Manager::COLOR,
			Controls_Manager::SLIDER,
			Controls_Manager::MEDIA,
			Controls_Manager::GALLERY,
		] );
	}


	// get init data from original control_manager
	public function _clone_controls_manager( $controls_manager ) {

		$controls = $controls_manager->get_controls();
		foreach ( $controls as $key => $value ) {
			$this->controls[ $key ] = $value;
		}

		$control_groups = $controls_manager->get_control_groups();
		foreach ( $control_groups as $key => $value ) {
			$this->control_groups[ $key ] = $value;
		}

		$this->stacks = $controls_manager->get_stacks();
		$this->tabs   = $controls_manager::get_tabs();
	}

	public $excluded_extensions = array();

	public function set_excluded_extensions( $extensions ) {
		$this->excluded_extensions = $extensions;
	}

	/**
	 * Add control to stack.
	 *
	 * This method adds a new control to the stack.
	 *
	 * @param Controls_Stack $element Element stack.
	 * @param string $control_id Control ID.
	 * @param array $control_data Control data.
	 * @param array $options Optional. Control additional options.
	 *                                     Default is an empty array.
	 *
	 * @return bool True if control added, False otherwise.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function add_control_to_stack( Controls_Stack $element, $control_id, $control_data, $options = [] ) {

		if ( ! in_array( $element->get_name(), array( 'popup_triggers', 'popup_timing' ) ) ) {
			$control_data = self::_add_dynamic_tags( $control_data );
		}

		return parent::add_control_to_stack( $element, $control_id, $control_data, $options );
	}

	public static function _add_dynamic_tags( $control_data ) {
		if ( ! empty( $control_data ) ) {
			foreach ( $control_data as $key => $control ) {
				if ( $key != 'dynamic' ) {
					if ( is_array( $control ) ) {
						$control_data[ $key ] = self::_add_dynamic_tags( $control );
					}
				}
			}
		}
		if ( isset( $control_data['type'] ) && ! is_array( $control_data['type'] ) ) {
			$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control_data['type'] );
			if ( $control_obj ) {
				$dynamic_settings = $control_obj->get_settings( 'dynamic' );
				if ( ! empty( $dynamic_settings ) ) {
					if ( in_array( $control_data['type'], self::get_control_types() ) ) {
						if ( ! isset( $control_data['dynamic'] ) ) {
							$control_data['dynamic']['active'] = true;
						} else {
							if ( isset( $control_data['dynamic']['active'] ) ) {
								// natively
								if ( ! $control_data['dynamic']['active'] ) {
									$control_data['dynamic']['active'] = true;
								}
							} // active => false
						}
					}
				}
			}
		}

		return $control_data;
	}


}
