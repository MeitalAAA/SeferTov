<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Block_URL extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-block-url';
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
		return __( 'BuddyBoss Block URL', 'gloo_for_elementor' );
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
		return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
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
		$context_options = array(
			'current_user'  => __( 'Current User', 'gloo_for_elementor' ),
			'custom_target' => __( 'Custom Target', 'gloo_for_elementor' ),
			'displayed_user' => __( 'BB Displayed User', 'gloo_for_elementor' )
		);

		if(function_exists('jet_engine')) {
			$context_options['queried_user'] = __( 'JetEngine Queried User', 'gloo_for_elementor' );
		}
 
		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'current_user',
				'options' => $context_options,
			)
		);

		$this->add_control(
			'target',
			array(
				'label'     => __( 'Target User ID', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'user_context' => 'custom_target'
				],
			)
		);
	}

	public function get_your_blocked_ids() {
		global $wpdb;
		$user_id     = get_current_user_id();
		$blocked_ids = $wpdb->get_col( "SELECT target_id FROM {$wpdb->base_prefix}bp_block_member WHERE user_id = '$user_id' " );

		return $blocked_ids;
	}

	function bp_block_link( $target_id, $action ) {

		$user_id = get_current_user_id();
		$token   = wp_create_nonce( 'block-' . $target_id );
		$partial = '/?action=' . $action . '&id=' . $user_id . '&target=' . $target_id . '&token=' . $token;
		global $wp;

		return home_url( $wp->request ) . $partial;

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

		$user_context = $this->get_settings_for_display( 'user_context' );
		$target       = $this->get_settings_for_display( 'target' );
		$target_id    = '';
		switch ( $user_context ) {
			case 'current_user':
				$user_object = wp_get_current_user();
				$target_id   = $user_object->ID;
				break;
			case 'queried_user':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_current_user_object();
					$target_id   = $user_object->ID;
				}
				break;
			case 'custom_target':
				$target_id = intval( $target );
				break;
			case 'displayed_user':
				$bp = buddypress();
				$target_id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;
				break;
		}

		if ( ! class_exists( 'BP_Block_Member' ) || ! $target_id ) {
			return;
		}

		if ( in_array( $target_id, $this->get_your_blocked_ids() ) ) {
			$action = 'unblock';
		} else {
			$action = 'block';
		}
		$url = $this->bp_block_link( $target_id, $action );
		echo $url;
	}
}
