<?php
namespace Gloo\Modules\Form_Post_Submission;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend_Post_Submission extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	private $prefix = 'gloo_frontend_post_creation';
	private $subPrefix = '';

	public function __construct($name = 'gloo_frontend_post_creation', $namePrefix = ''){
		if(!empty($namePrefix) && $namePrefix && is_numeric($namePrefix) && $namePrefix >= 2){
			$this->subPrefix = $namePrefix;			
		}
		$this->prefix .= $this->get_sub_prefix('_parent_');
	}

	public function get_sub_prefix($glue_string = '_'){
		$output = '';
		if(!empty($this->subPrefix) && $this->subPrefix && is_numeric($this->subPrefix) && $this->subPrefix >= 2)
			$output = $glue_string.$this->subPrefix;
		return $output;
	}

	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'frontend_post_creation'.$this->get_sub_prefix();
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
		return __( 'Frontend Post Submission', 'gloo_for_elementor' ).$this->get_sub_prefix(' ');
	}

	

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
		$field_with_settings = $record->get_form_settings('form_fields');
		/* get all media uploads if upload is set to media */
		$media_uploads = $record->get( 'uploaded_to_media' );

		// Get submitted form data
		$raw_fields = $record->get( 'fields' );
		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {

			if($id == $settings[ $this->prefix . '_post_content' ]){
				//$fields[$id] = sanitize_textarea_field($field['value'] );
				global $allowedposttags;
        $fields[$id] = trim(wp_kses( $field['value'], $allowedposttags));
			}elseif($field['type'] == 'gloo_wysiwyg'){
				global $allowedposttags;
				$fields[$id] = trim(wp_kses( $field['value'], $allowedposttags));
			}
			else
				$fields[ $id ] = sanitize_text_field( $field['value'] );
		}
		 

		if ( $settings[ $this->prefix . '_registered_only' ] === 'yes' ) {
			$is_allowed     = false;
			$selected_roles = $settings[ $this->prefix . '_roles' ];
			$user           = wp_get_current_user();

			// registered users only, but no specific role
			if ( is_user_logged_in() && empty( $selected_roles ) ) {
				$is_allowed = true;
			}

			// specific role
			if ( $user && ! empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $user_role ) {
					if ( in_array( $user_role, $selected_roles ) ) {
						$is_allowed = true;
					}
				}
			}

			// check if user is allowed
			if ( ! $is_allowed ) {
				$ajax_handler->add_error( $raw_fields['content'], 'Insufficient permissions.' );
			}
		}


		$post_title_id   = $settings[ $this->prefix . '_post_title' ] ?: 'title';
		$post_content_id = $settings[ $this->prefix . '_post_content' ] ?: 'content';
		$post_image_id   = $settings[ $this->prefix . '_post_image' ] ?: '';
		$term_repeater   = $settings[ $this->prefix . '_post_terms_repeater' ] ?: '';
		$is_repeater_field   = $settings[ $this->prefix . '_is_repeater_field' ] ?: '';
		$title_multiple   = $settings[ $this->prefix . '_post_title_multiple' ];
		$post_title_ids   = $settings[ $this->prefix . '_post_title_ids' ];
		
		if( 'yes' == $title_multiple) {

			if(isset($post_title_ids)) {
				$title = explode(',', $post_title_ids);
				$field_titles = array();
				
				if(!empty($title) && is_array($title)) {
					foreach( $title as $title_field ) {
						$title_trim = trim($title_field);
						if(isset( $fields[ $title_trim ] ) && isset($raw_fields[$title_trim]) && $raw_fields[$title_trim]['type'] == 'gloo_terms_field' && is_numeric($fields[ $title_trim ])){
							foreach($field_with_settings as $single_field){
								if(isset($single_field['custom_id']) && $single_field['custom_id'] == $title_trim && !empty($single_field['gloo_term_fields_by_tax'])){
									$term = get_term_by( 'id', $fields[ $title_trim ], $single_field['gloo_term_fields_by_tax']);
									if($term){
										$field_titles[] =  $term->name;
									}else{
										$field_titles[] =  isset( $fields[ $title_trim ] ) ? $fields[ $title_trim ] : '';
									}
									break;
								}
							}
						}
						else
							$field_titles[] =  isset( $fields[ $title_trim ] ) ? $fields[ $title_trim ] : '';
					}
				
					$post_title = implode(' ', $field_titles);	
				}
			}
			
		} else {
			$post_title = isset( $fields[ $post_title_id ] ) ? $fields[ $post_title_id ] : '';
		}

		$post_content = isset( $fields[ $post_content_id ] ) ? $fields[ $post_content_id ] : '';

		$meta_fields = $settings[ $this->prefix . '_meta_fields' ];
		$post_type_fields = $settings[ $this->prefix . '_post_type_fields' ];
		$user_type_fields = $settings[ $this->prefix . '_user_type_fields' ];
		$post_bidirectional_relation = array();

		$jet_engine_relation_key = [];
		$meta_data = [];
		$files     = $record->get( 'files' );

		if ( ! empty( $meta_fields ) ) {
			foreach ( $meta_fields as $meta_field ) {

				if ( ! isset( $fields[ $meta_field['field_id'] ] ) || empty( $fields[ $meta_field['field_id'] ] || empty( $meta_field['meta_field'] ) ) ) {
				// if ( ! isset( $fields[ $post_type_field['field_id'] ] ) || empty( $fields[ $meta_field['field_id'] ] || empty( $meta_field['meta_field'] ) ) ) {
					continue;
				}

				$value = $fields[ $meta_field['field_id'] ];
			
				// check if it is an image meta field
				if ( $meta_field['is_image'] ) {
					$store = $meta_field['store_image'];
					if(isset($meta_field['store_image_meta_type']) && $store == 'url' && $meta_field['store_image_meta_type'] == 'acf')
							$store = 'id';
							
					if ( isset( $media_uploads['uploaded_to_media'] ) && isset( $media_uploads['uploaded_to_media'][ $meta_field['field_id'] ] ) ) {

						$uploads_data = $media_uploads['uploaded_to_media'][$meta_field['field_id']];
						$value = $this->get_image_storage_value( $store, $uploads_data, $meta_field );
					}

				}

				if (isset($meta_field['is_relation_field']) && $meta_field['is_relation_field'] && $value) {
					if($meta_field['is_relation_field'] == 'acf' && !is_array($value)){
						$value = array($value);
					}elseif($meta_field['is_relation_field'] == 'jet_engine'){
						$jet_engine_relation_key[] = array('key' => $meta_field['meta_field'], 'post_id' => $value);
					}
				}

				$meta_data[ $meta_field['meta_field'] ] = $value;

				if (isset($meta_field['is_checkbox']) && $meta_field['checkbox_type'] == 'jet_engine' && $value && isset($raw_fields[$meta_field['field_id']])) {
					$raw_field = $raw_fields[$meta_field['field_id']];
					$field_with_settings = $record->get_form_settings('form_fields');
					$single_field_with_settings = array();
					if(isset($raw_field['raw_value']) && is_array($raw_field['raw_value']) && count($raw_field) >= 1){
						foreach($field_with_settings as $single_field){
							if(isset($single_field['custom_id']) && $single_field['custom_id'] == $meta_field['field_id']){
								$single_field_with_settings = $single_field;
								break;
							}
						}
						if(!empty($single_field_with_settings) && isset($single_field_with_settings['field_options'])){
							$options_array = $this->convert_pipe_string_to_array($single_field_with_settings['field_options']);
							if(!empty($options_array)){
								$value_with_selected_options = array();
								foreach($options_array as $key=>$value){
									$key = trim($key);
									$current_option_is_selected = false;
									foreach($raw_field['raw_value'] as $single_raw_value){
										if($single_raw_value){
											$single_raw_value = trim(sanitize_text_field( $single_raw_value));
											if($single_raw_value == $key){
												$current_option_is_selected = true;
											}
										}
									}
									$value_with_selected_options[$key] = $current_option_is_selected;
								}
								if(!empty($value_with_selected_options)){
									$meta_data[ $meta_field['meta_field'] ] = $value_with_selected_options;
								}
							}
						}
					}
				}

				if (isset($meta_field['is_gloo_datepicker']) && $value && isset($meta_field['is_timestamp']) && $meta_field['is_timestamp'] == 'yes') {

					if (strpos($value, "/")) {
						$date = str_replace('/', '-', $value);
						$value = strtotime($date);
						$meta_data[ $meta_field['meta_field'] ] = $value;
 					}
				}
			}
		}
		
		$post_type_jet_relation = array();
		$post_type_new_jet_relation = array();

		/* post types field */
		if ( ! empty( $post_type_fields ) ) {
			foreach ( $post_type_fields as $post_type_field ) {

				if ( ! isset( $fields[ $post_type_field['field_id'] ] ) || empty( $fields[ $post_type_field['field_id'] ] || empty( $post_type_field['meta_field'] ) ) ) {
					continue;
				}

				$value = $fields[ $post_type_field['field_id'] ];
				$value = $this->get_post_ids_by_return_value($field_with_settings, $post_type_field, $value);
				
				if (isset($post_type_field['is_relation_field']) && $post_type_field['is_relation_field'] && $value) {

					if($post_type_field['is_relation_field'] == 'acf'){
						if(!is_array($value)){
							$arr_values = explode(',', $value);

							if(!empty($arr_values) && is_array($arr_values)) {
								$value = $arr_values;
							}
						}						
						if(isset($post_type_field[$this->prefix.'_is_bi_directional_relation_field']) && $post_type_field[$this->prefix.'_is_bi_directional_relation_field'] == 'yes' && isset($post_type_field[$this->prefix.'_bi_directional_relation_field_id']) && !empty($post_type_field[$this->prefix.'_bi_directional_relation_field_id'])){
							$post_bidirectional_relation[] = array(
								'type' => 'acf',
								'first_relation_key' => $post_type_field['meta_field'],
								'value' => $value,
								'second_relation_key' => $post_type_field[$this->prefix.'_bi_directional_relation_field_id'],
							);
						}
					}
					elseif($post_type_field['is_relation_field'] == 'jet_engine'){

						$arr_values = explode(',', $value);

						if(!empty($arr_values) && is_array($arr_values)) {
							foreach( $arr_values as $jet_value ) {
								$post_type_jet_relation[] = array('key' => $post_type_field['meta_field'], 'value' => $jet_value);
							}
 						}
					}
					elseif($post_type_field['is_relation_field'] == 'new_jet_engine_relation'){

						$jet_relation = $post_type_field['jet_relation'];
						$jet_rel_context = $post_type_field['jet_rel_context'];
						
						$arr_values = explode(',', $value);

						if(!empty($arr_values) && is_array($arr_values)) {
							$post_type_new_jet_relation[] = array(
								'jet_relation' => $jet_relation,
								'jet_rel_context' => $jet_rel_context,
								'jet_values' => $arr_values
							);
 						}
					}
				}
				
				if(!in_array($post_type_field['is_relation_field'], array('jet_engine', 'new_jet_engine_relation'))) {
				//if($post_type_field['is_relation_field'] != 'jet_engine') {
					$meta_data[ $post_type_field['meta_field'] ] = $value;
				}
			}
		}

		/* user types field */
		if ( ! empty( $user_type_fields ) ) {
			foreach ( $user_type_fields as $user_type_field ) {

				if ( ! isset( $fields[ $user_type_field['field_id'] ] ) || empty( $fields[ $user_type_field['field_id'] ] ) ) {
					continue;
				}

				$value = $fields[ $user_type_field['field_id'] ];

				if (isset($user_type_field['meta_field_type']) && $user_type_field['meta_field_type'] && $value) {
					if($user_type_field['meta_field_type'] == 'acf_rel' && !is_array($value)){
				 
						$value = array_map('trim',array_filter(explode(',',$value)));
												
						if(!empty($arr_values) && is_array($arr_values)) {
							$value = $arr_values;
						}
					} elseif($user_type_field['meta_field_type'] == 'jet_engine_rel'){
						$jet_relation = $user_type_field['jet_relation'];
						$jet_rel_context = $user_type_field['jet_rel_context'];

						$jet_relation_object[] = array(
							'jet_relation' => $jet_relation,
							'jet_rel_context' => $jet_rel_context,
							'jet_values' => explode(',', $value)
						);
					} 
				}
				
				if($user_type_field['meta_field_type'] != 'jet_engine_rel') {
					$meta_data[ $user_type_field['meta_field'] ] = $value;
				}
			}
		}
 
		if ( isset( $settings[ $this->prefix . '_post_type' ] ) && ! empty( $settings[ $this->prefix . '_post_type' ] ) ) {
			// Create post object
			$post_to_insert = array(
				'post_title'   => wp_strip_all_tags( $post_title ),
				'post_content' => $post_content,
				'post_status'  => $settings[ $this->prefix . '_post_status' ],
				'post_type'    => $settings[ $this->prefix . '_post_type' ],
			);

			if ( ! empty( $meta_data ) ) {
				$post_to_insert['meta_input'] = $meta_data;
			}
			
			// Insert the post into the database
			$insert = wp_insert_post( $post_to_insert );

			if(isset($jet_engine_relation_key) && !empty($jet_engine_relation_key) && is_array($jet_engine_relation_key) && $insert){
				foreach($jet_engine_relation_key as $relation_key){
					add_post_meta( $relation_key['post_id'], $relation_key['key'], $insert );
				}
			}

			if(isset($post_type_jet_relation) && !empty($post_type_jet_relation) && is_array($post_type_jet_relation) && $insert){
				foreach($post_type_jet_relation as $post_type_relation){
					add_post_meta( $insert, $post_type_relation['key'], $post_type_relation['value'] );
				}
			}
			
			if(isset($post_type_new_jet_relation) && !empty($post_type_new_jet_relation) && is_array($post_type_new_jet_relation) && $insert){

 				foreach($post_type_new_jet_relation as $relation_object){
 
					$relation_instance = jet_engine()->relations->get_active_relations( $relation_object['jet_relation'] );
					$relations_values = $relation_object['jet_values'];
					$jet_rel_context = $relation_object['jet_rel_context'];

				//	echo '<pre>'; print_r($relations_values); echo '</pre>';

					/* insert Jet 2.11 Relation meta */
					if( isset($relation_instance) && !empty($relation_instance)) {
						if ( 'child_object' === $jet_rel_context ) {
							/**
							 * We updating children items from the parent object,
							 * this mean we need to delete all existing children for the parent and set up new
							 */
					
							// First of all completely delete all existing rows for the current parent
							$relation_instance->delete_rows( $insert );

							foreach ( $relations_values as $c_id ) {
								//$c_id = str_replace(' ','', $c_id);
								$relation_instance->update( $insert, $c_id );
							}
						} else {
							$child_id  = $insert;
							/**
							 * We updating parent items from the child object,
							 * this mean we need to delete all existing parents for the processed child and set up new
							 */
							
							// First of all completely delete all existing rows for the current child
							$relation_instance->delete_rows( false, $child_id );

							foreach ( $relations_values as $par_id ) {
								// $par_id = str_replace(' ','', $par_id);
								$relation_instance->update( $par_id, $child_id );
							}
						}
					}
				}
			}

			if ( ! $insert ) {
				$ajax_handler->add_error( $raw_fields['content'] );
			}
 
			// featured image
			if ( $post_image_id && isset( $media_uploads['uploaded_to_media'] ) && isset( $media_uploads['uploaded_to_media'][ $post_image_id ] ) ) {
				$attachment_id = $media_uploads['uploaded_to_media'][ $post_image_id ][0];
				set_post_thumbnail( $insert, $attachment_id );
			}

			// post terms
			if ( ! empty( $term_repeater ) ) {

				$terms = [];
				foreach ( $term_repeater as $term_item ) {
					if ( ! isset( $fields[ $term_item['field_id'] ] ) || ! $fields[ $term_item['field_id'] ] ) {
						continue;
					}

					$value = $fields[ $term_item['field_id'] ]; 
					$value = $this->get_term_ids_by_return_value($field_with_settings, $term_item, $value);
 
 					foreach ( explode( ',', $value ) as $term_id ) {
						$term                       = get_term( $term_id );
						$terms[ $term->taxonomy ][] = (int)$term_id;
					}
				}

				foreach ( $terms as $taxonomy => $term_ids ) {
					wp_set_post_terms( $insert, $term_ids, $taxonomy, true );
				}
			}
			
			if(!empty($jet_relation) && !empty($jet_rel_context)) {
				$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );
				$relations_values = explode(',', $value);
			}
			
			if(isset($jet_relation_object) && !empty($jet_relation_object)) {
				foreach( $jet_relation_object as $relation_object) {

					$relation_instance = jet_engine()->relations->get_active_relations( $relation_object['jet_relation'] );
					$relations_values = $relation_object['jet_values'];
					$jet_rel_context = $relation_object['jet_rel_context'];

					/* insert Jet 2.11 Relation meta */
					if( isset($relation_instance) && !empty($relation_instance)) {
						if ( 'child_object' === $jet_rel_context ) {
							/**
							 * We updating children items from the parent object,
							 * this mean we need to delete all existing children for the parent and set up new
							 */
					
							// First of all completely delete all existing rows for the current parent
							$relation_instance->delete_rows( $insert );

							foreach ( $relations_values as $c_id ) {
								$relation_instance->update( $insert, $c_id );
							}
						} else {
							$child_id  = $insert;
							/**
							 * We updating parent items from the child object,
							 * this mean we need to delete all existing parents for the processed child and set up new
							 */
							
							// First of all completely delete all existing rows for the current child
							$relation_instance->delete_rows( false, $child_id );

							foreach ( $relations_values as $par_id ) {
								$relation_instance->update( $par_id, $child_id );
							}
						}
					}
				}
			}

			//repeater fields
			if( $is_repeater_field == 'yes' )
				$this->update_repeater_field_data($record, $settings, $insert);

			if ($insert){
				if(isset($post_bidirectional_relation) && !empty($post_bidirectional_relation) && is_array($post_bidirectional_relation) && count($post_bidirectional_relation) >= 1){
					foreach($post_bidirectional_relation as $single_bidirectional_relation){
						if(isset($single_bidirectional_relation['value']) && is_array($single_bidirectional_relation['value']) && count($single_bidirectional_relation['value']) >= 1){
							$field_data = array('key' => $single_bidirectional_relation['first_relation_key']);
							$this->update_bidirectional_relation($single_bidirectional_relation['value'], $insert, $field_data, $single_bidirectional_relation['first_relation_key'], $single_bidirectional_relation['second_relation_key']);
						}
					}
				}
				do_action('gloo/frontend_post_creation/save_post', $insert);
				$ajax_handler->add_response_data( 'frontend_post_id', $insert );
			}

		} else {
			$ajax_handler->add_error( $raw_fields['content'] );
		}


	}

	public function get_post_ids_by_return_value($field_with_settings, $post_type_field, $value) {

		if( empty($field_with_settings) || empty($value)) {
			return $value;
		}

		foreach($field_with_settings as $single_post_type_field) {
			if(isset($single_post_type_field['custom_id']) && $single_post_type_field['custom_id'] == $post_type_field['field_id']) {
				$post_type_field_settings = $single_post_type_field;
				break;
			}
		}

		$return_type = $post_type_field_settings['gloo_cpt_fields_input_return_value'];
		$post_type = $post_type_field_settings['gloo_cpt_fields_post_type'];
 
		if( $return_type == 'title' || $return_type == 'slug' ) {
			
			$post_ids = array();
			$arr_values = explode(',', $value);
			$arr_values = array_map('trim',$arr_values);

			if(!empty($arr_values)) {
				foreach( $arr_values as $arr_value ) {

					if( $return_type == 'title' ) {

 						$post_object = get_page_by_title( $arr_value, OBJECT, $post_type );
						$post_ids[] = $post_object->ID;

					} elseif($return_type == 'slug') {
						$post_ids[] = url_to_postid( $arr_value );
					}
				}
			}

			return implode(',',$post_ids);
		} else {
			return $value;
		}

	}

	public function get_term_ids_by_return_value($field_with_settings, $term_field, $value) {
 		
		if( empty($field_with_settings) || empty($value) ) {
			return $value;
		}

		foreach( $field_with_settings as $single_term_field ) {
			if(isset($single_term_field['custom_id']) && $single_term_field['custom_id'] == $term_field['field_id']) {
				$term_field_settings = $single_term_field;
				break;
			}
		}
 
		$return_type = $term_field_settings['gloo_term_fields_output_value_type'];

		$arr_values = explode(',', $value);
		$arr_values = array_map('trim',$arr_values);

		if( $return_type == 'title' || $return_type == 'slug' ) {
			
			$query = $term_field_settings['gloo_term_fields_query'];
			$taxonomy = $term_field_settings['gloo_term_fields_by_tax'];
 			$terms_ids = array();
 			  
			if(!empty($arr_values)) {
				foreach( $arr_values as $arr_value ) {
 
					if( $return_type == 'title' ) {

						$term_value = get_term_by( 'name', $arr_value ,$taxonomy); 
						$terms_ids[] = $term_value->term_id;

					} elseif($return_type == 'slug') {

						$term_value = get_term_by( 'slug', $arr_value ,$taxonomy); 
						$terms_ids[] = $term_value->term_id;;
					}
				}
			}
  
			return implode(',',$terms_ids);
		} else {
			return $value;
		}
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
	// public function get_image_storage_value( $store, $files, $meta_field ) {
	// 	switch ( $store ) {
	// 		case 'url' :
	// 			$value = wp_get_attachment_url( $files['uploaded_to_media'][ $meta_field['field_id'] ][0] );
	// 			break;
	// 		case 'array' :
	// 			$value = [
	// 				'id'  => $files['uploaded_to_media'][ $meta_field['field_id'] ][0],
	// 				'url' => wp_get_attachment_url( $files['uploaded_to_media'][ $meta_field['field_id'] ][0] ),
	// 			];
	// 			break;
	// 		case 'array_multi_id' :
	// 			$value = $files['uploaded_to_media'][ $meta_field['field_id'] ];
	// 			break;

	// 		case 'array_multi_url' :
	// 			$value = [];
	// 			foreach ( $files['uploaded_to_media'][ $meta_field['field_id'] ] as $image_id ) {
	// 				$value[] = wp_get_attachment_url( $image_id );
	// 			}
	// 			break;
	// 		case 'comma_separated_string' :
	// 			$value = implode( ",", $files['uploaded_to_media'][ $meta_field['field_id'] ] );
	// 			break;
	// 		case 'comma_separated_string_url' :
	// 			$value = implode( ",", $files['uploaded_to_media'][ $meta_field['field_id'] ] );

	// 			$urls = explode( ",", $saved_value );
	// 			foreach( $urls as $val ) {
	// 				$value[] = attachment_url_to_postid($val);
	// 			}
	// 			break;
	// 		default:
	// 			$value = $files['uploaded_to_media'][ $meta_field['field_id'] ][0];

	// 	}

	// 	return $value;
	// }

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

		$relation_items = array();

		if(function_exists('jet_engine')) {
			if(method_exists(jet_engine()->relations, 'get_active_relations')){
				$relations = jet_engine()->relations->get_active_relations();
				if(!empty($relations)) {
					foreach( $relations as $relation) {
						$id = $relation->get_id();
						$relation_name = $relation->get_relation_name();
						$relation_items[$id] = $relation_name;
					}
				}
			}
		}	
		
		$widget->start_controls_section(
			$this->prefix,
			[
				'label'     => __( 'Frontend Post Submission', 'gloo_for_elementor' ).$this->get_sub_prefix(' '),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$post_types = array();
		$types      = get_post_types( [], 'objects' );

		foreach ( $types as $type ) {
			$post_types[ $type->name ] = $type->label;
		}

		$widget->add_control(
			$this->prefix . '_post_type',
			[
				'label'   => __( 'Post Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $post_types,
				'default' => '',
			]
		);

		$widget->add_control(
			$this->prefix . '_post_status',
			[
				'label'       => __( 'Post Status', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => get_post_statuses(),
				'default'     => 'draft',
				'description' => __( 'Default: Draft', 'gloo_for_elementor' ),
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
			$this->prefix . '_post_title',
			[
				'label'       => __( 'Title', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Default: title', 'gloo_for_elementor' ),
				'description' => __( 'Add comma separated field id\'s for appending data to the post title', 'gloo_for_elementor' )
			]
		);

		$widget->add_control(
			$this->prefix . '_post_title_multiple',
			[
				'label' => esc_html__( 'Multiple fields ?', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'Enable if multiple fields needed in the post title', 'gloo_for_elementor' ),
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$widget->add_control(
			$this->prefix . '_post_title_ids',
			[
				'label'       => __( 'Multiple Field Id\'s', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add comma separated field id\'s for appending data to the post title', 'gloo_for_elementor' ),
				'condition' => [
					$this->prefix . '_post_title_multiple' => 'yes'
				]
 			]
		);

		$widget->add_control(
			$this->prefix . '_post_content',
			[
				'label'       => __( 'Content', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Default: content', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_post_image',
			[
				'label'       => __( 'Featured Image', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Optional', 'gloo_for_elementor' ),
				'description' => __( 'You must enable the <b>Upload To Media Library</b> option in the field settings', 'gloo_for_elementor' ),
				'separator'   => 'after',
			]
		);


		$term_repeater = new \Elementor\Repeater();

		$term_repeater->add_control(
			'field_id',
			[
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Must return a list of term IDs', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
			$this->prefix . '_post_terms_repeater',
			[
				'label'       => __( 'Terms', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $term_repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
				'prevent_empty' => false,
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
			'is_relation_field', [
				'label' => __( 'Is Relation Field?', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					''                     => 'None',
					'jet_engine'                    => 'Jet engine',
					'acf'                  => 'Advanced Custom Fields (ACF)',
				]
			]
		);
		
		$repeater->add_control(
			'is_image', [
				'label' => __( 'Image Meta Field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
				'condition'   => [
					'is_relation_field' => ''
				],
			]
		);

		$repeater->add_control(
			'store_image', [
				'label'       => __( 'Image Field Storing Options', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'condition'   => [
					'is_image' => 'yes',
					'is_relation_field' => ''
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
					'is_relation_field' => ''
				],
				'default'     => 'default',
				'options'     => [
					'default'                     => 'Default',
					'jet_engine'                    => 'Jet Engine',
					'acf'                  => 'Advanced Custom Field',
				]
			]
		);

		$repeater->add_control(
			'is_checkbox', [
				'label' => __( 'Checkbox Field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$repeater->add_control(
			'checkbox_type', [
				'label'       => __( 'Type', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'condition'   => [
					'is_checkbox' => 'yes',
				],
				'default'     => '',
				'options'     => [
					''                     => 'None',
					'acf'                    => 'Advanced Custom Field',
					'jet_engine'                  => 'Jet Engine',
				]
			]
		);

		$repeater->add_control(
			'is_gloo_datepicker', [
				'label' => __( 'Datepicker Field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$repeater->add_control(
			'is_timestamp', [
				'label' => __( 'Store As Timestamp', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Return date should have day, month, year mentioned for timestamp', 'gloo_for_elementor' ),
				'condition'   => [
					'is_gloo_datepicker' => 'yes',
				],
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
				'prevent_empty' => false,
			]
		);

		$post_types_repeater = new \Elementor\Repeater();
		$post_types_repeater->add_control(
			'meta_field', [
				'label'       => __( 'Meta Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'is_relation_field',
							'operator' => '!in',
							'value' => array('new_jet_engine_relation')
						],
						[
							'name' => 'is_relation_field',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$post_types_repeater->add_control(
			'field_id',
			[
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'This option won\'t work if return type of post field is custom field', 'gloo_for_elementor' ),
			]
		);

		$post_types_repeater->add_control(
			'is_relation_field', [
				'label' => __( 'Is Relation Field?', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					''           => 'None',
					'jet_engine' => 'Jet engine',
					'new_jet_engine_relation' => 'Jet engine 2.11+',
					'acf'        => 'Advanced Custom Fields (ACF)'
				]
			]
		);

		$post_types_repeater->add_control(
			$this->prefix.'_is_bi_directional_relation_field', [
				'label' => __( 'Is Bidirectional relation field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'condition'   => [
					'is_relation_field' => 'acf'
				],
			]
		);
		$post_types_repeater->add_control(
			$this->prefix.'_bi_directional_relation_field_id', [
				'label' => __( 'Bidirectional relation field ID', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
				'condition'   => [
					$this->prefix.'_is_bi_directional_relation_field' => 'yes'
				],
			]
		);

		if(!empty($relation_items)) {
			$post_types_repeater->add_control(
				'jet_relation', [
					'label' => __( 'Jet Relation', 'gloo_for_elementor' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $relation_items,
					'condition' => [
						'is_relation_field' => 'new_jet_engine_relation'
					],
				]
			);
		}

		$post_types_repeater->add_control(
			'jet_rel_context', [
				'label' => __( 'Context', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default' => 'child_object',
				'options'     => [
					'child_object'        => 'Child Object',
					'parent_object' => 'Parent Object',
				],
				'condition' => [
					'is_relation_field' => 'new_jet_engine_relation'
				],
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
				'prevent_empty' => false,
			]
		);

		/* users repeater */
		$user_types_repeater = new \Elementor\Repeater();
		$user_types_repeater->add_control(
			'meta_field', [
				'label'       => __( 'Meta Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'meta_field_type',
							'operator' => '!in',
							'value' => array('jet_engine_rel')
						],
						[
							'name' => 'meta_field_type',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$user_types_repeater->add_control(
			'field_id',
			[
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Must return a list of post IDs', 'gloo_for_elementor' ),
			]
		);

		$user_types_repeater->add_control(
			'meta_field_type', [
				'label' => __( 'Meta Field Type', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					''           => 'None',
					'jet_engine_rel' => 'Jetengine 2.11+ Relationship',
					'acf_rel'        => '(ACF) Relationship'
				]
			]
		);

		if(!empty($relation_items)) {
			$user_types_repeater->add_control(
				'jet_relation', [
					'label' => __( 'Jet Relation', 'gloo_for_elementor' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $relation_items,
					'condition' => [
						'meta_field_type' => 'jet_engine_rel'
					],
				]
			);
		}

		$user_types_repeater->add_control(
			'jet_rel_context', [
				'label' => __( 'Context', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default' => 'child_object',
				'options'     => [
					'child_object'        => 'Child Object',
					'parent_object' => 'Parent Object',
				],
				'condition' => [
					'meta_field_type' => 'jet_engine_rel'
				],
			]
		);

		$widget->add_control(
			$this->prefix . '_user_type_fields',
			[
				'label'       => __( 'User Types', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $user_types_repeater->get_controls(),
				'title_field' => '{{{ field_id }}}',
				'separator'   => 'after',
				'prevent_empty' => false,
			]
		);

		$widget->add_control(
			$this->prefix . '_registered_only',
			[
				'label'        => __( 'Registered Users Only', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);


		global $wp_roles;
		$user_roles = array();

		foreach ( $wp_roles->roles as $role_id => $role ) {
			$user_roles[ $role_id ] = $role['name'];
		}

		$widget->add_control(
			$this->prefix . '_roles',
			[
				'label'       => __( 'Roles', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $user_roles,
				'description' => __( 'Allow post creation by specific roles, defaults to all', 'gloo_for_elementor' ),
				'condition'   => [
					'registered_only' => 'yes'
				],
			]
		);



		$widget->add_control(
      $this->prefix.'_is_repeater_field',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Is repeater field?', 'gloo_for_elementor' ),
				'label_block'   => false,
      ]
    );

		$source_list = array(
			'' => __('--Select--', 'gloo'), 
			'jet_engine' => 'Jet Engine',
			'acf' => 'ACF'
		);


		$allowed_repeater_fields = array();
		for($i = 1; $i <= 10; $i++){
			$allowed_repeater_fields[$i] = $i;
		}

		$widget->add_control(
			$this->prefix.'_allowed_repeater_fields', array(
				'label' => __( 'Number of repeater fields', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				// 'default' => 'manual',
				'options' => $allowed_repeater_fields,
				'default' => 1,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
					],
				],
			)
		);

		
		for($i = 1; $i <= 10; $i++){
			$sub_prefix = "_".$i;
			if($i == 1)
				$sub_prefix = '';


			$widget->add_control(
				$this->prefix.$sub_prefix.'_dnm_dynamic_menu_hr',
				[
					'type' => \Elementor\Controls_Manager::DIVIDER,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
							['name' => $this->prefix.'_allowed_repeater_fields', 'operator' => '>=', 'value' => $i],
						],
					],
				]
			);

			$widget->add_control(
				$this->prefix.$sub_prefix.'_repeater_source', array(
					'label' => __( 'Repeater Source', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					// 'default' => 'manual',
					'options' => $source_list,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
							['name' => $this->prefix.'_allowed_repeater_fields', 'operator' => '>=', 'value' => $i],
						],
					],
				)
			);

			$widget->add_control(
				$this->prefix.$sub_prefix.'_form_repeater_id',
				[
					'label' => __( 'Form Repeater ID', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
							['name' => $this->prefix.'_allowed_repeater_fields', 'operator' => '>=', 'value' => $i],
						],
					],
					'dynamic'     => [
						'active' => true,
					],
				]
			);

			$widget->add_control(
				$this->prefix.$sub_prefix.'_source_repeater_id',
				[
					'label' => __( 'Source Repeater ID', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
							['name' => $this->prefix.'_allowed_repeater_fields', 'operator' => '>=', 'value' => $i],
						],
					],
					'dynamic'     => [
						'active' => true,
					],
				]
			);

			$repeater_fields_repeater = new \Elementor\Repeater();
			$repeater_fields_repeater->add_control(
				$this->prefix.$sub_prefix . '_form_sub_field',
				[
					'label' => __( 'Form Sub Field', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
				]
			);

			$repeater_fields_repeater->add_control(
				$this->prefix.$sub_prefix . '_source_sub_field',
				[
					'label' => __( 'Source Sub Field', 'gloo_for_elementor' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
				]
			);

			$widget->add_control(
				$this->prefix.$sub_prefix. '_repeater_subfields',
				[
					'label' => 'Repeater Sub Fields',
					'type'          => \Elementor\Controls_Manager::REPEATER,
					'prevent_empty' => false,
					'fields'        => $repeater_fields_repeater->get_controls(),
					'title_field'   => '{{{' . $this->prefix.$sub_prefix . '_form_sub_field}}}',
					'label_block'   => false,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => $this->prefix . '_is_repeater_field', 'operator' => '==', 'value' => 'yes'],
							['name' => $this->prefix.'_allowed_repeater_fields', 'operator' => '>=', 'value' => $i],
						],
					],
				]
			);

			

		}
		
		$widget->end_controls_section();

	}


	public function update_repeater_field_data($record, $settings, $post_id){
				
		$field_with_settings = $record->get_form_settings('form_fields');		
		$allowed_repeater_fields = (int)$settings[$this->prefix.'_allowed_repeater_fields'];
		

		if(!empty($allowed_repeater_fields) && $allowed_repeater_fields && $allowed_repeater_fields >= 1){
			for($i = 1; $i <= $allowed_repeater_fields; $i++){
				
				$new_repeater_with_values = array();

				$sub_prefix = "_".$i;
				if($i == 1)
					$sub_prefix = '';

				$repeater_source = $settings[$this->prefix.$sub_prefix.'_repeater_source'];
				$form_repeater_id = $settings[$this->prefix.$sub_prefix.'_form_repeater_id'];
				$source_repeater_id = $settings[$this->prefix.$sub_prefix.'_source_repeater_id'];
					
					
				$repeater_subfields   = $settings[ $this->prefix.$sub_prefix . '_repeater_subfields' ];
				if(!(is_array($repeater_subfields) && count($repeater_subfields) >= 1))
					$repeater_subfields = array();
				
				if(!empty($repeater_source) && !empty($form_repeater_id) && !empty($source_repeater_id) && !empty($field_with_settings)){
					
					$start_repeater = false;
					
					foreach($field_with_settings as $single_field){
						if($single_field['field_type'] == 'gloo_repeater_start_field' && $single_field['custom_id'] == $form_repeater_id && isset($_POST[$form_repeater_id]) && isset($_POST[$form_repeater_id]) && is_array($_POST[$form_repeater_id]) && count($_POST[$form_repeater_id]) >= 1){
							$start_repeater = true;
							break;
						}
					}
		
					if($start_repeater){
						
						foreach($_POST[$form_repeater_id] as $key=>$field_value){
							if(!empty($repeater_subfields)){
								foreach($repeater_subfields as $single_sub_field){
									$form_sub_field = $single_sub_field[$this->prefix.$sub_prefix .'_form_sub_field'];
									$source_sub_field = $single_sub_field[$this->prefix.$sub_prefix .'_source_sub_field'];
									$form_sub_field_value = '';
									if(!empty($source_sub_field)){
										if(!empty($form_sub_field) && isset($_POST['gloo_repeater_fields']) && isset($_POST['gloo_repeater_fields'][$form_sub_field]) && isset($_POST['gloo_repeater_fields'][$form_sub_field][$key])){
											$form_sub_field_value = sanitize_text_field(stripslashes(trim($_POST['gloo_repeater_fields'][$form_sub_field][$key])));
										}
										$new_repeater_with_values[$key][$source_sub_field] = $form_sub_field_value;
									}
								}
							}
						}
		
						if(!empty($new_repeater_with_values)){
							if($repeater_source == 'jet_engine')
								$this->save_jet_engine_repeater_data($post_id, $source_repeater_id, $new_repeater_with_values);
							else if($repeater_source == 'acf')
								$this->save_acf_repeater_data($post_id, $source_repeater_id, $new_repeater_with_values);
						}
		
					}
		
				}
			}
		}

		
	}

	public function save_jet_engine_repeater_data($post_id, $repeater_key, $data){
		$new_data_array = array();
		if(!empty($repeater_key) && !empty($data) && is_array($data) && count($data) >= 1){			
			foreach($data as $key=>$value){
				$new_data_array['item-'.$key] = $value;			
			}
		}
		if(!empty($new_data_array))
			update_post_meta($post_id, $repeater_key, $new_data_array);		
	}

	public function save_acf_repeater_data($post_id, $repeater_key, $data){
		$new_data_array = array();
		if(!empty($repeater_key) && !empty($data) && is_array($data) && count($data) >= 1){		
			foreach($data as $key=>$value){
				// $new_data_array['item-'.$key] = $value;
				if($value && is_array($value) && count($value) >= 1){
					foreach($value as $sub_field_key=>$sub_field_value){
						update_post_meta($post_id, $repeater_key.'_'.$key.'_'.$sub_field_key, $sub_field_value);
					}
				}
			}
			update_post_meta($post_id, $repeater_key, count($data));
		}
		// if(!empty($new_data_array))
		// 	update_post_meta($post_id, $repeater_key, $new_data_array);		
	}
	

	public function convert_pipe_string_to_array($string){
    $output = array();
    if($string){
      $options = preg_split( "/\\r\\n|\\r|\\n/", $string );
      if($options && !empty($options)){
        foreach ( $options as $key => $option ) {
          $option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}

					$option_value = trim($option_value);
					$option_label = trim($option_label);

			if($option_value)
			$output[$option_value] = $option_label;
			else
				$output[] = $option_label;
        }
      }      
    }    
    return $output;
  }

	public function update_bidirectional_relation($value, $post_id, $field, $key_a, $key_b) {
		
		// set the two fields that you want to create
		// a two way relationship for
		// these values can be the same field key
		// if you are using a single relationship field
		// on a single post type
		
		/* change the below relationship field key. need to toggle on field key in screen options */
		// the field key of one side of the relationship
		// $key_a = 'field_620e15ead95f5'; 
		// the field key of the other side of the relationship
		// as noted above, this can be the same as $key_a
		// $key_b = 'field_620e15b48b5c3';
		
		// figure out wich side we're doing and set up variables
		// if the keys are the same above then this won't matter
		// $key_a represents the field for the current posts
		// and $key_b represents the field on related posts
		if ($key_a != $field['key']) {
			// this is side b, swap the value
			$temp = $key_a;
			$key_a = $key_b;
			$key_b = $temp;
		}
		
		// get both fields
		// this gets them by using an acf function
		// that can gets field objects based on field keys
		// we may be getting the same field, but we don't care
		$field_a = acf_get_field($key_a, $post_id);
		$field_b = acf_get_field($key_b, $post_id);
		
		// set the field names to check
		// for each post
		$name_a = $field_a['name'];
		$name_b = $field_b['name'];
		
		// get the old value from the current post
		// compare it to the new value to see
		// if anything needs to be updated
		// use get_post_meta() to a avoid conflicts
		$old_values = get_post_meta($post_id, $name_a, true);
		// make sure that the value is an array
		if (!is_array($old_values)) {
			if (empty($old_values)) {
				$old_values = array();
			} else {
				$old_values = array($old_values);
			}
		}
		// set new values to $value
		// we don't want to mess with $value
		$new_values = $value;
		// make sure that the value is an array
		if (!is_array($new_values)) {
			if (empty($new_values)) {
				$new_values = array();
			} else {
				$new_values = array($new_values);
			}
		}
		
		// get differences
		// array_diff returns an array of values from the first
		// array that are not in the second array
		// this gives us lists that need to be added
		// or removed depending on which order we give
		// the arrays in
		
		// this line is commented out, this line should be used when setting
		// up this filter on a new site. getting values and updating values
		// on every relationship will cause a performance issue you should
		// only use the second line "$add = $new_values" when adding this
		// filter to an existing site and then you should switch to the
		// first line as soon as you get everything updated
		// in either case if you have too many existing relationships
		// checking end updated every one of them will more then likely
		// cause your updates to time out.
		//$add = array_diff($new_values, $old_values);
		$add = $new_values;
		$delete = array_diff($old_values, $new_values);
		
		// reorder the arrays to prevent possible invalid index errors
		$add = array_values($add);
		$delete = array_values($delete);
		
		if (!count($add) && !count($delete)) {
			// there are no changes
			// so there's nothing to do
			return $value;
		}
		
		// do deletes first
		// loop through all of the posts that need to have
		// the recipricol relationship removed
		for ($i=0; $i<count($delete); $i++) {
			$related_values = get_post_meta($delete[$i], $name_b, true);
			if (!is_array($related_values)) {
				if (empty($related_values)) {
					$related_values = array();
				} else {
					$related_values = array($related_values);
				}
			}
			// we use array_diff again
			// this will remove the value without needing to loop
			// through the array and find it
			$related_values = array_diff($related_values, array($post_id));
			// insert the new value
			update_post_meta($delete[$i], $name_b, $related_values);
			// insert the acf key reference, just in case
			update_post_meta($delete[$i], '_'.$name_b, $key_b);
		}
		
		// do additions, to add $post_id
		for ($i=0; $i<count($add); $i++) {
			$related_values = get_post_meta($add[$i], $name_b, true);
			if (!is_array($related_values)) {
				if (empty($related_values)) {
					$related_values = array();
				} else {
					$related_values = array($related_values);
				}
			}
			if (!in_array($post_id, $related_values)) {
				// add new relationship if it does not exist
				$related_values[] = $post_id;
			}
			// update value
			update_post_meta($add[$i], $name_b, $related_values);
			// insert the acf key reference, just in case
			update_post_meta($add[$i], '_'.$name_b, $key_b);
		}
		
		return $value;
		
	} // end function acf_reciprocal_relationship_card_deck_relationship

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