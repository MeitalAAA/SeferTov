<?php

namespace Gloo\Modules\JEDV_LD;

class User_Not_Enrolled_Course extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'gloo-dash-user-not-enrolled-course';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'User Is Not Enrolled In Course', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'LearnDash';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function check( $args = array() ) {


		$type       = ! empty( $args['type'] ) ? $args['type'] : 'show';
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

		if ( 'hide' === $type ) {
			return $is_subscribed;
		} else {
			return !$is_subscribed;
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
