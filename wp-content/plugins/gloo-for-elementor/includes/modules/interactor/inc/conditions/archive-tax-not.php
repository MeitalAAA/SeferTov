<?php
namespace Gloo\Modules\Interactor\Conditions;

class Archive_Tax_Not extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'archive-tax-not';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Taxonomy Archive is not', 'gloo_for_elementor' );
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
		$tax  = $this->explode_string( $args['condition_value'] );

		if ( in_array( 'category', $tax ) && 'post' === get_post_type() ) {
			$result = is_category();
		} elseif ( in_array( 'post_tag', $tax ) && 'post' === get_post_type() ) {
			$result = is_tag();
		} else {
			$result = is_tax( $tax );
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
	$manager->register_condition( new Archive_Tax_Not() );
} );
