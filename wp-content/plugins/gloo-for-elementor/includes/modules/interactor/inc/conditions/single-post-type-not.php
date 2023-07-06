<?php
namespace Gloo\Modules\Interactor\Conditions;

class Single_Post_Type_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'single-post-type-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Single Post Type is not', 'gloo_for_elementor' );
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
	 * @param  array $args
	 * @return bool
	 */
	public function check( $args = array() ) {

		$type       = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';
		$post_types = $this->explode_string( $args['condition_value'] );

		if ( 'hide' === $type ) {
			return is_singular( $post_types );
		} else {
			return ! is_singular( $post_types );
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

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Single_Post_Type_Not() );
} );
