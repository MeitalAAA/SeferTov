<?php
namespace Gloo\Modules\Dynamic_Visibility_Wishlist;

class User_Wishlist_Course extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'gloo-wishlist-user-role';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'User and Course Wishlist role is', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'Wishlist';
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
		$levels = $args['value'];
        $completed = false;

        if($levels) {
            $completed = true;
        }   
    
        // echo '<pre>';
        // // print_r($user_levels);
        // print_r($levels);
        // echo '</pre>';

		if ( 'hide' === $type ) {
			return ! $completed;
		} else {
			return $completed;
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
		return true;
	}

}
