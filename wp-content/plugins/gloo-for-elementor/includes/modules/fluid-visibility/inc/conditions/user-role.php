<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class User_Role extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	public function get_id() {
		return 'user-role';
	}

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	public function get_name() {
		return __( 'User Role', 'gloo_for_elementor' );
	}

	/**
	 * Returns condition group
	 *
	 * @return string group
	 */
	public function get_group() {
		return 'user';
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {

		$roles   = ! empty( $args['user_role'] ) ? $args['user_role'] : array();
		$inverse = $args['inverse'] === 'yes';

		if ( ! $inverse ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();

				foreach ( $roles as $role ) {
					if ( in_array( $role, (array) $user->roles ) ) {
						return true;
					}
				}
			}

			return false;
		} else {
			$user = wp_get_current_user();

			foreach ( $roles as $role ) {
				if ( in_array( $role, (array) $user->roles ) ) {
					return false;
				}
			}

			return true;
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
	$manager->register_condition( new User_Role() );
} );
