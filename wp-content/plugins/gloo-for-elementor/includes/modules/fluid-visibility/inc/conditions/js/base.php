<?php

namespace Gloo\Modules\Fluid_Visibility\Conditions\JS;

use http\Params;

abstract class Base {

	public $prefix = 'gloo_fluid_visibility_';
	/**
	 * Returns condition ID
	 *
	 * @return string ID
	 */
	abstract public function get_id();

	/**
	 * Returns condition name
	 *
	 * @return string name
	 */
	abstract public function get_name();

	/**
	 * Evaluate condition
	 *
	 * @return bool evaluation
	 */
	abstract public function evaluate( $args = array() );

	/**
	 * Returns condition group
	 *
	 * @return string group
	 */
	public function get_group() {
		return false;
	}

	/**
	 * @return boolean Enable field for condition
	 */
	public function enable_field() {
		return true;
	}

	/**
	 * @return boolean Enable value for condition
	 */
	public function enable_value() {
		return true;
	}

	/**
	 * Returns the opposite of a js action
	 */
	public function opposite_action( $action ) {
		$opposites = [
			'hide' => 'show',
			'show' => 'hide',
		];

		if ( isset( $opposites[ $action ] ) ) {
			return $opposites[ $action ];
		}

		return false;
	}

	/**
	 * Returns the generated js
	 */
	public function generate_js( $args = [] ) {

		$output = '';
		$field_id   = $args['form_field_id'];
		

		$action   = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$action_without_edit = $action;
		$opposite = $this->opposite_action( $action );
		$opposite_without_edit = $opposite;
		
		
		$logic = $this->get_logic_for_next_conditions($args);
		
		// db($args['condition_meta_data']);
		$value = $args['value'];

		$parent_container = '';
			if(isset($args['all_conditions']) && isset($args['all_conditions']['current_listing_id']))
				$parent_container = ".jet-listing-dynamic-post-".$args['all_conditions']['current_listing_id']." ";

		$field_selector = "$parent_container#form-field-$field_id, $parent_container.elementor-field-type-checkbox.elementor-field-group-$field_id [value=\"$value\"], $parent_container.elementor-field-type-radio.elementor-field-group-$field_id input, $parent_container.elementor-field-type-gloo_terms_field.elementor-field-group-$field_id input, $parent_container.elementor-field-type-gloo_terms_field.elementor-field-group-$field_id [value=\"$value\"], $parent_container.elementor-field-type-gloo_cpt_field.elementor-field-group-$field_id input, $parent_container.elementor-field-type-gloo_cpt_field.elementor-field-group-$field_id [value=\"$value\"]";
		if(isset($args['is_element']) && isset($args['is_element']['id']) && $args['is_element']['id']){

			if ( $action === 'disable' ) {
				$action   = "prop('disabled', true)";
				$opposite = "prop('disabled', false)";
			} else {
				$action   = "$action()";
				$opposite = "$opposite()";
			}
			
			$output = "
			jQuery(document).ready(function($){";
				
				$output .= "var value = jQuery('$field_selector').val();";
				if(!(isset($args['current_condition']) && isset($args['current_condition'][$this->prefix . 'condition_case_sensitive_status']) && $args['current_condition'][$this->prefix . 'condition_case_sensitive_status'] == 'yes')){
					$output .= "if(typeof value != 'undefined'){
						value = value.toLowerCase().trim();
					} 
					";
				}
				
				$output .= "var isCheckbox;
				
				if($logic && isCheckbox){
					jQuery('$parent_container.elementor-element-".$args['is_element']['id']."').$action;
				}else {
					jQuery('$parent_container.elementor-element-".$args['is_element']['id']."').$opposite;
				}

				jQuery('body').on('change keydown paste input', '$field_selector', function (e) {
					var value = jQuery(this).val();";
					if(!(isset($args['current_condition']) && isset($args['current_condition'][$this->prefix . 'condition_case_sensitive_status']) && $args['current_condition'][$this->prefix . 'condition_case_sensitive_status'] == 'yes')){
						$output .= "if(typeof value != 'undefined'){
							value = value.toLowerCase().trim();
						} 
						";
					}
					$output .= "isCheckbox = jQuery(this).is(':checkbox') ? jQuery(this).is(':checked') : true;
					// console.log(value);
					if($logic && isCheckbox){
						jQuery('$parent_container.elementor-element-".$args['is_element']['id']."').$action;
					}else {
						jQuery('$parent_container.elementor-element-".$args['is_element']['id']."').$opposite;
					}
				});

			});
			";


			if(isset($args['condition_meta_data']) && isset($args['condition_meta_data']['php_or_is_true'])){
				$output .= "jQuery(document).ready(function($){
					jQuery('$parent_container.elementor-element-".$args['is_element']['id']."').$action;
				});";
			}

			

		}else{
			$custom_id  = $args['custom_id'];
			if($args['field_type'] == 'radio' 
			|| $args['field_type'] == 'checkbox' 
			|| ($args['field_type'] == 'gloo_terms_field' && isset($args['fluid_visibility_current_field']) && isset($args['fluid_visibility_current_field']['gloo_term_fields_output']) && ($args['fluid_visibility_current_field']['gloo_term_fields_output'] == 'radio' || $args['fluid_visibility_current_field']['gloo_term_fields_output'] == 'checkbox')) 
			|| ($args['field_type'] == 'gloo_cpt_field' && isset($args['fluid_visibility_current_field']) && isset($args['fluid_visibility_current_field']['gloo_cpt_fields_output']) && ($args['fluid_visibility_current_field']['gloo_cpt_fields_output'] == 'radio' || $args['fluid_visibility_current_field']['gloo_cpt_fields_output'] == 'checkbox')) 
			){
				$custom_id .= '-0';
			}

			if ( $action === 'disable' ) {
				$action   = "prop('disabled', true)";
				$opposite = "prop('disabled', false)";
			} else {
				$action   = "closest('.elementor-field-group').$action(gloo_visibility_action_callback)";
				$opposite = "closest('.elementor-field-group').$opposite(gloo_visibility_action_callback)";
			}

			$action_field_selector = "#form-field-$custom_id";
			$action_field_selector = $parent_container.$action_field_selector;

			if($args['field_type'] == 'html' || $args['field_type'] == 'gloo_repeater_end_field'){
				$action_field_selector = $parent_container.".elementor-field-group-".$custom_id;

				$action   = "$action_without_edit(gloo_visibility_action_callback)";
				$opposite = "$opposite_without_edit(gloo_visibility_action_callback)";
			}
				

			$output = "
			jQuery(document).ready(function($){
				var value = jQuery('$field_selector').val();";
				if(!(isset($args['current_condition']) && isset($args['current_condition'][$this->prefix . 'condition_case_sensitive_status']) && $args['current_condition'][$this->prefix . 'condition_case_sensitive_status'] == 'yes')){
					$output .= "if(typeof value != 'undefined'){
						value = value.toLowerCase().trim();
					} 
					";
				}
				$output .= "var isCheckbox;
				
				if(jQuery('$action_field_selector').length >= 1 && jQuery('$action_field_selector').prop('required')){
					jQuery('$action_field_selector').closest('.elementor-field-group').addClass('had_gloo_visibility_required_group');
					jQuery('$action_field_selector').addClass('had_gloo_visibility_required');
				}
				if(typeof value != 'undefined' && $logic && isCheckbox){
					jQuery('$action_field_selector').$action;
				}else {
					jQuery('$action_field_selector').$opposite;
				}

				
				if(value){
					//gloo_trigger_change_items.push('$field_id');
				}

				jQuery('body').on('change keydown paste input', '$field_selector', function (e) {
					var value = jQuery(this).val();";
					if(!(isset($args['current_condition']) && isset($args['current_condition'][$this->prefix . 'condition_case_sensitive_status']) && $args['current_condition'][$this->prefix . 'condition_case_sensitive_status'] == 'yes')){
						$output .= "if(typeof value != 'undefined'){
							value = value.toLowerCase().trim();
						} 
						";
					}
					$output .= "var isCheckbox = jQuery(this).is(':checkbox') ? jQuery(this).is(':checked') : true;
					// console.log(value);
					if(typeof value != 'undefined' && $logic && isCheckbox){
						jQuery('$action_field_selector').$action;
					}else {
						jQuery('$action_field_selector').$opposite;
					}
				});
			});
			";

			if(isset($args['condition_meta_data']) && isset($args['condition_meta_data']['php_or_is_true'])){
				$output .= "jQuery(document).ready(function($){
					jQuery('$action_field_selector').$action;
				});";
			}

			

		}
		return $output;
	}

	public function get_logic_for_next_conditions($args){
		$output = "(";
		if(isset($args['condition_meta_data']) && isset($args['condition_meta_data']['before_js_operators']) && $args['condition_meta_data']['before_js_operators']){
			$output .= " ".$args['condition_meta_data']['before_js_operators']." ";
		}
		$output .= $args['logic'];
		// db($output);
		$condition = $args['current_condition'];
		$output .= $this->add_next_condition($args, $condition);
		$output .= ")";
		// db($output);
		return $output;
	}

	public function add_next_condition($args, $condition){
		$output = '';
		$conditions_operators = array('and' => '&&', 'or' => '||');
		if ( isset( $condition[ $this->prefix . 'condition_next_status' ] ) && $condition[ $this->prefix . 'condition_next_status' ] && isset( $condition[ $this->prefix . 'condition_next' ] ) && $condition[ $this->prefix . 'condition_next' ]) {
			
			$condition_operator = (isset( $condition[ $this->prefix . 'condition_next_logic' ] ) && $condition[ $this->prefix . 'condition_next_logic' ] ? $condition[ $this->prefix . 'condition_next_logic' ] : 'and');
				
			$condition_index    = array_search( $condition[ $this->prefix . 'condition_next' ], array_column( $args['all_conditions'], '_id' ) );
			$next_condition          = $args['all_conditions'][ $condition_index ];
			$selected_condition = $next_condition[ $this->prefix . 'condition' ];
			if ( $next_condition[ $this->prefix . 'is_form_field' ] === 'yes' ) {
				$selected_condition = $next_condition[ $this->prefix . 'condition_js' ];
			}
			$condition_instance = $args['condition_manager']->get_condition( $selected_condition );
			if ( ! $condition_instance ) {
				return $output;
			}
			
				
			if ( $next_condition[ $this->prefix . 'is_form_field' ] === 'yes' ) {
				// if(!(isset($next_condition[ $this->prefix . 'condition_case_sensitive_status' ]) && $next_condition[ $this->prefix . 'condition_case_sensitive_status' ] == 'yes')){
				// 	$next_condition[ $this->prefix . 'condition_value' ] = trim(strtolower($next_condition[ $this->prefix . 'condition_value' ]));
				// }

				$output .= " ".$conditions_operators[$condition_operator]." ";
				$output .= 'value '.$condition_instance->condition_operator." '".$next_condition[ $this->prefix . 'condition_value' ]."' ";
			}else{
				// $new_args = $args;
				// unset($new_args['value']);
				// $php_evaluate = $condition_instance->evaluate( $args );
				// db($php_evaluate);
				$output .= " ".$conditions_operators[$condition_operator]." ";
				$output .= ($args['condition_meta_data']['php_results'][$next_condition['_id']] ? 'true' : 'false').' ';
			}

			if ( isset( $next_condition[ $this->prefix . 'condition_next_status' ] ) && $next_condition[ $this->prefix . 'condition_next_status' ] && isset( $next_condition[ $this->prefix . 'condition_next' ] ) && $next_condition[ $this->prefix . 'condition_next' ]) {
					
					
				$output .= $this->add_next_condition($args, $next_condition);
			}
		}

		

		return $output;
	}

}
