<?php
namespace Gloo\Modules\Interactor\Conditions;

class Not_Exists extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'not-exists';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Doesn\'t exist', 'gloo_for_elementor' );
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
			return ! empty( $current_value );
		} else {
			return empty( $current_value );
		}

	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Not_Exists() );
} );
