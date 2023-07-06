<?php

namespace Gloo\Modules\JEDV_BP;


class Is_Friend extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jet-otw-is-friend';
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

		$is_friend = false;
		$type      = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$target_id = $args['field'];

		if ( $target_id && bp_is_friend( $target_id ) == 'is_friend' ) {
			$is_friend = true;
		}

		if ( 'hide' === $type ) {
			return ! $is_friend;
		} else {
			return $is_friend;
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
