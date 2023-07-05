<?php

namespace Gloo\Modules\Form_User_Submission;
 
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend_User_Registration extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'frontend_user_registration';
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
		return __( 'Frontend User Registration', 'gloo_for_elementor' );
	}

	private $prefix = 'gloo_frontend_user_reg';

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
			$fields[ $id ] = sanitize_text_field( $field['value'] );
		}


		$user_role = $settings[ $this->prefix . '_role' ] ?: 'subscriber';

		$username_field        = $settings[ $this->prefix . '_username' ] ?: 'username';
		$password_field        = $settings[ $this->prefix . '_password' ] ?: 'password';
		$repeat_password_field = $settings[ $this->prefix . '_repeat_password' ] ?: '';

		$username = isset( $fields[ $username_field ] ) ? $fields[ $username_field ] : '';
		$password = isset( $fields[ $password_field ] ) ? $fields[ $password_field ] : '';

		// confirm repeat password
		if ( ( $repeat_password_field && isset( $fields[ $repeat_password_field ] ) ) && $fields[ $repeat_password_field ] != $password ) {
			return $ajax_handler->add_error( $repeat_password_field, 'Both passwords must match' );
		}

		$meta_fields      = $settings[ $this->prefix . '_meta_fields' ];
		$term_fields      = $settings[ $this->prefix . '_term_fields' ];
		$post_type_fields = $settings[ $this->prefix . '_post_type_fields' ];
		$user_data_fields = $settings[ $this->prefix . '_user_data' ];
		$buddyboss_fields = $settings[ $this->prefix . '_buddyboss_fields' ];

		if ( $settings[ $this->prefix . '_require_email' ] === 'yes' ) {
			// email required from user
			$email_field = $settings[ $this->prefix . '_email' ] ?: 'email';
			$user_email  = isset( $fields[ $email_field ] ) ? $fields[ $email_field ] : '';
		} else {
			// email generated from domain name
			$domain       = $_SERVER['HTTP_HOST'];
			$email_suffix = $settings[ $this->prefix . '_email_suffix' ] ? str_replace( "@", "", $settings[ $this->prefix . '_email_suffix' ] ) : $domain;
			$user_email   = "{$username}@{$email_suffix}";
		}


		// gather user data
		$user_data = [];

		if ( ! empty( $user_data_fields ) ) {
			foreach ( $user_data_fields as $item ) {

				if ( ! isset( $fields[ $item['field_id'] ] ) || empty( $fields[ $item['field_id'] ] || empty( $item['user_data'] ) ) ) {
					continue;
				}

				$user_data[ $item['user_data'] ] = $fields[ $item['field_id'] ];
			}
		}


		// Create post object
		$user_to_insert = array(
			'user_pass'  => $password,
			'user_login' => $username,
			'user_email' => $user_email,
			'role'       => $user_role
		);

		if ( ! empty( $user_data ) ) {
			$user_to_insert = array_merge( $user_to_insert, $user_data );
		}


		// Insert the post into the database
		$user_id = wp_insert_user( $user_to_insert );
		if ( is_wp_error( $user_id ) ) {

			$error_message = $user_id->get_error_message();
			$error_field   = $username_field;

			if ( strpos( $user_id->get_error_message(), "password" ) !== false ) {
				$error_field = $password_field;
			}
			if ( strpos( $user_id->get_error_message(), "email" ) !== false && isset( $email_field ) ) {
				$error_field = $email_field;
			}

			$ajax_handler->add_error( $error_field, $error_message );
		}

		// user exists at this point

		// set meta data
		if ( ! empty( $meta_fields ) ) {
			foreach ( $meta_fields as $meta_field ) {

				if ( ! isset( $fields[ $meta_field['field_id'] ] ) || empty( $fields[ $meta_field['field_id'] ] || empty( $meta_field['meta_field'] ) ) ) {
					continue;
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
					} elseif($post_type_field['is_relation_field'] == 'jet_engine') {
						$jet_engine_relation_key[] = array('key' => $post_type_field['meta_field'], 'post_id' => $value);
					}
				}

				if ( get_user_meta( $user_id, $post_type_field['meta_field'] ) ) {
					update_user_meta( $user_id, $post_type_field['meta_field'], $value );
				} else {
					add_user_meta( $user_id, $post_type_field['meta_field'], $value );
				}
			}
		}
		
		if( ! empty( $buddyboss_fields ) && function_exists( 'buddypress' ) && $user_id) {
			foreach ( $buddyboss_fields as $buddyboss_field ) {
				
				
				if ( ! isset( $fields[ $buddyboss_field['field_id'] ] ) || empty( $fields[ $buddyboss_field['field_id'] ] ) ) {
					continue;
				}
 
				$is_profile_type = $buddyboss_field['is_profile_type'];
				$field_id = $buddyboss_field['bb_profile_field_id'];
				$is_multi_field = $buddyboss_field['is_multi_field'];
				$value = $fields[ $buddyboss_field['field_id'] ];
 				
				$field_type     = \BP_XProfile_Field::get_type( $field_id );

				if($value) {
					
					if($is_profile_type == 'yes') {
 
						$profile_type_post = get_post( $value );
						$profile_type_name = str_replace( '%', '', $profile_type_post->post_name );
						if ( ! $profile_type_name ) {
							return;
						}
						bp_set_member_type( $user_id, $profile_type_name );
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

		if ( $settings[ $this->prefix . '_login_after_register' ] === 'yes' ) {
			wp_set_current_user($user_id);
                          wp_set_auth_cookie($user_id);
                          $user  = get_user_by( 'ID', $user_id );
                          do_action( 'wp_login', $user->user_login, $user );
		}

		if($user_id) {
			$ajax_handler->add_response_data( 'frontend_user_id', $user_id );
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
				'label'     => __( 'Frontend User Registration', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		global $wp_roles;
		$user_roles = array();

		foreach ( $wp_roles->roles as $role_id => $role ) {
			$user_roles[ $role_id ] = $role['name'];
		}

		$widget->add_control(
			$this->prefix . '_role',
			[
				'label'       => __( 'Assign Role', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => 'subscriber',
				'options'     => $user_roles,
				'description' => __( 'The role that will be assigned to users registered with this form.', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_field_ids',
			[
				'label'     => __( 'Field IDs', 'plugin-name' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$widget->add_control(
			$this->prefix . '_username',
			[
				'label'       => __( 'Username', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Default: username', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_password',
			[
				'label'       => __( 'Password', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Default: password', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_repeat_password',
			[
				'label'       => __( 'Repeat Password', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Optional', 'gloo_for_elementor' ),
				'description' => __( 'Will be compared to the password field.', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_email',
			[
				'label'       => __( 'Email', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Default: email', 'gloo_for_elementor' ),
				'condition'   => [
					$this->prefix . '_require_email' => 'yes'
				],
			]
		);

		$domain = $_SERVER['HTTP_HOST'];
		$widget->add_control(
			$this->prefix . '_email_suffix',
			[
				'label'       => __( 'Email Domain', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( "Default: {$domain}", 'gloo_for_elementor' ),
				'description' => "Specify a domain, defaults to current domain.<br> Default: {$domain}<br> Example<b>@{$domain}</b>",
				'condition'   => [
					$this->prefix . '_require_email!' => 'yes'
				],

			]
		);


		$widget->add_control(
			$this->prefix . '_require_email',
			[
				'label'        => __( 'Require Email', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => 'Require email from user input, if set to false, an email address will be generated using the user provided username.',
				//'separator'    => 'after',
			]
		);
		
		$widget->add_control(
			$this->prefix . '_login_after_register',
			[
				'label'        => __( 'Auto Login ', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => __( 'Auto Login after successfull registration.', 'gloo_for_elementor' ),
				'separator'    => 'after',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'user_data', [
				'label'       => __( 'User Data Field', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => [
					'display_name' => 'Display Name',
					'nickname'     => 'Nickname',
					'first_name'   => 'First Name',
					'last_name'    => 'Last Name',
					'description'  => 'Description',
				],
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
					'jet_engine' => 'Jet engine',
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