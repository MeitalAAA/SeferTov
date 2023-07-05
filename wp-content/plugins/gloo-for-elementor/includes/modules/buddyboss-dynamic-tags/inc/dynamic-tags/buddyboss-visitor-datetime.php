<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Visitor_Datetime extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'buddy-boss-visitor-datetime';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'BuddyBoss Visitor Date/Time', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	 * Register Controls
	 *
	 * Registers the Dynamic tag controls
	 *
	 * @return void
	 * @since 2.0.0
	 * @access protected
	 *
	 */
	protected function _register_controls() {

		$user_context_options = array(
			'bp_logged_in'  => __( 'BP Logged In User', 'gloo_for_elementor' ),
			'current_user'  => __( 'Current User', 'gloo_for_elementor' ),
			'custom_target' => __( 'Custom Target', 'gloo_for_elementor' ),
		);

		if(function_exists('jet_engine')) {
			$user_context_options['queried_user'] = __( 'JetEngine Queried User', 'gloo_for_elementor' );
		}

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'User Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'current_user',
				'options' => $user_context_options,
			)
		);

		$this->add_control(
			'user_target',
			array(
				'label'     => __( 'User ID', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'user_context' => 'custom_target'
				],
			)
		);

		$visitor_context_options = array(
			'bp_logged_in'  => __( 'BP Logged In User', 'gloo_for_elementor' ),
			'current_user'  => __( 'Current User', 'gloo_for_elementor' ),
			'custom_target' => __( 'Custom Target', 'gloo_for_elementor' ),
		);

		if(function_exists('jet_engine')) {
			$visitor_context_options['queried_user'] = __( 'JetEngine Queried User', 'gloo_for_elementor' );
		}

		$this->add_control(
			'visitor_context',
			array(
				'label'   => __( 'Visitor Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'current_user',
				'options' => $visitor_context_options,
			)
		);

		$this->add_control(
			'visitor_target',
			array(
				'label'     => __( 'Visitor User ID', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'visitor_context' => 'custom_target'
				],
			)
		);

		$this->add_control(
			'output',
			array(
				'label'   => __( 'Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'datetime' => 'Date & Time',
					'date'     => 'Date Only',
					'time'     => 'Time Only',
				],
				'default' => 'datetime',
			)
		);
	}


	/**
	 * Render
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @return void
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function render() {

		$user_context    = $this->get_settings_for_display( 'user_context' );
		$user_target     = $this->get_settings_for_display( 'user_target' );
		$visitor_context = $this->get_settings_for_display( 'visitor_context' );
		$visitor_target  = $this->get_settings_for_display( 'visitor_target' );
		$output          = $this->get_settings_for_display( 'output' );

		$user_id = '';
		$visitor_id = '';


		switch ( $user_context ) {
			case 'current_user':
				$user_object = wp_get_current_user();
				$user_id   = $user_object->ID;
				break;
			case 'queried_user':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_current_user_object();
					$user_id   = $user_object->ID;
				}
				break;
			case 'custom_target':
				$user_id = intval( $user_target );
				break;
			case 'bp_logged_in':
				$user_id = bp_loggedin_user_id();
				break;
		}


		switch ( $visitor_context ) {
			case 'current_user':
				$user_object = wp_get_current_user();
				$visitor_id   = $user_object->ID;
				break;
			case 'queried_user':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_current_user_object();
					$visitor_id   = $user_object->ID;
				}
				break;
			case 'custom_target':
				$visitor_id = intval( $user_target );
				break;
			case 'bp_logged_in':
				$visitor_id = bp_loggedin_user_id();
				break;
		}


		if ( ! class_exists( 'BP_Recent_Visitors' ) || ! $visitor_id|| ! $user_id  ) {
			return;
		}

		$result = \BP_Recent_Visitors::get(
			[
				'visitor_id' => $visitor_id,
				'user_id'    => $user_id,
				'per_page'   => 1,
				'duration'   => 0
			]
		);

		if ( ! $result || ! isset( $result[0] ) || ! isset( $result[0]->visit_time ) ) {
			return;
		}
		
		switch ( $output ) {
			case 'date':
				$result = wp_date( "d/m/Y", strtotime( $result[0]->visit_time ) );
 				break;
			case 'time':
				$result = wp_date( "H:i", strtotime( $result[0]->visit_time ) );
 
				break;
			default:
				$result = wp_date("d לF, H:i",strtotime($result[0]->visit_time));
 
		}

		echo $result;
	}
}