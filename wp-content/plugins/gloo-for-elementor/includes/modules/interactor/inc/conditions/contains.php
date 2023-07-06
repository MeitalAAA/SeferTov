<?php
namespace Gloo\Modules\Interactor\Conditions;

class Contains extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'contains';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Contains', 'gloo_for_elementor' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';
		$values        = $this->explode_string( $args['condition_value'] );
		$current_value = $this->get_current_value( $args );

		if ( 'hide' === $type ) {

			foreach ( $values as $value ) {
				if ( false !== strpos( $current_value, $value ) ) {
					return false;
				}
			}

			return true;

		} else {

			foreach ( $values as $value ) {
				if ( false !== strpos( $current_value, $value ) ) {
					return true;
				}
			}

			return false;

		}

	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Contains() );
} );
