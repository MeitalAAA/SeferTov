<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions;

class Manager {

	private $condition_list = array();
	private $js_condition_list = array();

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

		$path = gloo()->modules_path( 'fluid-visibility/inc/conditions/' );

		require_once $path . 'base.php';
		require_once $path . 'user.php';
		require_once $path . 'equal.php';
		require_once $path . 'user-role.php';
		require_once $path . 'user-id.php';
		require_once $path . 'post-id.php';
		require_once $path . 'single-post-type.php';
		require_once $path . 'archive-post-type.php';
		require_once $path . 'archive-tax.php';
		require_once $path . 'archive-search.php';
		require_once $path . 'post-author.php';
		require_once $path . 'post-has-terms.php';
		require_once $path . 'greater-than.php';
		require_once $path . 'less-than.php';
		require_once $path . 'in-list.php';
		require_once $path . 'exists.php';
		require_once $path . 'contains.php';
		require_once $path . 'is-mobile.php';
		require_once $path . 'single-post-status.php';

		/* buddyboss conditions */
		if($this->is_required_module_active('buddyboss_gloo_kit') && function_exists('buddypress')) {
			require_once $path . 'blocked_both_ways.php';
			require_once $path . 'has_subgroups.php';
			require_once $path . 'is_blocked.php';
			require_once $path . 'is_bp.php';
			require_once $path . 'is_friend.php';
			require_once $path . 'is_group.php';
			require_once $path . 'is_groups_directory.php';
			require_once $path . 'is_members_directory.php';
			require_once $path . 'is_my_profile.php';
			require_once $path . 'is_user_profile.php';
		}
		
		/* learndash conditions */
		if($this->is_required_module_active('buddyboss_gloo_kit') && defined( 'LEARNDASH_VERSION' )) {
			require_once $path . 'user-completed-course.php';
			require_once $path . 'user-enrolled-course.php';
		}

		//js
		require_once $path . '/js/base.php';
		require_once $path . '/js/equal.php';
		require_once $path . '/js/not-equal.php';
		require_once $path . '/js/greater-than.php';
		require_once $path . '/js/greater-or-equal.php';
		require_once $path . '/js/smaller-than.php';
		require_once $path . '/js/smaller-or-equal.php';
		require_once $path . '/js/contains.php';
		require_once $path . '/js/not-contains.php';
		
		do_action( 'gloo/modules/fluid_visibility/conditions/register', $this );

	}

	/**
	 * Condition instance
	 *
	 * @param  [type] $instance [description]
	 *
	 * @return [type]           [description]
	 */
	public function register_condition( $instance ) {
		$id    = $instance->get_id();
		$is_js = substr( $id, 0, 3 ) === "js_";
		if ( $is_js !== false ) {
			$this->js_condition_list[ $id ] = $instance;
		} else {
			$this->condition_list[ $id ] = $instance;

		}
	}

	/**
	 * Returns registered conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_conditions_for_options() {

		$result = array();

		foreach ( $this->condition_list as $id => $instance ) {
			$result[ $id ] = $instance->get_name();
		}

		return $result;

	}


	/**
	 * Returns registered js conditions in id => name format
	 *
	 * @return [type] [description]
	 */
	public function get_js_conditions_for_options($result = array()) {

		foreach ( $this->js_condition_list as $id => $instance ) {
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
			'general' => array(
				'label'   => __( 'General', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'gloo'    => array(
				'label'   => __( 'Gloo specific', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'user'    => array(
				'label'   => __( 'User', 'gloo_for_elementor' ),
				'options' => array(),
			),
			'posts'   => array(
				'label'   => __( 'Posts', 'gloo_for_elementor' ),
				'options' => array(),
			),
		);

		if($this->is_required_module_active('buddyboss_gloo_kit') && function_exists('buddypress')) {
			$result['buddypress'] = array(
				'label'   => __( 'BuddyPress', 'gloo_for_elementor' ),
				'options' => array(),
			);
		}

		if($this->is_required_module_active('buddyboss_gloo_kit') && defined( 'LEARNDASH_VERSION' )) {
			$result['learndash'] = array(
				'label'   => __( 'LearnDash', 'gloo_for_elementor' ),
				'options' => array(),
			);
		}

		foreach ( $this->condition_list as $id => $instance ) {

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

		foreach ( $this->condition_list as $id => $instance ) {
			if ( $instance->enable_field() ) {
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

		foreach ( $this->condition_list as $id => $instance ) {
			if ( $instance->enable_value() ) {
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

		foreach ( $this->condition_list as $id => $instance ) {
			if ( $instance->enable_data_type() ) {
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
		$is_js = substr( $id, 0, 3 ) === "js_";
		if ( $is_js !== false ) {
			return isset( $this->js_condition_list[ $id ] ) ? $this->js_condition_list[ $id ] : false;
		}
		return isset( $this->condition_list[ $id ] ) ? $this->condition_list[ $id ] : false;
	}

	/**
	 * check for active required gloo module
	 *
	 * @param  [type] $module_id [description]
	 * 
	 * @return true/false
	 */
	public function is_required_module_active($module_id) {

		if(!$module_id) {
			return false;
		}

		$output = false;
		$active_modules = get_option( 'gloo_modules', array() );

		if(!empty($active_modules)) {
			if (in_array($module_id, $active_modules)) {
				$output = true;
			}
		}

		return $output;
	}

}
