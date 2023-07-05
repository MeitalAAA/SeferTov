<?php

namespace OTW_Custom_Code;

class Is_Not_Blocked extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jet-otw-is-not-blocked';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is User Not Blocked', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'BuddyPress';
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

		$not_blocked      = true;
		$type         = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$target_id    = $args['field'];
		$current_user = get_current_user_id();
		if ( $current_user && $target_id ) {
			$is_user_blocked = $wpdb->get_var( $wpdb->prepare( "SELECT `target_id` FROM {$wpdb->base_prefix}bp_block_member WHERE `user_id` = %d AND `target_id` = %d", $current_user , $target_id) );
			if ( $is_user_blocked ) {
				$not_blocked = false;
			}
		}

		if ( 'hide' === $type ) {
			return ! $not_blocked;
		} else {
			return $not_blocked;
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
