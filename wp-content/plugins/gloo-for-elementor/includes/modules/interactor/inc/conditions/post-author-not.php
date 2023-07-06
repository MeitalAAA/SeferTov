<?php
namespace Gloo\Modules\Interactor\Conditions;

class Post_Author_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'post-author-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Is Not Current Post Author', 'gloo_for_elementor' );
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

		$type = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';

		if ( 'hide' === $type ) {
			return get_the_author_meta( 'ID' ) === get_current_user_id();
		} else {
			return get_the_author_meta( 'ID' ) !== get_current_user_id();
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
		return false;
	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Post_Author_Not() );
} );
