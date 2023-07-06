<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Single_Post_Type extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'single-post-type';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Single Post Type', 'gloo_for_elementor' );
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
		$post_types = $this->explode_string( $args['value'] );

		if ( $inverse ) {
			return ! is_singular( $post_types );
		} else {
			return is_singular( $post_types );
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
	$manager->register_condition( new Single_Post_Type() );
} );
