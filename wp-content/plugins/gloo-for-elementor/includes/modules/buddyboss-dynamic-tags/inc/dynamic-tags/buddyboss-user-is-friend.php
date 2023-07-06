<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Is_User_Friend extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-is-user-friend';
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
		return __( 'Buddyboss Friendship', 'gloo_for_elementor' );
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

	public function render() {

		$is_friend = false;
		
		if(function_exists('jet_engine')) {

			$user_object = jet_engine()->listings->data->get_current_user_object();

			if ( ! empty( $user_object->ID ) && is_user_logged_in() ) {
				// get the login user id.
				$current_user_id = get_current_user_id();

				// check if the login user is friends of the display user.
				$is_friend = friends_check_friendship( $current_user_id, $user_object->ID );

				if ( $is_friend ) {
					echo 'Friend';
				} else {
					echo 'Not Friend';
				}

			}
		}

	}

}