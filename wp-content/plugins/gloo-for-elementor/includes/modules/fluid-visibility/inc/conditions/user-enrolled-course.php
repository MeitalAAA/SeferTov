<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class User_Enrolled_Course extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'gloo-dash-user-enrolled-course';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'User Is Enrolled In Course', 'gloo_for_elementor' );
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

		$is_subscribed = false;

		if ( function_exists( 'learndash_user_get_enrolled_courses' ) ) {
			$user_course_ids = learndash_user_get_enrolled_courses( get_current_user_id() );
		}

		if ( $course_ids && ! empty( $user_course_ids ) && is_array( $user_course_ids ) ) {

			$course_ids = explode( ',', $course_ids );
			foreach ( $course_ids as $course_id ) {
				if ( in_array( $course_id, $user_course_ids ) ) {
					$is_subscribed = true;
				} else {
					$is_subscribed = false;
					break;
				}
			}
		}

		if ( $inverse ) {
			return ! $is_subscribed;
		} else {
			return $is_subscribed;
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
	$manager->register_condition( new User_Enrolled_Course() );
} );