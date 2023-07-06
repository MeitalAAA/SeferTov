<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Friend_Req_URL extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddy-boss-friend-req-url';
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
		return __( 'BuddyBoss Friend Request URL', 'gloo_for_elementor' );
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
			'current_post_author' => __( 'Current Post Author', 'gloo_for_elementor' ),
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
			case 'current_post_author':
				$post = get_post( get_the_ID() );

				if(isset($post->post_author)) {
					$target_id   = $post->post_author;	
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

		if ( ! function_exists( 'bp_is_friend' ) || ! $target_id ) {
			return;
		}

		$is_friend = bp_is_friend( $target_id );

		switch ( $is_friend ) {
			case 'pending':
				$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests/cancel/' . $target_id . '/', 'friends_withdraw_friendship' );
				break;
			case 'awaiting_response':
				$url = bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests/';
				break;
			case 'is_friend':
				$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/remove-friend/' . $target_id . '/', 'friends_remove_friend' );
				break;
			default:
				$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $target_id . '/', 'friends_add_friend' );
				break;
		}

		echo $url;
	}
}