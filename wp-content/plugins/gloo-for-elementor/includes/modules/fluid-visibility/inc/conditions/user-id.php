<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class User_ID extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user-id';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User ID', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'user';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function evaluate( $args = array() ) {

		$inverse = $args['inverse'] === 'yes';

		$user_ids   = $this->explode_string( $args['user_id'] );
		$current_id = get_current_user_id();

		if ( $inverse ) {
			return ! in_array( $current_id, $user_ids );
		} else {
			return in_array( $current_id, $user_ids );
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
	$manager->register_condition( new User_ID() );
} );
