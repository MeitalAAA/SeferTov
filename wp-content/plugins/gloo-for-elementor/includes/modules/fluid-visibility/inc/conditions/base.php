<?php
namespace Gloo\Modules\Fluid_Visibility\Conditions;

abstract class Base {

	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	abstract public function get_id();

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	abstract public function get_name();

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	abstract public function evaluate( $args = array() );

	/**
	 * Returns condition group
	 *
	 * @return string group
	 */
	public function get_group() {
		return false;
	}


	/**
	 * @return boolean Enable field for condition
	 */
	public function enable_field() {
		return true;
	}

	/**
	 * @return boolean Enable value for condition
	 */
	public function enable_value() {
		return true;
	}


	/**
	 * @return boolean data type detection
	 */
	public function enable_data_type() {
		return false;
	}

	/**
	 * Returns current field value
	 */
	public function get_current_value( $args = array() ) {

		$current_value = null;

		if ( ! empty( $args['field_raw'] ) ) {
			$current_value = get_post_meta( get_the_ID(), $args['field_raw'], true );
		} else {
			$current_value = $args['field'];
		}
		return $current_value;

	}

	/**
	 * Convert Engine checkboxes values to plain array
	 */
	public function checkboxes_to_array( $array = array() ) {

		$result = array();

		foreach ( $array as $value => $bool ) {

			$bool = filter_var( $bool, FILTER_VALIDATE_BOOLEAN );

			if ( $bool ) {
				$result[] = $value;
			}
		}

		return $result;

	}

	/**
	 * Adjust type
	 */
	public function adjust_type( $current_value, $value_to_compare, $type ) {

		switch ( $type ) {
			case 'numeric':
				$current_value    = intval( $current_value );
				$value_to_compare = intval( $value_to_compare );
				break;

			case 'datetime':
			case 'date':

				if ( ! $this->is_valid_timestamp( $current_value ) ) {
					$current_value = strtotime( $current_value );
				}

				$value_to_compare = strtotime( $value_to_compare );

				break;

			default:
				$current_value    = strval( $current_value );
				$value_to_compare = strval( $value_to_compare );
				break;
		}

		return array(
			'current' => $current_value,
			'compare' => $value_to_compare,
		);

	}


	public function is_valid_timestamp( $timestamp ) {
		return ( ( string ) ( int ) $timestamp === $timestamp || ( int ) $timestamp === $timestamp )
		       && ( $timestamp <= PHP_INT_MAX )
		       && ( $timestamp >= ~PHP_INT_MAX );
	}


	public function explode_string( $value = null ) {

		if ( empty( $value ) ) {
			return array();
		}

		$value = explode( ',', $value );
		$value = array_map( 'trim', $value );

		return $value;

	}

}
