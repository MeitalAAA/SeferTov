<?php

namespace Gloo\Modules\Form_User_Editing;

use Elementor\Modules\DynamicTags\Module as DynamicTags;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend_User_Editing extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'frontend_user_editing';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Frontend User Editing', 'gloo_for_elementor' );
	}

	private $prefix = 'gloo_frontend_user_editing';

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );


		/* get all media uploads if upload is set to media */
		$media_uploads = $record->get( 'uploaded_to_media' );
		$uploaded_ids = $record->get( 'uploaded_ids' );
		$updated_media = array();
		$stored_ids = array();

		// Get submitted form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			if($field['type'] == 'gloo_wysiwyg') {
				global $allowedposttags;
				$fields[$id] = trim(wp_kses( $field['value'], $allowedposttags));
			} else {
				$fields[ $id ] = sanitize_text_field( $field['value'] );
			}
		}

		$user_id          = $settings[ $this->prefix . '_user_id' ];
		$meta_fields      = $settings[ $this->prefix . '_meta_fields' ];
		$term_fields      = $settings[ $this->prefix . '_term_fields' ];
		$post_type_fields = $settings[ $this->prefix . '_post_type_fields' ];
		$buddyboss_fields = $settings[ $this->prefix . '_buddyboss_fields' ];
		$user_data_fields = $settings[ $this->prefix . '_user_data' ];

		if ( ! $user_id ) {
			$ajax_handler->add_error( 'No user ID provided.' );

			return;
		}

		// gather user data
		$user_data = [];

		if ( ! empty( $user_data_fields ) ) {

			foreach ( $user_data_fields as $item ) {

				if ( ! isset( $fields[ $item['field_id'] ] ) || empty( $fields[ $item['field_id'] ] || empty( $item['user_data'] ) ) ) {
					continue;
				}

				if ( $item['user_data'] === 'password' && $fields[ $item['field_id'] ] ) { // update password
					// confirm pass

					$field_key = array_search( 'confirm_password', array_column( $user_data_fields, 'user_data' ) );
					if ( $field_key !== false ) {
						$confirm_pass = $fields[ $user_data_fields[ $field_key ]['field_id'] ];
						if ( $confirm_pass !== $fields[ $item['field_id'] ] ) {
							return $ajax_handler->add_error( $user_data_fields[ $field_key ]['field_id'], 'Passwords do not match.' );
						}
					}
					$current_user = wp_get_current_user();

					wp_set_password( $fields[ $item['field_id'] ], $user_id );
					if ( ! $item['keep_logged_in'] ) {
						continue;
					}
					wp_set_auth_cookie( $current_user->ID );
					wp_set_current_user( $current_user->ID );
					do_action( 'wp_login', $current_user->user_login, $current_user );
				}

				$user_data[ $item['user_data'] ] = $fields[ $item['field_id'] ];
			}
		}

		// Create user object
		$user_to_edit = array(
			'ID' => $user_id,
		);

		if ( ! empty( $user_data ) ) {
			$user_to_edit = array_merge( $user_to_edit, $user_data );
		}

		if(!empty($user_data)) {
			// Update the user
			ob_start(); //prevent any output printing
			$updated_user_id = wp_update_user( $user_to_edit );
			ob_clean();
		}
		
		if ( is_wp_error( $updated_user_id ) ) {
			$ajax_handler->add_error( "Couldn't Update User." );

			return;
		}

		// update meta data
		if ( ! empty( $meta_fields ) ) {
			foreach ( $meta_fields as $meta_field ) {
				if ( ! isset( $fields[ $meta_field['field_id'] ] ) || empty( $fields[ $meta_field['field_id'] ] || empty( $meta_field['meta_field'] ) ) && $meta_field['is_image'] != 'yes') {
					if(!($raw_fields[$meta_field['field_id']]['type'] == 'acceptance' || $raw_fields[$meta_field['field_id']]['type'] == 'checkbox'))
						continue;
					if(empty($meta_field['is_image']) || (isset($meta_field['is_image']) && $meta_field['is_image'] == 'no'))
						$fields[ $meta_field['field_id'] ] = '';
				}


					// check if it is an image meta field
					if ( $meta_field['is_image'] == 'yes' ) {
						
						$raw_field = $raw_fields[$meta_field['field_id']];
						$field_with_settings = $record->get_form_settings('form_fields');
						
						if(!empty($field_with_settings)) {
							foreach($field_with_settings as $upload_field){
								if(isset($upload_field['custom_id']) && $upload_field['custom_id'] == $meta_field['field_id']){
									$upload_field_settings = $upload_field;
									break;
								}
							}
						}
						
						//&& ( isset($fields[ $meta_field['field_id'] ] ) && !empty($fields[ $meta_field['field_id'] ])) 
						$store = $meta_field['store_image'];
						if(isset($meta_field['store_image_meta_type']) && $store == 'url' && $meta_field['store_image_meta_type'] == 'acf')
							$store = 'id';
							
						$image_uploads = array();
						$saved_uploads = get_user_meta( $user_id, $meta_field['meta_field'], true );
						
						if(isset($upload_field_settings['gloo_filepond_upload']) && $upload_field_settings['gloo_filepond_upload'] == 'yes') {
							
							if( isset($uploaded_ids[$meta_field['field_id']]) && !empty($uploaded_ids[$meta_field['field_id']]) ) {
								$modified_uploads = $uploaded_ids[$meta_field['field_id']];
	
								if( !empty($saved_uploads)) {
									$saved_uploads = $this->convert_format_to_ids($store, $saved_uploads);
									
									/* remove attahcments from db if removed from frontend*/
									if( is_array($saved_uploads) ) {
										foreach( $saved_uploads as $saved_upload ) {
	
											if(!in_array($saved_upload, $modified_uploads)) {
												wp_delete_attachment( $saved_upload, true );
											} else {
												$updated_media[$meta_field['field_id']][] = $saved_upload;
											}
										}
									} else {
										/* for single values */
										if(!in_array($saved_uploads, $modified_uploads)) {
											wp_delete_attachment( $saved_uploads, true );
										} else {
											$updated_media[$meta_field['field_id']][] = $saved_uploads;
										}
									}
									
									if(!empty($updated_media) && !isset($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ])) {
										
										$image_uploads['uploaded_to_media'][ $meta_field['field_id'] ] = $updated_media[ $meta_field['field_id'] ];
	
									/* if there are already uploaded files and merge ids with new uploads*/
									} else if(!empty($updated_media) && (isset($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ]) && !empty($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ]))) { 
										 
										if(!empty($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ]) && !empty($updated_media[ $meta_field['field_id'] ])) {
											$image_uploads['uploaded_to_media'][ $meta_field['field_id'] ] = array_merge($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ], $updated_media[$meta_field['field_id']]);
										}
									}
								}
							} else {
								/* if it's a new upload without any exisitng files in the field */
								if(isset($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ]) && !empty($media_uploads['uploaded_to_media'][ $meta_field['field_id'] ])) { 
									$image_uploads = $media_uploads;	
								} else {
									 delete_user_meta( $user_id, $meta_field['meta_field'] );		
								}
							}
						}  else {
							$image_uploads = $media_uploads;	
						}
						
					
						if ( isset( $image_uploads['uploaded_to_media'] ) && !empty( $image_uploads['uploaded_to_media'][ $meta_field['field_id'] ] ) ) {

							$uploads_data = $image_uploads['uploaded_to_media'][$meta_field['field_id']];
							/* get return values in format according to database */
							$value = $this->get_image_storage_value( $store, $uploads_data, $meta_field );
							
							// db($value);exit();
							$stored_ids[$meta_field['field_id']] = $uploads_data;
							// $meta_data[ $meta_field['meta_field'] ] = $value;
							$fields[ $meta_field['field_id'] ] = $value;
						}
					}
					

				if ( get_user_meta( $user_id, $meta_field['meta_field'] ) ) {
					update_user_meta( $user_id, $meta_field['meta_field'], $fields[ $meta_field['field_id'] ] );
				} else {
					add_user_meta( $user_id, $meta_field['meta_field'], $fields[ $meta_field['field_id'] ] );
				}
			}
		}
		// update user taxonomy terms
		if ( ! empty( $term_fields ) ) {
			foreach ( $term_fields as $term_field ) {

				if ( ! isset( $fields[ $term_field['field_id'] ] ) || empty( $fields[ $term_field['field_id'] ] || empty( $term_field['term_field'] ) ) ) {
					continue;
				}
				$value = explode( ', ', $fields[ $term_field['field_id'] ] );

				if ( function_exists( 'update_field' ) ) { // acf
					update_field( $term_field['term_field'], $value, "user_{$user_id}" );
				} else {
					if ( get_user_meta( $user_id, $term_field['term_field'] ) ) {
						update_user_meta( $user_id, $term_field['term_field'], $value );
					} else {
						add_user_meta( $user_id, $term_field['term_field'], $value );
					}
				}
			}
		}

		/* post types field */
		if ( ! empty( $post_type_fields ) ) {
			foreach ( $post_type_fields as $post_type_field ) {

				if ( ! isset( $fields[ $post_type_field['field_id'] ] ) || empty( $fields[ $post_type_field['field_id'] ] || empty( $post_type_field['meta_field'] ) ) ) {
					continue;
				}

				$value = $fields[ $post_type_field['field_id'] ];
			 
				if (isset($post_type_field['is_relation_field']) && $post_type_field['is_relation_field'] && $value) {
					if($post_type_field['is_relation_field'] == 'acf' && !is_array($value)) {
						$arr_values = explode(',', $value);

						if(!empty($arr_values) && is_array($arr_values)) {
							$value = $arr_values;
						}
					} 
				}

				if ( get_user_meta( $user_id, $post_type_field['meta_field'] ) ) {
					update_user_meta( $user_id, $post_type_field['meta_field'], $value );
				} else {
					add_user_meta( $user_id, $post_type_field['meta_field'], $value );
				}
			}
		}
		
		if( ! empty( $buddyboss_fields ) && function_exists( 'buddypress' )) {
			foreach ( $buddyboss_fields as $buddyboss_field ) {
				
				if ( ! isset( $fields[ $buddyboss_field['field_id'] ] ) ) {
					continue;
				}

				$is_profile_type = $buddyboss_field['is_profile_type'];
				$field_id = $buddyboss_field['bb_profile_field_id'];
				$is_range_slider = $buddyboss_field['is_range_slider'];
				$is_multi_field = $buddyboss_field['is_multi_field'];
				$value = $fields[ $buddyboss_field['field_id'] ];
 				 
				$field_type     = \BP_XProfile_Field::get_type( $field_id );

				if($is_profile_type == 'yes') {
 
					$profile_type_post = get_post( $value );
					$profile_type_name = str_replace( '%', '', $profile_type_post->post_name );
					if ( ! $profile_type_name ) {
						return;
					}
					bp_set_member_type( $user_id, $profile_type_name );

				} elseif($is_range_slider == 'yes') {
						
					if(!empty($value)) {
						$range_values = explode(',',$value);
 
						foreach( $range_values as $key => $range_value ) {
							$value = $range_values[$key];
							$key++;
							xprofile_set_field_data( $buddyboss_field['bb_profile_range_field_'.$key], $user_id, $value );
						}
					}

				} else {

					if($is_multi_field == 'yes' && !empty($value)) {
					
						$arr_values = explode(',', $value);
	
						if(!empty($arr_values) && is_array($arr_values)) {
							$value = array_map( 'sanitize_text_field', $arr_values );
						}
					}
	
					if($field_type == 'datebox' && !empty($value)) {
						$value = str_replace( '/', '-', $value );
						$date  = new \DateTime( "$value" );
						if ( $value ) {
							$value = $date->format( 'Y-m-d 00:00:00' );
						}
					}

					xprofile_set_field_data( $field_id, $user_id, $value );

				}
			}
		}
	}


	public function convert_format_to_ids( $store, $saved_value ) {
 
		$value = array();

		switch ( $store ) {
			case 'url' :
				$value = attachment_url_to_postid($saved_value);
				break;
			case 'array_multi_url' :
				foreach( $saved_value as $val ) {
					echo $val;
					$value[] = attachment_url_to_postid($val);
				}
				break;
			case 'array' :
				foreach( $saved_value as $val ) {
					$value[] = $val['id'];
				}
				break;
			case 'array_multi_id' :
				$value = $saved_value;
				break;
			case 'comma_separated_string' :
				case 'id' :
				$value = explode( ",", $saved_value );
				break;
			case 'comma_separated_string_url' :
				$urls = explode( ",", $saved_value );
				foreach( $urls as $val ) {
					$value[] = attachment_url_to_postid($val);
				}
				break;
			default:
				$value = $saved_value;

		}

		return $value;
	}



	public function get_image_storage_value( $store, $files, $meta_field ) {
		$value = array();
		
		switch ( $store ) {
			case 'url' :
				$value = wp_get_attachment_url($files[0]);
				break;
			case 'array' :
				foreach ( $files as $val ) {	
					$value[] = [
						'id'  => $val,
						'url' => wp_get_attachment_url( $val ),
					];
				}
				break;
			case 'array_multi_id' :
				$value = $files;
				break;
			case 'array_multi_url' :
				foreach ( $files as $val ) {
					$value[] = wp_get_attachment_url( $val );
				}
				break;
			case 'comma_separated_string' :
				$value = implode( ",", $files );
				break;
			case 'comma_separated_string_url' :
				foreach ( $files as $val ) {
					$urls[] = wp_get_attachment_url( $val );
				}
				$value = implode( ",", $urls );
				break;
			case 'id' :
				$value = $files[0];
				break;
			default:
				$value = $files;

		}

		return $value;
	}
	
	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {


		$widget->start_controls_section(
			$this->prefix,
			[
				'label'     => __( 'Frontend User Editing', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			$this->prefix . '_user_id',
			[
				'label'   => __( 'User ID', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'dynamic' => array(
					'active'     => true,
					'categories' => array(
						DynamicTags::BASE_GROUP,
						DynamicTags::TEXT_CATEGORY,
						DynamicTags::URL_CATEGORY,
						DynamicTags::GALLERY_CATEGORY,
						DynamicTags::IMAGE_CATEGORY,
						DynamicTags::MEDIA_CATEGORY,
						DynamicTags::POST_META_CATEGORY,
						DynamicTags::NUMBER_CATEGORY,
						DynamicTags::COLOR_CATEGORY,
					),
				),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'user_data', [
				'label'       => __( 'User Data Field', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => [
					'user_login'       => 'Username',
					'display_name'     => 'Display Name',
					'nickname'         => 'Nickname',
					'first_name'       => 'First Name',
					'last_name'        => 'Last Name',
					'password'         => 'Password',
					'confirm_password' => 'Confirm Password',
					'user_email'       => 'Email',
					'description'      => 'Description',
				],
				'label_block' => true,
			]
		);


		$repeater->add_control(
			'keep_logged_in', [
				'label'     => __( 'Keep User Logged In', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'condition' => [
					'user_data' => 'password'
				]
			]
		);

		$repeater->add_control(
			'field_id', [
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$widget->add_control(
			$this->prefix . '_user_data',
			[
				'label'       => __( 'User Data', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'meta_field', [
				'label'       => __( 'Meta Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'field_id', [
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'is_image', [
				'label' => __( 'Image Meta Field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$repeater->add_control(
			'store_image', [
				'label'       => __( 'Image Field Storing Options', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'condition'   => [
					'is_image' => 'yes',
				],
				'description' => __(' For jet engine gallery field use comma separated values', 'gloo_for_elementor'),
				'default'     => 'id',
				'options'     => [
					'id'                     => 'Image ID',
					'url'                    => 'Image URL',
					'array'                  => 'Array of Image ID and URL',
					'array_multi_id'         => 'Array of multiple image IDs',
					'array_multi_url'        => 'Array of multiple image URLs',
					'comma_separated_string' => 'Comma Separated String of IDs',
					'comma_separated_string_url' => 'Comma Separated String of Urls',
				]
			]
		);

		$repeater->add_control(
			'store_image_meta_type', [
				'label'       => __( 'Image Field Storing Type', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'condition'   => [
					'is_image' => 'yes',
					// 'is_relation_field' => ''
				],
				'default'     => 'default',
				'options'     => [
					'default'                     => 'Default',
					'jet_engine'                    => 'Jet Engine',
					'acf'                  => 'Advanced Custom Field',
				]
			]
		);

		$widget->add_control(
			$this->prefix . '_meta_fields',
			[
				'label'       => __( 'Meta Fields', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
			]
		);

		$terms_repeater = new \Elementor\Repeater();

		$terms_repeater->add_control(
			'term_field', [
				'label'       => __( 'Term Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$terms_repeater->add_control(
			'field_id', [
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$widget->add_control(
			$this->prefix . '_term_fields',
			[
				'label'       => __( 'Term Fields', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $terms_repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
			]
		);

		$post_types_repeater = new \Elementor\Repeater();
		$post_types_repeater->add_control(
			'meta_field', [
				'label'       => __( 'Meta Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$post_types_repeater->add_control(
			'field_id',
			[
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Must return a list of post IDs', 'gloo_for_elementor' ),
			]
		);

		$post_types_repeater->add_control(
			'is_relation_field', [
				'label' => __( 'Is Relation Field?', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					''           => 'None',
					'acf'        => 'Advanced Custom Fields (ACF)',
				]
			]
		);

		$widget->add_control(
			$this->prefix . '_post_type_fields',
			[
				'label'       => __( 'Post Type', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $post_types_repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
			]
		);
		
		if(function_exists( 'buddypress' )) {

			$buddyboss_repeater = new \Elementor\Repeater();

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
			
			$buddyboss_repeater->add_control(
				'is_profile_type',
				[
					'label' => esc_html__( 'Is Profile Type ?', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
					'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);
						
			$buddyboss_repeater->add_control(
				'is_multi_field',
				[
					'label' => esc_html__( 'Is Multi Field ?', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'gloo_for_elementor' ),
					'label_off' => esc_html__( 'Hide', 'gloo_for_elementor' ),
					'return_value' => 'yes',
					'default' => 'no',
					'condition' => [
						'is_profile_type!' => 'yes',
					],
				]
			);
			
			$buddyboss_repeater->add_control(
				'bb_profile_field_id', [
					'label'       => __( 'Profile Field', 'gloo_for_elementor' ),
					'type'    => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'options' => $variables,
					'condition' => [
						'is_profile_type!' => 'yes',
					],
				]
			);

			$buddyboss_repeater->add_control(
				'field_id',
				[
					'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
					'label_block' => true,
					'type'        => \Elementor\Controls_Manager::TEXT,
					// 'description' => __( 'Must return a list of post IDs', 'gloo_for_elementor' ),
				]
			);


			// $buddyboss_repeater->add_control(
			// 	'is_relation_field', [
			// 		'label' => __( 'Is Relation Field?', 'gloo_for_elementor' ),
			// 		'type'        => \Elementor\Controls_Manager::SELECT,
			// 		'options'     => [
			// 			''           => 'None',
			// 			'acf'        => 'Advanced Custom Fields (ACF)',
			// 		]
			// 	]
			// );

			$widget->add_control(
				$this->prefix . '_buddyboss_fields',
				[
					'label'       => __( 'BuddyBoss Fields', 'gloo_for_elementor' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $buddyboss_repeater->get_controls(),
					'title_field' => '{{{ field_id }}}',
					'separator'   => 'after',
				]
			);
		}
		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 *
	 * @param array $element
	 */
	public function on_export( $element ) {
	}
}