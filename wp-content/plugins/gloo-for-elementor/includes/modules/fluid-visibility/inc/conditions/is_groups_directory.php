<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Is_Groups_Directory extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'bp-is-group-directory';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is Groups Directory Page', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'buddypress';
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {

		$inverse = $args['inverse'] === 'yes';
		$value = bp_is_groups_directory();
        
		if ( $inverse ) {
			return ! $value;
		} else {
			return $value;
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
	$manager->register_condition( new Is_Groups_Directory() );
} );