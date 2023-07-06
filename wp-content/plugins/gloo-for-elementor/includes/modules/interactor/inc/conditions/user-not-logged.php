<?php
namespace Gloo\Modules\Interactor\Conditions;

class User_Not_Logged extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user-not-logged';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User not logged in', 'gloo_for_elementor' );
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
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';

		if ( 'hide' === $type ) {
			return is_user_logged_in();
		} else {
			return ! is_user_logged_in();
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new User_Not_Logged() );
} );
