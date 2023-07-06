<?php
namespace Gloo\Modules\Interactor\Conditions;

class Not_Contains extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'not-contains';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Doesn\'t contain', 'gloo_for_elementor' );
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

		$found = false;

		foreach ( $values as $value ) {
			if ( false !== strpos( $current_value, $value ) ) {
				$found = true;
			}
		}

		if ( 'hide' === $type ) {
			return $found;
		} else {
			return ! $found;
		}

	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Not_Contains() );
} );
