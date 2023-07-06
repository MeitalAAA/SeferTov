<?php
namespace Gloo\Modules\Interactor\Conditions;

class Archive_Post_Type_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-post-type-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Post Type Archive is not', 'gloo_for_elementor' );
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

		if ( in_array( 'post', $post_types ) && 'post' === get_post_type() ) {
			$result = is_archive() || is_home();
		} else {
			$result = is_post_type_archive( $post_types ) || ( is_tax() && in_array( get_post_type(), $post_types ) );
		}

		if ( 'hide' === $type ) {
			return $result;
		} else {
			return ! $result;
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
	$manager->register_condition( new Archive_Post_Type_Not() );
} );
