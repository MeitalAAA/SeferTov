<?php

namespace Gloo\Modules\JEDV_BP;


class Has_Subgroups extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'jedv-bp-group-has-subgroups';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Group Has Subgroups', 'gloo_for_elementor' );
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

		$result = false;

		// not in a group
		if ( ! bp_is_group() ) {
			return $result;
		}

		$group_id = bp_get_group_id();

		// could not get current group id
		if ( ! $group_id ) {
			return $result;
		}

		$sub_groups = groups_get_groups(
			array(
				'parent_id' => $group_id,
				'fields'    => 'ids',
				'per_page'  => 1,
			)
		);

		$value = isset( $sub_groups['groups'] ) && $sub_groups['groups'];

		if ( 'hide' === $type ) {
			return ! $value;
		} else {
			return $value;
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean
	 */
	public function is_for_fields() {
		return false;
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
