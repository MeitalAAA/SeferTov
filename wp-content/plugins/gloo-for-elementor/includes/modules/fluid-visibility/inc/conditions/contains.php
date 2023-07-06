<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Contains extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'contains';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Contains', 'gloo_for_elementor' );
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {
		$inverse = $args['inverse'] === 'yes';
		$values        = $this->explode_string( $args['value'] );
		$current_value = $this->get_current_value( $args );

		if ( $inverse ) {

			foreach ( $values as $value ) {
				if ( false !== strpos( $current_value, $value ) ) {
					return false;
				}
			}

			return true;

		} else {

			foreach ( $values as $value ) {
				if ( false !== strpos( $current_value, $value ) ) {
					return true;
				}
			}

			return false;

		}
	}
}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Contains() );
} );
