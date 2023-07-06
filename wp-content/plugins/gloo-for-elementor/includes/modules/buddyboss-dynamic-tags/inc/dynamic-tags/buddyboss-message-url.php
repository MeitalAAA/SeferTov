<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Message_URL extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddy-boss-message-url';
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
		return __( 'BuddyBoss Message URL', 'gloo_for_elementor' );
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
			$context_options['queried_user'] = __( 'Queried User', 'gloo_for_elementor' );
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

		if ( ! $target_id ) {
			return;
		}

		$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $target_id ) );
		echo $url;
	}
}