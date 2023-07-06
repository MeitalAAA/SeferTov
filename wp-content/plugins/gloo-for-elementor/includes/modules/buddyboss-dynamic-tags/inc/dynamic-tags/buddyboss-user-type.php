<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_User_Type extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-user-type';
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
		return __( 'BuddyBoss User Type', 'gloo_for_elementor' );
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

		$this->add_control(
			'output',
			array(
				'label'   => __( 'Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'name',
				'options' => array(
					'id'   => __( 'ID', 'gloo_for_elementor' ),
					'name' => __( 'Name', 'gloo_for_elementor' ),
				),
			)
		);

		$context_options = array(
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
			'current_post_author' => __( 'Current Post Author', 'gloo_for_elementor' )
		);

		if(function_exists('jet_engine')) {
			$context_options['queried_user'] = __( 'Queried User', 'gloo_for_elementor' );
		}

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'current_user' => __( 'Current User', 'gloo_for_elementor' ),
					'queried_user' => __( 'Queried User', 'gloo_for_elementor' ),
					'current_post_author' => __( 'Current Post Author', 'gloo_for_elementor' ),
					'displayed_user' => __( 'BB Displayed User', 'gloo_for_elementor' )
				),
			)
		);

	}

	public function render() {

		$context = $this->get_settings_for_display( 'user_context' );
		$output  = $this->get_settings_for_display( 'output' );

		if ( ! $context ) {
			$context = 'current_user';
		}

		if ( 'current_user' === $context ) {
			
			$user_object = wp_get_current_user();

		} elseif('current_post_author' === $context ) {
			
			$post = get_post( get_the_ID() );

			if(isset($post->post_author)) {
				$user = get_user_by('ID', $post->post_author);
				$user_object = $user->data;
			}
			
		} elseif( 'displayed_user' === $context ) {
			$bp = buddypress();
			$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

			$user = get_user_by('ID', $id);
			$user_object = $user->data;
	
		} else {

			if(function_exists('jet_engine')) {
				$user_object = jet_engine()->listings->data->get_current_user_object();
			}
		}

		if ( ! empty( $user_object->ID ) ) {
			// Get the profile type.

			if ( $output === 'id' ) {
				$member_type = bp_get_member_type( $user_object->ID );
				$posts       = get_posts( array(
					'post_type'      => 'bp-member-type',
					'posts_per_page' => 1,
					'meta_key'       => '_bp_member_type_key',
					'meta_value'     => $member_type,
					'fields'         => 'ids'
				) );
				echo isset( $posts[0] ) ? $posts[0] : '';
			} else {
				$member_type = bp_get_member_type( $user_object->ID );
				if ( ! empty( $member_type ) ) {
					$type_obj    = bp_get_member_type_object( $member_type );
					$member_type = $type_obj->labels['singular_name'];
					echo $member_type;
				}
			}
		}

	}

}