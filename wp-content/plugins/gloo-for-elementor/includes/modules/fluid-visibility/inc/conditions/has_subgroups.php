<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Has_Subgroups extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'bp-group-has-subgroups';
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
		return 'buddypress';
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {

		$result = false;
		$inverse = $args['inverse'] === 'yes';
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
	$manager->register_condition( new Has_Subgroups() );
} );