<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_User_Pictures extends \Elementor\Core\DynamicTags\Data_Tag {

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
		return 'buddyboss-user-images';
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
		return __( 'BuddyBoss User Images', 'gloo_for_elementor' );
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
		return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ];
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
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'current_user',
				'options' => array(
					'current_user' => __( 'Current User', 'gloo_for_elementor' ),
					'queried_user' => __( 'Queried User', 'gloo_for_elementor' ),
					'displayed_user' => __( 'BB Displayed User', 'gloo_for_elementor' )
				),
			)
		);

		$this->add_control(
			'image_type',
			array(
				'label'   => __( 'User Image', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'profile_photo',
				'options' => array(
					'profile_photo' => __( 'Profile Image', 'gloo_for_elementor' ),
					'cover_photo'   => __( 'Cover Image', 'gloo_for_elementor' ),
				),
			)
		);

		$this->add_control(
			'user_image_fallback',
			array(
				'label'     => __( 'User Image Fallback', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'image_type' => 'profile_photo'
				],
			)
		);

		$this->add_control(
			'fallback_cover',
			array(
				'label'     => __( 'Cover Image Fallback', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'image_type' => 'cover_photo'
				],
			)
		);

	}

	public function get_value( array $options = array() ) {

		$context        = $this->get_settings( 'user_context' );
		$image_type     = $this->get_settings( 'image_type' );
		$fallback       = $this->get_settings( 'fallback' );
		$fallback_cover = $this->get_settings( 'fallback_cover' );

		if( !function_exists('jet_engine') ) {
			return;
		}

		if ( ! $context ) {
			$context = 'current_user';
		}

		if ( 'current_user' === $context ) {
			$user_object = jet_engine()->listings->data->get_current_user_object();
			
		} elseif( 'displayed_user' === $context ) {
			$bp = buddypress();
			$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

			$user = get_user_by('ID', $id);
			$user_object = $user->data;

		} else {
			$user_object = jet_engine()->listings->data->get_current_user_object();
		}


		if ( ! $user_object ) {
			return;
		}


		if ( ! empty( $user_object->ID ) ) {
			// Get the profile type.

			if ( $image_type == 'profile_photo' ) {

				$args_avtar = [
					'item_id' => $user_object->ID,
					'object'  => 'user',
					'html'    => false
				];

				$user_url = bp_core_fetch_avatar( $args_avtar );


				if ( $user_url ) {
					return array(
						'url' => $user_url,
					);
				} else {
					return $fallback;
				}

			} elseif ( $image_type == 'cover_photo' ) {

				$cover_src = bp_attachments_get_attachment( 'url', array(
					'item_id' => $user_object->ID,
					'type'    => 'cover-image',
				) );

				if ( $cover_src ) {
					return array(
						'url' => $cover_src,
					);
				} else {
					return $fallback_cover;
				}

			}
		}
	}

}