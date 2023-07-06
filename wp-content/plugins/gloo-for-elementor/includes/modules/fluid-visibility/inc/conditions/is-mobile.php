<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Is_Mobile extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'wp-is-mobile';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Is mobile device', 'gloo_for_elementor' );
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {
		$inverse = $args['inverse'] === 'yes';

		if ( $inverse ) {
			return ! wp_is_mobile();
		} else {
			return wp_is_mobile();
		}
	}

	/**
	 * @return boolean Enable field for condition
	 */
	public function enable_field() {
		return false;
	}

	/**
	 * @return boolean Enable value for condition
	 */
	public function enable_value() {
		return false;
	}
}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Is_Mobile() );
} );
