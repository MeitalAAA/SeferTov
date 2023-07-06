<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Archive_Post_Type extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'archive-post-type';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Post Type Archive', 'gloo_for_elementor' );
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
		$post_types = $this->explode_string( $args['value'] );

		if ( in_array( 'post', $post_types ) && 'post' === get_post_type() ) {
			$result = is_archive() || is_home();
		} else {
			$result = is_post_type_archive( $post_types ) || ( is_tax() && in_array( get_post_type(), $post_types ) );
		}

		if ( $inverse ) {
			return ! $result;
		} else {
			return $result;
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
	$manager->register_condition( new Archive_Post_Type() );
} );
