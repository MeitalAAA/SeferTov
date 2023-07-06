<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Post_Author extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'post-author';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Current Post Author', 'gloo_for_elementor' );
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
		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( $inverse ) {
			return get_the_author_meta( 'ID' ) !== get_current_user_id();
		} else {
			return get_the_author_meta( 'ID' ) === get_current_user_id();
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
	$manager->register_condition( new Post_Author() );
} );
