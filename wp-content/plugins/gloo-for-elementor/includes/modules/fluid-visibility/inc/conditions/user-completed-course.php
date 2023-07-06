<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class User_Completed_Course extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'gloo-dash-user-completed-course';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'User Completed Course', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'learndash';
	}

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	public function evaluate( $args = array() ) {

		$inverse = $args['inverse'] === 'yes';
		$course_ids = $args['field'];
		$completed = false;

		if ( function_exists( 'learndash_user_get_course_completed_date' ) ) {
			if ( $course_ids ) {

				$course_ids = explode( ',', $course_ids );
				foreach ( $course_ids as $course_id ) {
					if ( learndash_user_get_course_completed_date( get_current_user_id(), $course_id ) ) {
						$completed = true;
					} else {
						$completed = false;
						break;
					}
				}
			}
		}

		if ( $inverse ) {
			return ! $completed;
		} else {
			return $completed;
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
	$manager->register_condition( new User_Completed_Course() );
} );