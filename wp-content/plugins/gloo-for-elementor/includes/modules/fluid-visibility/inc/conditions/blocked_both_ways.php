<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Blocked_Both_Ways extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'blocked-both-ways';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Blocked Both Ways', 'gloo_for_elementor' );
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

		global $wpdb;

		$blocked      = false;
		$inverse = $args['inverse'] === 'yes';
		$target_id    = $args['field'];
		$current_user = get_current_user_id();

		if ( $current_user && $target_id ) {
			$is_user_blocked = $wpdb->get_var( $wpdb->prepare( "SELECT `target_id` FROM {$wpdb->base_prefix}bp_block_member WHERE (`user_id` = %d AND `target_id` = %d) OR (`user_id` = %d AND `target_id` = %d)", $current_user, $target_id, $target_id, $current_user ) );
			if ( $is_user_blocked ) {
				$blocked = true;
			}
		}

		if ( $inverse ) {
			return ! $blocked;
		} else {
			return $blocked;
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
	$manager->register_condition( new Blocked_Both_Ways() );
} );