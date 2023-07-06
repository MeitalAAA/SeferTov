<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Single_Post_Status extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'single-post-status';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Single Post Status', 'gloo_for_elementor' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'posts';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function evaluate( $args = array() ) {

		$inverse = $args['inverse'] === 'yes';
		$type       = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$post_status = $args['value'];
		
		
		if(function_exists('jet_engine')) {
			$post_id = jet_engine()->listings->data->get_current_object_id();
		} else {
			$post_id = get_the_ID();
		}

		$current_post_status = get_post_status( $post_id );
		$output = ($post_status == $current_post_status) ? true : false;
		
		if ( $inverse ) {
			return ! $output;
		} else {
			return $output;
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
		return true;
	}

}

add_action( 'gloo/modules/fluid_visibility/conditions/register', function ( $manager ) {
	$manager->register_condition( new Single_Post_Status() );
} );
