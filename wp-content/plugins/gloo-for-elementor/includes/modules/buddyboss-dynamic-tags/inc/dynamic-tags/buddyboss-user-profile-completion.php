<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Profile_Completion extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-profile-completion';
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
		return __( 'Buddyboss Profile Completion', 'gloo_for_elementor' );
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

		$context_options = array(
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
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

	}

	/**
	 * Get profile percentage of user
	 *
	 */
	public function bp_xprofile_get_queried_user_progress( $group_ids, $photo_types, $user_id ) {

		if ( empty( $group_ids ) ) {
			$group_ids = array();
		}

		/* User Progress specific VARS. */
		$progress_details       = array();
		$grand_total_fields     = 0;
		$grand_completed_fields = 0;

		/* Profile Photo */

		// check if profile photo option still enabled.
		$is_profile_photo_disabled = bp_disable_avatar_uploads();
		if ( ! $is_profile_photo_disabled && in_array( 'profile_photo', $photo_types ) ) {

			++ $grand_total_fields;

			$is_profile_photo_uploaded = ( bp_get_user_has_avatar( $user_id ) ) ? 1 : 0;

			if ( $is_profile_photo_uploaded ) {
				++ $grand_completed_fields;
			} else {

				// check if profile gravatar option enabled.
				// blank setting will remove gravatar also
				if ( bp_enable_profile_gravatar() && 'blank' !== get_option( 'avatar_default', 'mystery' ) ) {

					/**
					 * There is not any direct way to check gravatar set for user.
					 * Need to check $profile_url is send 200 status or not.
					 */
					remove_filter( 'get_avatar_url', 'bp_core_get_avatar_data_url_filter', 10 );
					$profile_url = get_avatar_url( $user_id, [ 'default' => '404' ] );
					add_filter( 'get_avatar_url', 'bp_core_get_avatar_data_url_filter', 10, 3 );

					$headers = get_headers( $profile_url, 1 );
					if ( $headers[0] === 'HTTP/1.1 200 OK' ) {
						$is_profile_photo_uploaded = 1;
						++ $grand_completed_fields;
					}
				}
			}

			$progress_details['photo_type']['profile_photo'] = array(
				'is_uploaded' => $is_profile_photo_uploaded,
				'name'        => __( 'Profile Photo', 'buddyboss' ),
			);

		}

		/* Cover Photo */

		// check if cover photo option still enabled.
		$is_cover_photo_disabled = bp_disable_cover_image_uploads();
		if ( ! $is_cover_photo_disabled && in_array( 'cover_photo', $photo_types ) ) {

			++ $grand_total_fields;

			$is_cover_photo_uploaded = ( bp_attachments_get_user_has_cover_image( $user_id ) ) ? 1 : 0;

			if ( $is_cover_photo_uploaded ) {
				++ $grand_completed_fields;
			}

			$progress_details['photo_type']['cover_photo'] = array(
				'is_uploaded' => $is_cover_photo_uploaded,
				'name'        => __( 'Cover Photo', 'buddyboss' ),
			);

		}

		/* Groups Fields */

		$profile_groups = bp_xprofile_get_groups(
			array(
				'fetch_fields' => true,
				'user_id'      => $user_id,
			)
		);

		foreach ( $profile_groups as $single_group_details ) {

			if ( empty( $single_group_details->fields ) ) {
				continue;
			}

			/* Single Group Specific VARS */
			$group_id              = $single_group_details->id;
			$single_group_progress = array();

			// Consider only selected Groups ids from the widget form settings, skip all others.
			if ( ! in_array( $group_id, $group_ids ) ) {
				continue;
			}

			// Check if Current Group is repeater if YES then get number of fields inside current group.
			$is_group_repeater_str = bp_xprofile_get_meta( $group_id, 'group', 'is_repeater_enabled', true );
			$is_group_repeater     = ( 'on' === $is_group_repeater_str ) ? true : false;

			/* Loop through all the fields and check if fields completed or not. */
			$group_total_fields     = 0;
			$group_completed_fields = 0;
			foreach ( $single_group_details->fields as $group_single_field ) {

				/**
				 * Added support for display name format support from platform.
				 * Get the current display settings from BuddyBoss > Settings > Profiles > Display Name Format.
				 */
				if ( function_exists( 'bp_core_hide_display_name_field' ) && true === bp_core_hide_display_name_field( $group_single_field->id ) ) {
					continue;
				}

				// If current group is repeater then only consider first set of fields.
				if ( $is_group_repeater ) {

					// If field not a "clone number 1" then stop. That means proceed with the first set of fields and restrict others.
					$field_id     = $group_single_field->id;
					$clone_number = bp_xprofile_get_meta( $field_id, 'field', '_clone_number', true );
					if ( $clone_number > 1 ) {
						continue;
					}
				}

				$field_id = $group_single_field->id;

				// For Social networks field check child field is completed or not
				if ( 'socialnetworks' == $group_single_field->type ) {


					$field_data_value = maybe_unserialize( bp_get_profile_field_data( array(
						'field'   => $field_id,
						'user_id' => $user_id
					) ) );

					$children = $group_single_field->type_obj->field_obj->get_children();
					foreach ( $children as $child ) {
						if ( isset( $field_data_value[ $child->name ] ) && ! empty( $field_data_value[ $child->name ] ) ) {
							++ $group_completed_fields;
						}
						++ $group_total_fields;
					}
				} else {
					$field_data_value = maybe_unserialize( bp_get_profile_field_data( array(
						'field'   => $field_id,
						'user_id' => $user_id
					) ) );

					if ( ! empty( $field_data_value ) ) {
						++ $group_completed_fields;
					}

					++ $group_total_fields;
				}
			}

			/* Prepare array to return group specific progress details */
			$single_group_progress['group_name']             = $single_group_details->name;
			$single_group_progress['group_total_fields']     = $group_total_fields;
			$single_group_progress['group_completed_fields'] = $group_completed_fields;

			$grand_total_fields     += $group_total_fields;
			$grand_completed_fields += $group_completed_fields;

			$progress_details['groups'][ $group_id ] = $single_group_progress;

		}

		/* Total Fields vs completed fields to calculate progress percentage. */
		$progress_details['total_fields']     = $grand_total_fields;
		$progress_details['completed_fields'] = $grand_completed_fields;

		/**
		 * Filter returns User Progress array.
		 *
		 * @since BuddyBoss 1.2.5
		 */
		return apply_filters( 'xprofile_pc_user_progress', $progress_details );
	}

	public function render() {

		// do not do anything if user isn't logged in OR IF user is viewing other members profile.
		if ( ! is_user_logged_in() || ( bp_is_user() && ! bp_is_my_profile() ) ) {
			return;
		}

		$profile_percent = false;
		$user_context = $this->get_settings( 'user_context' );

		if ( 'current_user' === $user_context ) {
			$user_object = wp_get_current_user();

		} elseif( 'queried_user' === $user_context && function_exists('jet_engine')) {

			$user_object = jet_engine()->listings->data->get_current_user_object();

		} elseif( 'displayed_user' === $user_context ) {
			$bp = buddypress();
			
			$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

			$user = get_user_by('ID', $id);
			$user_object = $user->data;
		}

		if ( ! empty( $user_object->ID ) ) {

			$groups_ids = [];

			$groups = bp_xprofile_get_groups(
				array(
					'fetch_fields'      => false,
					'user_id'           => $user_object->ID,
					'update_meta_cache' => false
				)
			);

			if ( ! empty( $groups ) ) {

				foreach ( $groups as $group ) {

					$groups_ids[] = $group->id;

				}

				if ( ! empty( $groups_ids ) && is_array( $groups_ids ) ) {

					$profile_groups    = $groups_ids;
					$profile_phototype = array( 'profile_photo', 'cover_photo' );

					$user_progress_arr = $this->bp_xprofile_get_queried_user_progress( $profile_groups, $profile_phototype, $user_object->ID );

					// Do not proceed if no fields found based on settings.
					if ( isset( $user_progress_arr['total_fields'] ) && $user_progress_arr['total_fields'] <= 0 ) {
						return $user_progress;
					}

					// Format User Progress array to pass on to the template.
					$user_progress = bp_xprofile_get_user_progress_formatted( $user_progress_arr );

					echo $user_progress['completion_percentage'] . '%';

				}

			}

		}

	}

}