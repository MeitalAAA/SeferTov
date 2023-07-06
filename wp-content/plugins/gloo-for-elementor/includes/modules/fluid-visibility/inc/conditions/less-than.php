<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Less_Than extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'less-than';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Less than', 'gloo_for_elementor' );
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {

		$inverse = $args['inverse'] === 'yes';
		$data_type     = ! empty( $args['data_type'] ) ? $args['data_type'] : 'chars';
		$current_value = $this->get_current_value( $args );
		$value         = $args['value'];
		$values        = $this->adjust_type( $current_value, $value, $data_type );

		if ( $inverse ) {
			return $values['current'] >= $values['compare'];
		} else {
			return $values['current'] < $values['compare'];
		}
	}

	/**
	 * This condition is required data type detection
	 *
	 * @return boolean [description]
	 */
	public function enable_data_type() {
		return true;
	}

}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Less_Than() );
} );
