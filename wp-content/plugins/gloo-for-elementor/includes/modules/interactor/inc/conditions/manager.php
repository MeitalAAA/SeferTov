<?php

namespace Gloo\Modules\Interactor\Conditions;

class Manager {

	private $_conditions = array();

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'elementor/init', array( $this, 'register_conditions' ) );
	}

	/**
	 * Register conditions
	 *
	 * @return [type] [description]
	 */
	public function register_conditions() {

		$path = gloo()->modules_path( 'interactor/inc/conditions/' );

		require_once $path . 'base.php';
		require_once $path . 'user.php';
		require_once $path . 'user-not-logged.php';
		require_once $path . 'user-role.php';
		require_once $path . 'user-role-not.php';
		require_once $path . 'user-id.php';
		require_once $path . 'user-id-not.php';
		require_once $path . 'equal.php';
		require_once $path . 'not-equal.php';
		require_once $path . 'greater-than.php';
		require_once $path . 'less-than.php';
		require_once $path . 'in-list.php';
		require_once $path . 'not-in-list.php';
		require_once $path . 'exists.php';
		require_once $path . 'not-exists.php';
		require_once $path . 'contains.php';
		require_once $path . 'not-contains.php';
		require_once $path . 'post-id.php';
		require_once $path . 'post-id-not.php';
		require_once $path . 'single-post-type.php';
		require_once $path . 'single-post-type-not.php';
		require_once $path . 'archive-post-type.php';
		require_once $path . 'archive-post-type-not.php';
		require_once $path . 'archive-tax.php';
		require_once $path . 'archive-tax-not.php';
		require_once $path . 'archive-search.php';
		require_once $path . 'archive-search-not.php';
		require_once $path . 'post-author.php';
		require_once $path . 'post-author-not.php';


		do_action( 'gloo/modules/interactor/conditions/register', $this );

	}

	/**
	 * Condition instance
	 *
	 * @param  [type] $instance [description]
	 *
	 * @return [type]           [description]
	 */
	public function register_condition( $instance ) {
		$this->_conditions[ $instance->get_id() ] = $instance;
	}

	/**
	 * Returns registered conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_for_options() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			$result[ $id ] = $instance->get_name();
		}

		return $result;

	}

	/**
	 * Returns registered conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_grouped_conditions_for_options() {

		$result = array(
			'general'    => array(
				'label'   => __( 'General', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'gloo' => array(
				'label'   => __( 'Gloo specific', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'user'       => array(
				'label'   => __( 'User', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'posts'      => array(
				'label'   => __( 'Posts', 'gloo_for_elementor' ),
				'options' => array(),
			),
		);

		foreach ( $this->_conditions as $id => $instance ) {

			$group = $instance->get_group();

			if ( ! $group ) {
				$group = 'general';
			}

			if ( empty( $result[ $group ] ) ) {
				$result[ $group ] = array(
					'label'   => $group,
					'options' => array(),
				);
			}

			$result[ $group ]['options'][ $id ] = $instance->get_name();

		}

		return array_values( $result );

	}

	/**
	 * Get conditions allowed for meta fields
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_for_fields() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->is_for_fields() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	/**
	 * Returns conditions list that is requires value detection
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_with_value_detect() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->need_value_detect() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	/**
	 * Returns conditions list that is requires type detection
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_with_type_detect() {

		$result = array();

		foreach ( $this->_conditions as $id => $instance ) {
			if ( $instance->need_type_detect() ) {
				$result[] = $id;
			}
		}

		return $result;

	}

	/**
	 * Get condition instance by ID
	 *
	 * @param  [type] $id [description]
	 *
	 * @return [type]     [description]
	 */
	public function get_condition( $id ) {
		return isset( $this->_conditions[ $id ] ) ? $this->_conditions[ $id ] : false;
	}

}
