<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Exists extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'exists';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Exists', 'gloo_for_elementor' );
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {
		$inverse = $args['inverse'] === 'yes';
		$current_value = $this->get_current_value( $args );

		if ( $inverse ) {
			return empty( $current_value );
		} else {
			return ! empty( $current_value );
		}
	}

	/**
	* @return boolean Enable value for condition
	*/
	public function enable_value() {
		return false;
	}

}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Exists() );
} );
