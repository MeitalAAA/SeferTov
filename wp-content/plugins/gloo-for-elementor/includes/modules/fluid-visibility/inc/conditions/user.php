<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class User extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User logged in', 'gloo_for_elementor' );
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
		
		if ( $inverse ) {
			return ! is_user_logged_in();
		} else {
			return is_user_logged_in();
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
	$manager->register_condition( new User() );
} );
