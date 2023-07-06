<?php

namespace OTW_Custom_Code;

class Blocked_Both_Ways extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jet-otw-blocked-both-ways';
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
		return 'user';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function check( $args = array() ) {

		global $wpdb;

		$blocked      = false;
		$type         = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$target_id    = $args['field'];
		$current_user = get_current_user_id();

		if ( $current_user && $target_id ) {
			$is_user_blocked = $wpdb->get_var( $wpdb->prepare( "SELECT `target_id` FROM {$wpdb->base_prefix}bp_block_member WHERE (`user_id` = %d AND `target_id` = %d) OR (`user_id` = %d AND `target_id` = %d)", $current_user, $target_id, $target_id, $current_user ) );
			if ( $is_user_blocked ) {
				$blocked = true;
			}
		}

		if ( 'hide' === $type ) {
			return ! $blocked;
		} else {
			return $blocked;
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean
	 */
	public function is_for_fields() {
		return true;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean
	 */
	public function need_value_detect() {
		return false;
	}

}
