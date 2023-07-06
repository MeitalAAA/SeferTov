<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class In_List extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'in-list';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'In the list', 'gloo_for_elementor' );
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

		if ( is_array( $current_value ) ) {

			if ( in_array( 'true', $current_value ) || in_array( 'false', $current_value ) ) {
				$current_value = $this->checkboxes_to_array( $current_value );
			}

			if ( empty( $current_value ) ) {
				if ( $inverse ) {
					return true;
				} else {
					return false;
				}
			}

			$found = false;

			foreach ( $current_value as $value ) {
				if ( in_array( $value, $values ) ) {
					$found = true;
				}
			}

			if ( $inverse ) {
				return ! $found;
			} else {
				return $found;
			}

		} else {
			if ( $inverse ) {
				return ! in_array( $current_value, $values );
			} else {
				return in_array( $current_value, $values );
			}
		}
	}
}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new In_List() );
} );
