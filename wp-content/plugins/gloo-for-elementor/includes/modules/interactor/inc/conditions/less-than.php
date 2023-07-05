<?php
namespace Gloo\Modules\Interactor\Conditions;

class Less_Than extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'less-than';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Less than', 'gloo_for_elementor' );
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type          = ! empty( $args['condition_type'] ) ? $args['condition_type'] : 'show';
		$data_type     = ! empty( $args['condition_data_type'] ) ? $args['condition_data_type'] : 'chars';
		$current_value = $this->get_current_value( $args );
		$value         = $args['condition_value'];
		$values        = $this->adjust_values_type( $current_value, $value, $data_type );

		if ( 'hide' === $type ) {
			return $values['current'] >= $values['compare'];
		} else {
			return $values['current'] < $values['compare'];
		}

	}

	/**
	 * This condition is required data type detection
	 *
	 * @return boolean [description]
	 */
	public function need_type_detect() {
		return true;
	}

}

add_action( 'gloo/modules/interactor/conditions/register', function( $manager ) {
	$manager->register_condition( new Less_Than() );
} );
