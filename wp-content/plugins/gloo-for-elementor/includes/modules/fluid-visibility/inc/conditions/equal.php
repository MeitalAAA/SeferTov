<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Equal extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'equal';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Equal', 'gloo_for_elementor' );
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {
		
		$inverse = $args['inverse'] === 'yes';
		
		$current_value = $this->get_current_value( $args );
		
		if ($inverse ) {
			return $current_value != $args['value'];
		} else {
			return $current_value == $args['value'];
		}

	}

}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Equal() );
} );
