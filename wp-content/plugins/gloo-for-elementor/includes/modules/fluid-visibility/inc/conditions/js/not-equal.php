<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions\JS;

class Not_Equal extends Base {

	public $condition_operator = '!=';

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'js_not_equal';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'Not Equal', 'gloo_for_elementor' );
	}

	public function get_group() {
		return 'js';
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {
		return true;
	}

	public function get_js( $args = array() ) {

		$value = $args['value'];
		$args['logic'] = "value != '$value'";
		return $this->generate_js($args);

	}


}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Not_Equal() );
} );
