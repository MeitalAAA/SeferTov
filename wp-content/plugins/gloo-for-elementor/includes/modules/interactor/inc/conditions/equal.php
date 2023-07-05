<?php

namespace Gloo\Modules\Interactor\Conditions;

class Equal extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'equal';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Equal', 'gloo_for_elementor' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';
		$current_value = $this->get_current_value( $args );

		if ( 'hide' === $type ) {
			return $current_value != $args['condition_value'];
		} else {
			return $current_value == $args['condition_value'];
		}

	}

}

add_action( 'gloo/modules/interactor/conditions/register', function ( $manager ) {
	$manager->register_condition( new Equal() );
} );
