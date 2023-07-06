<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Post_Has_Terms extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'post-has-terms';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Post Has Terms', 'gloo_for_elementor' );
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
		$terms   = $this->explode_string( $args['value'] );
		$tax   = ! empty( $args['condition_terms_taxonomy'] ) ? $args['condition_terms_taxonomy'] :'';

		if(function_exists('jet_engine')) {
 			$post_id = jet_engine()->listings->data->get_current_object_id();
		} else {
			$post_id = get_the_ID();
		}

		if ( $inverse ) {
			return ! has_term( $terms, $tax, $post_id );
		} else {
			return has_term( $terms, $tax, $post_id );
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
	$manager->register_condition( new Post_Has_Terms() );
} );
