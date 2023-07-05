<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Archive_Tax extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'archive-tax';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Taxonomy Archive', 'gloo_for_elementor' );
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
		$tax  = $this->explode_string( $args['value'] );

		if ( in_array( 'category', $tax ) && 'post' === get_post_type() ) {
			$result = is_category();
		} elseif ( in_array( 'post_tag', $tax ) && 'post' === get_post_type() ) {
			$result = is_tag();
		} else {
			$result = is_tax( $tax );
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
	$manager->register_condition( new Archive_Tax() );
} );
