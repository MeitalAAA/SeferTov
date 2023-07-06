<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_User_Fields extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddy-boss-variable';
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
		return __( 'BuddyBoss User Fields', 'gloo_for_elementor' );
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

		$variables = [
			'user_id' => 'User ID'
		];

		$output_option = [
			'type_none'    => 'Default',
			'type_limeter' => 'Delimeter',
			'type_ul'      => 'Ul Structure',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		if ( ! function_exists( 'bp_xprofile_get_groups' ) ) {
			return;
		}

		$groups = bp_xprofile_get_groups(
			array(
				'fetch_fields' => true,
			)
		);

		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group ) {
				if ( ! empty( $group->fields ) ) {
					foreach ( $group->fields as $field ) {
						$variables[ $field->id ] = $field->name;
					}
				}
			}
		}

		$context_options = array(
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
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
			'field_visibilty',
			[
				'label'        => __( 'Ignore visibilty preferences', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'profile_field',
			array(
				'label'   => __( 'Choose Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $variables,
			)
		);

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => $output_option,
			)
		);
		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_output' => 'type_limeter'
				],
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					'field_output' => 'type_array'
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

		$field_id        = $this->get_settings( 'profile_field' );
		$field_visibilty = $this->get_settings( 'field_visibilty' );
		$user_context    = $this->get_settings( 'user_context' );
		$field_output    = $this->get_settings( 'field_output' );
		$delimiter       = $this->get_settings( 'delimiter' );
		$array_index     = $this->get_settings( 'array_index' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		if ( ! $field_id ) {
			return;
		}

		if ( 'current_user' === $user_context ) {
			$user_object = wp_get_current_user();

		} elseif('current_post_author' === $user_context ) {
			
			$post = get_post( get_the_ID() );

			if(isset($post->post_author)) {
				$user = get_user_by('ID', $post->post_author);
				$user_object = $user->data;
			}
			
		}
		elseif('queried_user' === $user_context && function_exists('jet_engine') && isset(jet_engine()->listings) && isset(jet_engine()->listings->data)) {

			$user_object = jet_engine()->listings->data->get_current_user_object();
			if(!empty($user_object)) {
				$user = get_user_by('ID', $user_object->ID);
				$user_object = $user->data;
			}
		} elseif( 'displayed_user' === $user_context ) {
			$bp = buddypress();
			
			$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

			$user = get_user_by('ID', $id);
			$user_object = $user->data;
		} else {
			
			if(function_exists('bp_is_user') && bp_is_user() && ! bp_get_member_user_id()) {
				$user_id = bp_displayed_user_id();
  			} else {
				$user_id = bp_get_member_user_id();
			}
			
			if($user_id) {
				$user = get_user_by('ID', $user_id);
				$user_object = $user->data;
			}
  		}

	//	echo '<pre>'; print_r($user_object); echo '</pre>';

		$user_value = '';

		if ( ! empty( $field_id ) ) {

			if($field_id == 'user_id') {
				$user_field_value[] = $user_object->ID;
			} else {
				$visibilty = xprofile_get_field_visibility_level( $field_id, $user_object->ID );

				$user_field_value = bp_get_profile_field_data( array(
					'field'   => $field_id,
					'user_id' => $user_object->ID
				) );
			}
 
			if ( ! empty( $user_field_value ) ) {

				if ( is_array( $user_field_value ) ) {

					if ( $field_output == 'type_ul' ) {

						$user_value .= '<ul class="user-fields">';

						foreach ( $user_field_value as $value ) {
							$user_value .= '<li>' . $value . '</li>';
						}

						$user_value .= '</ul>';

					} else if ( $field_output == 'type_lenght' ) {

						$user_value = count( $user_field_value );

					} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

						$user_value = implode( $delimiter, $user_field_value );

					} else if ( $field_output == 'type_array' && ! empty( $array_index ) ) {

						if ( isset( $user_field_value[ $array_index ] ) && ! empty( $user_field_value[ $array_index ] ) ) {
							$user_value = $user_field_value[ $array_index ];
						}

					} else if ( $field_output == 'one_per_line' ) {
						if($one_per_line_type == 'html')
							$output = implode( '<br />', $user_field_value );
						else
							$output = implode( PHP_EOL, $user_field_value );
	
					} 

				} else {
					$user_value = $user_field_value;
				}

				if ( $visibilty == 'adminsonly' && is_admin() ) {
					echo $user_value;

				} else if ( $visibilty == 'adminsonly' && ! is_admin() && $field_visibilty == 'yes' ) {

					echo $user_value;

				} else if ( $visibilty != 'adminsonly' ) {
					echo $user_value;
				}

			}

		}

	}

}