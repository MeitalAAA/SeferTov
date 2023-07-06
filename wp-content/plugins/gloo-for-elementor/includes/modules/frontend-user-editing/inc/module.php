<?php

namespace Gloo\Modules\Form_User_Editing;
use ElementorPro\Modules\Forms\Module as Action_Module;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_user_editing';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_action( 'elementor_pro/init', [ $this, 'register_form_action' ] );
		add_action( 'wp_ajax_elementor_pro_forms_send_form', [ $this, 'ensure_get_parameters' ] );
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'check_user_editing_action' ], 10, 3 );

		add_action( 'elementor_pro/forms/process/date', [ $this, 'maybe_change_to_timestamp' ], 11, 3 );
		add_filter( 'elementor_pro/forms/render/item/date', [ $this, 'maybe_revert_to_date_format' ], 10, 3 );
		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_save_as_timestamp_controls'
		], 11 );
		add_action('elementor_pro/forms/validation', [$this, 'elementor_pro_forms_validation'], 999, 2);
	}

	public function add_save_as_timestamp_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		if ( isset( $control_data['fields']['gloo_date_save_as_timestamp'] ) ) {
			return;
		}


		$field_controls =
			[
				'gloo_date_save_as_timestamp' => [
					'name'         => 'gloo_date_save_as_timestamp',
					'label'        => __( 'Save as timestamp', 'gloo' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'condition'    => [
						'field_type' => 'date',
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
			];


		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'required', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
	}

	public function maybe_revert_to_date_format( $item, $item_index, $element ) {

		$value        = $item['field_value'];
		$is_timestamp = ( (string) (int) $value === $value )
		                && ( $value <= PHP_INT_MAX )
		                && ( $value >= ~PHP_INT_MAX );

		if ( $is_timestamp && isset( $item['gloo_date_save_as_timestamp'] ) && $item['gloo_date_save_as_timestamp'] ) {
			$element->set_render_attribute( 'input' . $item_index, 'value', date( 'Y-m-d', $item['field_value'] ) );
		}

		return $item;

	}

	public function maybe_change_to_timestamp( $field, $record, $ajax_handler ) {

		$form_fields    = $record->get_form_settings( 'form_fields' );
		$field_settings = [];
		$field_id       = $field['id'];
		foreach ( $form_fields as $form_field ) {
			if ( $form_field['custom_id'] === $field_id ) {
				$field_settings = $form_field;
			}
		}

		if ( ! isset( $field_settings['gloo_date_save_as_timestamp'] ) || ! $field_settings['gloo_date_save_as_timestamp'] ) {
			return;
		}

		$value = strtotime( $field['value'] );
		$record->update_field( $field['id'], 'value', $value );
		$record->update_field( $field['id'], 'raw_value', $value );
	}

	public function check_user_editing_action( $item, $item_index, $element ) {

		$submit_actions = $element->get_settings_for_display( 'submit_actions' );

		if ( ! $submit_actions || ! in_array( "frontend_user_editing", $submit_actions ) ) {
			return $item;
		}

		$settings = $element->get_settings_for_display();

		$user_data        = $settings['gloo_frontend_user_editing_user_data'];
		$form_meta_fields = $settings['gloo_frontend_user_editing_meta_fields'];
		$form_term_fields = $settings['gloo_frontend_user_editing_term_fields'];
		$form_buddyboss_fields = $settings['gloo_frontend_user_editing_buddyboss_fields'];
		$user_id          = $settings['gloo_frontend_user_editing_user_id'];

		$item_custom_id = $item['custom_id'];

		$input_type = 'input';

		if ( $item['field_type'] === 'select' ) {
			$input_type = 'select';
		}

		$user_data_field_key = array_search( $item_custom_id, array_column( $user_data, 'field_id' ) );

		if ( $user_data_field_key !== false ) {

			$user_data_field_id = $user_data[ $user_data_field_key ]['user_data'];
			$data               = get_userdata( $user_id );
			$value              = $data && isset( $data->{$user_data_field_id} ) ? $data->{$user_data_field_id} : false;

			if ( $value ) {
				$item['field_value'] = $value;
				$element->add_render_attribute( $input_type . $item_index, 'value', $value );
			}

		}

		$field_key = array_search( $item_custom_id, array_column( $form_meta_fields, 'field_id' ) );
		if ( $field_key !== false && $form_meta_fields ) {
			$meta_field_id = $form_meta_fields[ $field_key ]['meta_field'];
			$value         = get_user_meta( $user_id, $meta_field_id, true );
			$store = $form_meta_fields[ $field_key ]['store_image'];
			if(isset($form_meta_fields[ $field_key ]['store_image_meta_type']) && $store == 'url' && $form_meta_fields[ $field_key ]['store_image_meta_type'] == 'acf')
				$store = 'id';

			if(!empty($value)) {
				$formated_ids = $this->get_image_attribute_render_value($store, $value);
			}

			if ( $form_meta_fields[ $field_key ]['is_image'] && !empty($value)) {

				if(isset($item['gloo_filepond_upload']) && $item['gloo_filepond_upload'] == 'yes') {
 					
					$attachments = array();

					if (!empty($formated_ids)) {
						if(is_array($formated_ids)) {
							foreach( $formated_ids as $val ) {
								$attachments[] = [
									'id' => $val
								];
							}
						}  else {
							$attachments[] = [
								'id' => $formated_ids
							];
						}

						$element->add_render_attribute( 'field-group' . $item_index, 'data-gloo-uploads', wp_json_encode($attachments) );
					}
				} else {
					// db($formated_ids);exit();
					 /* only for image meta values */ 
					// if($item_custom_id != $post_image_id) {
						$attachment_url = $item['gloo_upload_image_ui_placeholder']['url'];
						$attachment_url = wp_get_attachment_url($formated_ids);
						
						$element->add_render_attribute( 'field-group' . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ');');
						$element->set_render_attribute( 'field-group' . $item_index, 'data-background-image', $attachment_url);
						$element->set_render_attribute( $input_type . $item_index, 'data-background-image', $attachment_url );
// 						$element->add_render_attribute( $input_type . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ')' );
					// }
				}
					
			}else{

			
			if ( $value ) {
				$item['field_value'] = $value;
				if($item['field_type'] == 'acceptance') {	
					if($value == 'yes' || $value == 'on')
						$element->add_render_attribute( $input_type . $item_index, 'checked', 'checked');
				} else {
					$element->add_render_attribute( $input_type . $item_index, 'value', $value );
				}
				// $element->add_render_attribute( $input_type . $item_index, 'value', $value );
			}
			}

		}

		$field_key = array_search( $item_custom_id, array_column( $form_term_fields, 'field_id' ) );
		if ( $field_key !== false && $form_term_fields ) {
			$term_field_id = $form_term_fields[ $field_key ]['term_field'];
			$value         = get_user_meta( $user_id, $term_field_id, true );

			if ( $value ) {
				$item['gloo_checked_terms'] = $value;
			}
		}

		
		/* render buddyboss xprofile fields */
		if(is_array($form_buddyboss_fields)){
			$field_key = array_search( $item_custom_id, array_column( $form_buddyboss_fields, 'field_id' ) );
		}
		
		if ( $field_key !== false && $form_buddyboss_fields && function_exists( 'buddypress' )) {

			$bb_profile_field_id = $form_buddyboss_fields[ $field_key ]['bb_profile_field_id'];
			$is_profile_type = $form_buddyboss_fields[ $field_key ]['is_profile_type'];
			$is_range_slider = $form_buddyboss_fields[ $field_key ]['is_range_slider'];
			$field_type = $item['field_type'];
				
			if($is_profile_type == 'yes' ) {
				$member_type = bp_get_member_type($user_id);
				$member_type_post_id = bp_member_type_post_by_type( $member_type );
				$value = (array)$member_type_post_id;
		
				if ( $value ) {
					$this->render_multiple_values_field($item, $item_index, $element, $value);
				}
			} elseif($is_range_slider == 'yes') {
				$bb_profile_range_field_1 = sanitize_text_field($form_buddyboss_fields[ $field_key ]['bb_profile_range_field_1']);
				$bb_profile_range_field_2 = sanitize_text_field($form_buddyboss_fields[ $field_key ]['bb_profile_range_field_2']);

				$value1 = xprofile_get_field_data($bb_profile_range_field_1, $user_id);
				$value2 = xprofile_get_field_data($bb_profile_range_field_2, $user_id);
				$range_value = $value1.','.$value2;

				$item['field_value'] = $range_value;
				$element->add_render_attribute( $input_type . $item_index, 'value', $range_value );
				
			} else {

				$value = xprofile_get_field_data($bb_profile_field_id, $user_id);
				$bb_field_type     = \BP_XProfile_Field::get_type( $bb_profile_field_id );
				
				if ( $value ) {	
					if($bb_field_type == 'datebox') {
						//echo '<pre>'; print_r($item); echo '</pre>';
						$value = str_replace( '/', '-', $value );
						$date  = new \DateTime( "$value" );
						if ( $value ) {
							$value = $date->format( 'Y-m-d' );
						}
					}
					
					if($field_type == 'checkbox' || $field_type == 'radio' || $field_type == 'select' ) {
						$this->render_multiple_values_field($item, $item_index, $element, $value);
					} elseif( $field_type == 'gloo_wysiwyg' ) {		
						$value = empty( $value ) ? '' : $value;
						$item['field_value'] = $value;
					} elseif($field_type == 'gloo_range_field') {
						$item['field_value'] = $value;
						
					} else {
						$item['field_value'] = $value;
						$element->add_render_attribute( $input_type . $item_index, 'value', $value );
					}
				//  print_r($value);
				}
			}
		}

		return $item;
	}


	public function get_image_attribute_render_value( $store, $saved_value ) {
		$values = array();

	 switch ( $store ) {
		 case 'url' :
			 $value = attachment_url_to_postid($saved_value);
			 break;
		 case 'array_multi_url' :
			 foreach( $saved_value as $val ) {
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
			 $value = explode( ",", $saved_value );
			 break;
		 case 'comma_separated_string_url' :
			 $urls = explode( ",", $saved_value );
			 foreach( $urls as $val ) {
				 $value[] = attachment_url_to_postid($val);
			 }
			 break;
		 case 'id' :
			 $value = $saved_value;
			 break;
		 default:
			 $value[] = $saved_value;

	 }

	 return $value;
 }

	public function render_multiple_values_field($item, $item_index, $element, $value) {
		$field_type = $item['field_type'];

		if ( $value ) {
			
			$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
		//	print_r($options);
			
			if ( ! $options ) {
				return '';
			}
			
			/* render checked checkbox/radio field*/
			if($field_type == 'checkbox' || $field_type == 'radio') {
				$item['field_value'] = $value;

				foreach ( $options as $key => $option ) {
					$element_id = $item['custom_id'] . $key;
					$option_label = $option;
					$option_value = $option;

					if ( false !== strpos( $option, '|' ) ) {
						list( $option_label, $option_value ) = explode( '|', $option );
					}
					
					if ( ! empty( $value ) && in_array($option_value, $value) ) {
						$element->add_render_attribute( $element_id, 'checked', 'checked' );
					}
				}
			/* render selected select field*/
			} elseif( $field_type == 'select' ) {
				if(is_array($value)) {
					$item['field_value'] = $value;
				} else {
					$item['field_value'] = (array)$value;
				}
			
				foreach ( $options as $key => $option ) {
					$option_id = $item['custom_id'] . $key;
					$option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}

					// Support multiple selected values
					if ( ! empty( $item['field_value'] ) && in_array( $option_value, $item['field_value']) ) {
						$element->add_render_attribute( $option_id, 'selected', 'selected' );
					}
				}
			}
		}
	}

	public function ensure_get_parameters() {
		$query_params = parse_url( wp_get_referer(), PHP_URL_QUERY );
		if ( $query_params ) {
			global $_GET;
			$new_parameters = [];
			parse_str( $query_params, $new_parameters );
			$_GET = $_GET + $new_parameters;
		}
	}

	public function register_form_action() {

		// Include the form actions
		foreach ( glob( gloo()->modules_path( 'frontend-user-editing/inc/form-action/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'Frontend_User_Editing'
		];

		// register tags
		foreach ( $classes as $class ) {

			$class       = "Gloo\Modules\Form_User_Editing\\{$class}";
			$form_action = new $class;

			// Register the action with form widget
			\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );
		}

	}
	
	public function elementor_pro_forms_validation($record, $ajax_handler) {
		
		$submit_actions = $record->get_form_settings( 'submit_actions' );

		$module = Action_Module::instance();
		$actions = $module->get_form_actions();

		foreach ( $actions as $action ) {
			if ( ! in_array( $action->get_name(), $submit_actions, true ) ) {
				continue;
			}

			$settings = $record->get( 'form_settings' );
 			$raw_fields = $record->get( 'fields' );
 			$user_data_fields = $settings[ 'gloo_frontend_user_editing_user_data' ];

			// Normalize the Form Data
			$fields = [];
			foreach ( $raw_fields as $id => $field ) {
				$fields[ $id ] = $field['value'];
			}
 
			/* user exist validation */
		 	if ( $action->get_name() == 'frontend_user_editing' ) {

				$editing_user_id = $settings[ 'gloo_frontend_user_editing_user_id'];
				if(empty($editing_user_id)){
					wp_send_json_error( [
						'message' => __('User does not exist with specified ID.', 'gloo_for_elementor'),
						'data' => $ajax_handler->data,
					] ); die();
				}
				$user_with_id = get_user_by('id', $editing_user_id);
				if(!$user_with_id) {
					wp_send_json_error( [
						'message' => __('User does not exist with specified ID.', 'gloo_for_elementor'),
						'data' => $ajax_handler->data,
					] ); die();
				}
				$user_data = [];

				if ( ! empty( $user_data_fields ) ) {

					foreach ( $user_data_fields as $item ) {

						if ( ! isset( $fields[ $item['field_id'] ] ) || empty( $fields[ $item['field_id'] ] || empty( $item['user_data'] ) ) ) {
							continue;
						}
 						
						/* check if user email already exist */
						if ( $item['user_data'] === 'user_email' && $fields[ $item['field_id'] ] ) { 
							$field_id = $item['field_id']; 
							$user_email = $fields[$item['field_id']];
							$field_key = array_search( 'user_email', array_column( $user_data_fields, 'user_data' ) );

							$user = get_user_by('email', $user_email);
							
							if($user && $user->ID !== $user_with_id->ID) {
								wp_send_json_error( [
									'message' => __('User already registered with this email.', 'gloo_for_elementor'),
									'data' => $ajax_handler->data,
								] ); die();
 							}
						}
					}
		 
 				}
			}
		}
	}
 
	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}