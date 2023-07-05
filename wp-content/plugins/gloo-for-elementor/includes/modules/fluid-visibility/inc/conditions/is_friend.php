<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Is_Friend extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'bp-is-friend';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is User a Friend', 'gloo_for_elementor' );
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
		$is_friend = false;
		$target_id = $args['field'];

		if ( $target_id && bp_is_friend( $target_id ) == 'is_friend' ) {
			$is_friend = true;
		}

		if ( $inverse ) {
			return ! $is_friend;
		} else {
			return $is_friend;
		}

	}
	
	/**
	 * @return boolean Enable field for condition
	 */
	public function enable_field() {
		return true;
	}

	/**
	 * @return boolean Enable value for condition
	 */
	public function enable_value() {
		return false;
	}
}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Is_Friend() );
} );