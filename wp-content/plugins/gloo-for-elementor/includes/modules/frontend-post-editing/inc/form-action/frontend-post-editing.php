<?php

namespace Gloo\Modules\Form_Post_Editing;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use Elementor\Modules\DynamicTags\Module as DynamicTags;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend_Post_Editing extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	private $prefix = 'gloo_frontend_post_editing';
	private $subPrefix = '';

	public function __construct($name = 'gloo_frontend_post_editing', $namePrefix = ''){
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
		return 'frontend_post_editing'.$this->get_sub_prefix();;
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
		return __( 'Frontend Post Editing', 'gloo_for_elementor' ).$this->get_sub_prefix(' ');
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
		$uploaded_ids = $record->get( 'uploaded_ids' );
		$updated_media = array();
		$stored_ids = array();
		
		
		// Get submitted form data
		$raw_fields = $record->get( 'fields' );
		$files = $record->get( 'files' );
		
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
			else{
				
				$fields[ $id ] = sanitize_text_field( $field['value'] );
			}
		}
 
		if ( ! isset( $settings[ $this->prefix . '_post_id' ] ) || empty( $settings[ $this->prefix . '_post_id' ] ) ) {
			$ajax_handler->add_error( $raw_fields['title'], 'Post not found.' );
		}

		$post_to_edit_id = absint( $settings[ $this->prefix . '_post_id' ] );

		$post_title_id   = $settings[ $this->prefix . '_post_title' ] ?: 'title';
		$post_content_id = $settings[ $this->prefix . '_post_content' ] ?: 'content';
		$post_image_id   = $settings[ $this->prefix . '_post_image' ] ?: '';
		$term_repeater   = $settings[ $this->prefix . '_post_terms_repeater' ] ?: '';
		$is_repeater_field   = $settings[ $this->prefix . '_is_repeater_field' ] ?: '';
		$title_multiple   = $settings[ $this->prefix . '_post_title_multiple' ];
		$post_title_ids   = $settings[ $this->prefix . '_post_title_ids' ];
		if($post_image_id && is_array($media_uploads) && count($media_uploads) >= 1 && isset($media_uploads['uploaded_to_media']) && is_array($media_uploads['uploaded_to_media']) && count($media_uploads['uploaded_to_media']) >= 1 && array_key_exists($post_image_id, $media_uploads['uploaded_to_media'])){
			$stored_ids[$post_image_id] = $media_uploads['uploaded_to_media'][$post_image_id];
		}
		if( 'yes' == $title_multiple ) {

			if(isset($post_title_ids)) {
				$title = explode(',', $post_title_ids);
				$field_titles = array();
			
				if(!empty($title) && is_array($title)) {
					foreach( $title as $title_field ) {
						$title_trim = trim($title_field);
						if(isset( $fields[ $title_trim ] ) && isset($raw_fields[$title_trim]) && $raw_fields[$title_trim]['type'] == 'gloo_terms_field' && is_numeric($fields[ $title_trim ])){
							$term = get_term_by( $field, $value, $taxonomy);
							foreach($field_with_settings as $single_field){
								if(isset($single_field['custom_id']) && $single_field['custom_id'] == $title_trim && !empty($single_field['gloo_term_fields_by_tax'])){
									$term = get_term_by( 'id', $fields[ $title_trim ], $single_field['gloo_term_fields_by_tax']);
									if($term){
										$field_titles[] =  $term->name;
									}else{
										$field_titles[] =  isset( $fields[ $title_trim ] ) ? $fields[ $title_trim ] : '';
									}
									
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
			$post_title   = (isset( $fields[ $post_title_id ] ) && !empty($fields[ $post_title_id ])) ? $fields[ $post_title_id ] : get_the_title($post_to_edit_id);
		}
 
		$post_content = isset( $fields[ $post_content_id ] ) ? $fields[ $post_content_id ] : '';

		$meta_fields = $settings[ $this->prefix . '_meta_fields' ];
		$post_type_fields = $settings[ $this->prefix . '_post_type_fields' ];
		$user_type_fields = $settings[ $this->prefix . '_user_type_fields' ];

		$meta_data = [];
		$files     = $record->get( 'files' );
		$post_type_jet_relation = array();
		$post_type_new_jet_relation = array();
 
		if ( ! empty( $meta_fields ) ) {
			foreach ( $meta_fields as $meta_field ) {
				

				if ( (! isset( $fields[ $meta_field['field_id'] ] ) || empty( $fields[ $meta_field['field_id'] ] || empty( $meta_field['meta_field'] ) ) ) && $meta_field['is_image'] != 'yes') {
					if(empty($meta_field['is_image']) || (isset($meta_field['is_image']) && $meta_field['is_image'] == 'no'))
						$meta_data[ $meta_field['meta_field'] ] = '';

				}
				else {
					$value = $fields[ $meta_field['field_id'] ];

					if (isset($meta_field['is_relation_field']) && $meta_field['is_relation_field'] && $value) {
					
						if($meta_field['is_relation_field'] == 'acf' && !is_array($value)){
							$arr_values = explode(',', $value);

							if(!empty($arr_values) && is_array($arr_values)) {
								$value = $arr_values;
							}
						} elseif($meta_field['is_relation_field'] == 'jet_engine'){
							$arr_values = explode(',', $value);
							
							if(!empty($arr_values) && is_array($arr_values)) {
								foreach( $arr_values as $jet_value ) {
									$post_type_jet_relation[] = array('key' => $meta_field['meta_field'], 'value' => $jet_value);
								}
						 	}
						} 
					}

					if($meta_field['is_relation_field'] != 'jet_engine' && $meta_field['is_image'] != 'yes') {
						$meta_data[ $meta_field['meta_field'] ] = $value;
					}
					
					
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
						$saved_uploads = get_post_meta( $post_to_edit_id, $meta_field['meta_field'], true );
						
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
									 delete_post_meta( $post_to_edit_id, $meta_field['meta_field'] );		
								}
							}
						}  else {
							$image_uploads = $media_uploads;	
						}

					
						if ( isset( $image_uploads['uploaded_to_media'] ) && !empty( $image_uploads['uploaded_to_media'][ $meta_field['field_id'] ] ) ) {

							$uploads_data = $image_uploads['uploaded_to_media'][$meta_field['field_id']];
							/* get return values in format according to database */
							$value = $this->get_image_storage_value( $store, $uploads_data, $meta_field );
							$stored_ids[$meta_field['field_id']] = $uploads_data;
							$meta_data[ $meta_field['meta_field'] ] = $value;
						}
					}
				}
			}
		}
		// print_r($meta_data);
		// die();

 		/* post types field */
		if ( ! empty( $post_type_fields ) ) {
			$pt= 0;
			foreach ( $post_type_fields as $post_type_field ) {
				
				if ( ! isset( $fields[ $post_type_field['field_id'] ] ) || empty( $fields[ $post_type_field['field_id'] ] || empty( $post_type_field['meta_field'] ) ) ) {
					if(empty($post_type_field['is_image']) || (isset($post_type_field['is_image']) && $post_type_field['is_image'] == 'no'))
						$meta_data[ $meta_field['meta_field'] ] = '';
				} else {
				
					$value = $fields[ $post_type_field['field_id'] ];
					$value = $this->get_post_ids_by_return_value($field_with_settings, $post_type_field, $value);

					if (isset($post_type_field['is_relation_field']) && $post_type_field['is_relation_field'] && $value) {
						
						if($post_type_field['is_relation_field'] == 'acf' && !is_array($value)){
							$arr_values = explode(',', $value);

							if(!empty($arr_values) && is_array($arr_values)) {
								$value = $arr_values;
							}
						} elseif($post_type_field['is_relation_field'] == 'jet_engine'){
							$arr_values = explode(',', $value);
							
							if(!empty($arr_values) && is_array($arr_values)) {
								foreach( $arr_values as $jet_value ) {
									$post_type_jet_relation[] = array('key' => $post_type_field['meta_field'], 'value' => $jet_value);
								}
						 	}

						} elseif($post_type_field['is_relation_field'] == 'new_jet_engine_relation'){

							$jet_relation = $post_type_field['jet_relation'];
							$jet_rel_context = $post_type_field['jet_rel_context'];
							
							$arr_values = explode(',', $value);
	
							$post_type_new_jet_relation[$pt]['jet_relation'] = $jet_relation;
							$post_type_new_jet_relation[$pt]['jet_rel_context'] = $jet_rel_context;
 					 
							if(!empty($value) && is_array($arr_values)) {
								$post_type_new_jet_relation[$pt]['jet_values'] = $arr_values;
							} else {
								$post_type_new_jet_relation[$pt]['jet_values'] = $value;
 							}

							$pt++;
						}
					}

					if(!in_array($post_type_field['is_relation_field'], array('jet_engine', 'new_jet_engine_relation'))) {
					// if($post_type_field['is_relation_field'] != 'jet_engine') {
						$meta_data[ $post_type_field['meta_field'] ] = $value;
					}
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
					if($user_type_field['meta_field_type'] == 'acf_rel' && !is_array($value)) {
				 
						$value = array_map('trim',array_filter(explode(',',$value)));
												
						if(!empty($arr_values) && is_array($arr_values)) {
							$value = $arr_values;
						}
					} elseif($user_type_field['meta_field_type'] == 'jet_engine_rel') {

						$jet_relation = $user_type_field['jet_relation'];
						$jet_rel_context = $user_type_field['jet_rel_context'];

						if(!empty($jet_relation) && !empty($jet_rel_context)) {

							$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );
							$relations_values = explode(',', $value);

							/* update Jet 2.11 Relation meta */
							if( isset($relation_instance) && !empty($relation_instance)) {
								if ( ! empty( $post_to_edit_id ) ) {
									if ( 'child_object' === $jet_rel_context ) {
										/**
										 * We updating children items from the parent object,
										 * this mean we need to delete all existing children for the parent and set up new
										 */
								
										// First of all completely delete all existing rows for the current parent
										$relation_instance->delete_rows( $post_to_edit_id );
							
										foreach ( $relations_values as $c_id ) {
											$relation_instance->update( $post_to_edit_id, $c_id );
										}
									} else {
										$child_id  = $post_to_edit_id;
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
					} 
				}
				
				if($user_type_field['meta_field_type'] != 'jet_engine_rel') {
					$meta_data[ $user_type_field['meta_field'] ] = $value;
				}
			}
		}

		if ( ! empty( $post_to_edit_id ) ) {

			$post_to_update = array(
				'ID'           => $post_to_edit_id,
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_status'  => $settings[ $this->prefix . '_post_status' ],
			);

			if(empty($post_content))
				unset($post_to_update['post_content']);

			if ( ! empty( $meta_data ) ) {
				$post_to_update['meta_input'] = $meta_data;
			}

			// update
			remove_action( 'post_updated', 'wp_save_post_revision' );
			$update = wp_update_post( $post_to_update );
			add_action( 'post_updated', 'wp_save_post_revision' );

			if ( ! $update ) {
				$ajax_handler->add_error( $raw_fields['content'] );
			}

			// featured image
			if ( $post_image_id && isset( $stored_ids ) && isset( $stored_ids[ $post_image_id ] ) ) {
				$attachment_id = $stored_ids[ $post_image_id ][0];
				set_post_thumbnail( $update, $attachment_id );
			}
 			
			// post types field update
			if(isset($post_type_jet_relation) && !empty($post_type_jet_relation) && $post_to_edit_id) {
 				foreach($post_type_jet_relation as $post_type_relation) {
					delete_post_meta( $post_to_edit_id, $post_type_relation['key']);
				}
				
				foreach($post_type_jet_relation as $post_type_relation) {
					add_post_meta( $post_to_edit_id, $post_type_relation['key'], $post_type_relation['value'] );
				}
			}

			/* new relation field logic */
			if( $post_type_field['is_relation_field'] == 'new_jet_engine_relation' && $post_to_edit_id ) {

				foreach( $post_type_new_jet_relation as $relation_object ) {
					
					$relation_instance = jet_engine()->relations->get_active_relations( $relation_object['jet_relation'] );

					$relations_values = $relation_object['jet_values'];
					$jet_rel_context = $relation_object['jet_rel_context'];
					$preserve_data =  $post_type_field['preserve_current_data'];

					if( $jet_rel_context ) {

						if ( 'child_object' === $jet_rel_context ) {
							$related_ids = $relation_instance->get_children( $post_to_edit_id, 'ids' );
						} else {
							$related_ids = $relation_instance->get_parents( $post_to_edit_id, 'ids' );
						}

						if(!empty($relations_values)) {
							$relations_values = array_map('trim',array_filter($relations_values));
						}

						$gloo_post_types = $_POST['form_fields']['gloo_post_types'][$post_type_field['field_id']];
						$post_values = (isset($gloo_post_types) && !empty($gloo_post_types)) ? explode(',', $gloo_post_types) : array();
  
						if($preserve_data == 'yes') {
 
							if(!empty($gloo_post_types)) {
  
								/* these are the post ids loading for a specific users or all post ids if no filter applied for user */
								
								if(!empty($relations_values)) {
									/* it will provide the values removed by user  */
									$result = array_values(array_diff($post_values, $relations_values));
								 
									if(!empty($result) && !empty($related_ids)) {
										foreach( $result as $post_id ) {
											if (($key = array_search($post_id, $related_ids)) !== false) {
												unset($related_ids[$key]);
											}
										}
									}

									/* after removing values merge the new posted values with related array */
									if(!empty($related_ids)) {
										$relations_values = array_unique(array_merge($related_ids, $relations_values));
									}
								} else {
 
									if(!empty($post_values)) {
 										foreach( $post_values as $remove_item) {
 											if (($key = array_search($remove_item, $related_ids)) !== false) {
 												unset($related_ids[$key]);
											}
										}	

										$relations_values = $related_ids;
 									}	
								}
							
							}
  						}
 					}
 			     
					/* insert Jet 2.11 Relation meta */
					if( isset($relation_instance) && !empty($relation_instance)) {
						if ( 'child_object' === $jet_rel_context ) {
							/**
							 * We updating children items from the parent object,
							 * this mean we need to delete all existing children for the parent and set up new
							 */
					
							// First of all completely delete all existing rows for the current parent
							$relation_instance->delete_rows( $post_to_edit_id );

							foreach ( $relations_values as $c_id ) {
								$relation_instance->update( $post_to_edit_id, $c_id );
							}
						} else {
							$child_id  = $post_to_edit_id;
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

			/* if post type field is empty then delete all values from the database */	
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
					wp_set_post_terms( $update, $term_ids, $taxonomy, false );
				}

			}

			//repeater fields
			if( $is_repeater_field == 'yes' )
				$this->update_repeater_field_data($record, $settings, $update);


		} else {
			$ajax_handler->add_error( $raw_fields['content'] );
		}

		$ajax_handler->add_response_data( 'frontend_post_id', $post_to_edit_id );

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
 
		if( $return_type == 'title' || $return_type == 'slug' ) {
			$arr_values = explode(',', $value);
			$arr_values = array_map('trim', $arr_values);

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

		$relation_items = array();
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
		$widget->start_controls_section(
			$this->prefix,
			[
				'label'     => __( 'Frontend Post Editing', 'gloo_for_elementor' ).$this->get_sub_prefix(' '),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			$this->prefix . '_post_id',
			[
				'label'   => __( 'Post ID', 'gloo_for_elementor' ),
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

		$widget->add_control(
			$this->prefix . '_post_status',
			[
				'label'       => __( 'Post Status', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => get_post_statuses(),
				'default'     => 'publish',
				'description' => __( 'Publish: publish', 'gloo_for_elementor' ),
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
					'jet_engine'           => 'Jet engine',
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
					'new_jet_engine_relation' => 'Jet engine 2.11+',
					'acf'        => 'Advanced Custom Fields (ACF)',
				]
			]
		);

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

		$post_types_repeater->add_control(
			'preserve_current_data',
			[
				'label' => esc_html__( 'Preserve current data ?', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

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