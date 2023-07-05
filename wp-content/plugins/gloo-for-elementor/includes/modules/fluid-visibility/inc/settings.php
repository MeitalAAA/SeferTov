<?php

namespace Gloo\Modules\Fluid_Visibility;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use ElementorPro\Plugin;

class Settings {

	private $prefix = 'gloo_fluid_visibility_';
	
	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

	public $current_condition_chain_data = array();

	private $settings = '';

	private $debug_string = '';

	public $condition_list = [];

	private $js_functions = [];
	use Traits\FormSubmitButton;

	public function __construct() {

		$this->initialize_form_submit_button();
		//add admin js file
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );

		// register elementor controls
		add_action( 'elementor/documents/register_controls', [
			$this,
			'add_fluid_visibility_section'
		] );

		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'update_fields_controls'
		] );


		require gloo()->modules_path( 'fluid-visibility/inc/conditions/manager.php' );

		$this->conditions = new Conditions\Manager();

		$element_types = array(
			'section',
			'column',
			'widget',
			'container',
		);

		foreach ( $element_types as $element ) {
			add_filter( "elementor/frontend/{$element}/should_render", array( $this, 'check_condition' ), 10, 2 );
			add_filter( "elementor/frontend/{$element}/should_render", array( $this, 'evaluate_condition' ), 99, 2 );
		}


		// widgets
		add_action( 'elementor/element/column/section_advanced/after_section_end', [
			$this,
			'register_visibility'
		], 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [
			$this,
			'register_visibility'
		], 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [
			$this,
			'register_visibility'
		], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [
			$this,
			'register_visibility'
		], 10, 2 );
		
		// form fields
		add_action( 'elementor-pro/forms/pre_render', [ $this, 'check_form_field_conditions' ], 10, 2 );
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'render_field' ], 10, 3 );

		// print out js
		add_action( 'wp_footer', [ $this, 'print_js' ], 20, 2 );

	}

	public function register_visibility( $element, $section_id ) {

		$element->start_controls_section(
			$this->prefix . 'elements',
			array(
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => __( 'Fluid Logic', 'gloo-for-elementor' ),
			)
		);

		$element->add_control(
			$this->prefix . 'elements_status',
			array(
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Enable', 'gloo-for-elementor' ),
				'render_type'    => 'template',
				'prefix_class'   => 'jedv-enabled--',
				'style_transfer' => false,
			)
		);

		$element->add_control(
			$this->prefix . 'elements_condition',
			array(
				'label'     => __( 'Condition Chain', 'gloo-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [],
				'condition' => [ $this->prefix . 'elements_status' => 'yes' ],
			)
		);

		$element->add_control(
			$this->prefix . 'elements_action',
			array(
				'label'     => __( 'Action', 'gloo-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'show' => 'Show',
					'hide' => 'Hide'
				],
				'condition' => [ $this->prefix . 'elements_status' => 'yes' ],
			)
		);

		$element->end_controls_section();

	}

	public function print_js() {

		echo "<script>jQuery(document).ready(function($){
			$('.gloo_ffc_remove_field').hide();
		});</script>";

		$js_functions = $this->js_functions;

		if ( ! empty( $js_functions ) ) {
			$output = '';

			foreach ( $js_functions as $js_function ) {
				$output .= $js_function . "
				";
			}

			if ( $output ) {
				
				echo "<script id='gloo-fluid-visibility-all'>jQuery(document).ready(function($){
					$('.gloo_ffc_remove_field').hide();
					// $('.gloo_fluid_visibility_required').each(function(i, v){
						// $(this).closest('.elementor-field-group').addClass('gloo_fluid_visibility_required_group');
					// });
				});</script>";
				
				echo "<script id='gloo-fluid-visibility'>
				var gloo_trigger_change_items = [];
				function gloo_visibility_action_callback() {
					//element_input = jQuery(this).find('input, email, password, url, number');
					//if(!jQuery(this).is(':hidden'))
						//jQuery(this).css({'width': 'auto', 'height': 'auto'});
					
					 if(jQuery(this).hasClass('had_gloo_visibility_required_group') /*element_input.lenght >= 1 && element_input.hasClass('had_gloo_visibility_required')*/){
						if(jQuery(this).is(':hidden')){
							jQuery(this).find('input, email, password, url, number, select').prop('required', false);
						}else{
							jQuery(this).find('input, email, password, url, number, select').prop('required', true);
						}
					 }
					 if(jQuery(this).hasClass('gloo_fluid_visibility_required_group')){
						if(jQuery(this).is(':hidden')){
							if(jQuery(this).find('input, email, password, url, number').length >= 1){
								jQuery(this).find('input, email, password, url, number').val(function() {
									return this.defaultValue;
								});
							}else if(jQuery(this).find('select').length >= 1){
								jQuery(this).find('select option').each(function (index, element) {
									jQuery(element).prop('selected', this.defaultSelected)
								});
							}
							jQuery(this).find('input, email, password, url, number, select').prop('required', false);
						}else{
							jQuery(this).find('input, email, password, url, number, select').prop('required', true);
						}
					 }
				}
				$output

				jQuery(document).ready(function($){
					if(gloo_trigger_change_items.length >= 1){
						gloo_trigger_change_items = [...new Set(gloo_trigger_change_items)];
						for(i = 0; i < gloo_trigger_change_items.length; i++){
							if(jQuery('#form-field-'+gloo_trigger_change_items[i]).length >= 1){
								jQuery('#form-field-'+gloo_trigger_change_items[i]).trigger('change');
							}
						}
					}
				});
				
				
				
				</script>";
			}
		}
	}

	public function add_error( $message ) {
		if ( ! current_user_can( 'editor' ) && ! current_user_can( 'administrator' ) ) {
			return; // no edit permissions
		}
		$string = $message['string'];
		$type   = $message['value'] ? 'success' : 'danger';
		echo "<div class='elementor-message elementor-message-$type gloo-ffc-debug-message' role='alert'>$string</div>";
	}

	public function render_field( $item, $item_index, $form ) {


		if ( $form->get_render_attributes( 'input' . $item_index, $this->prefix . 'ffc_remove_field' ) ) {
			// remove field
			return false;
		}

		if ( $message = $form->get_render_attributes( 'input' . $item_index, $this->prefix . 'ffc_debug_message' ) ) {
			// debug mode
			$this->add_error( $message );
		}

		return $item;
	}


	private function get_fields_conditions( $instance ) {
		$conditions = [];
		foreach ( $instance['form_fields'] as $field ) {
			if ( self::are_conditions_enabled( $field ) ) {
				$conditions[] = [
					'id'          => $field['custom_id'],
					'condition'   => self::and_join_lines( $field['dce_conditions_expression'] ),
					'mode'        => $field['dce_field_conditions_mode'],
					'disableOnly' => $field['dce_conditions_disable_only'] === 'yes'
				];
			}
		}

		return $conditions;
	}


	public function get_elementor_condition_settings( $id ) {

		// $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $id );
		
		// db($document->get_elements_data());
		// db(get_class_methods($document));
		// db($document->get_settings());
		// db($document->get_settings_for_display('gloo_fluid_visibility_conditions')[0]['gloo_fluid_visibility_condition_field']);
		
		
		// db($object);

		
		// db( $post->ID);
		
		// db( $post->ID);
		
		
		

		// $document = \Elementor\Plugin::instance()->documents->get( $id );
		// $document->refresh_post();
		// \Elementor\Plugin::$instance->db->switch_to_post( $post_object->ID );
		
		
		// db(get_class_methods($document));
		
		// db($document->get_settings_for_display( $this->prefix . 'conditions' )[0]['gloo_fluid_visibility_condition_field']);

		$document = \Elementor\Plugin::instance()->documents->get( $id );
		if ( ! empty( $document ) ) {
			if($document->get_name() == 'jet-listing-items'){
				$conditions = $document->get_settings()[$this->prefix . 'conditions'];
				if($conditions && is_array($conditions) && count($conditions) >= 1 ){
					foreach($conditions as $key=>$single_condition){
						if(isset($single_condition['__dynamic__']) && is_array($single_condition['__dynamic__']) && count($single_condition['__dynamic__']) >= 1){
							// global $post;
							$post_object = jet_engine()->listings->data->get_current_object();
							$main_listing_settings = $document->get_settings();

							// $post = get_post($post_object->ID, OBJECT );
							// setup_postdata( $post );

							$sub_listing = \Elementor\Plugin::instance()->documents->get( $post_object->ID );
							$sub_listing_settings = $sub_listing->get_settings();
							$sub_listing->set_settings( $main_listing_settings );

							$sub_listing_settings_frontend = $sub_listing->get_settings_for_display( $this->prefix . 'conditions' );
							$sub_listing_settings_frontend['current_listing_id'] = $post_object->ID;
							$sub_listing->set_settings( $sub_listing_settings );
							return $sub_listing_settings_frontend;
							// db($sub_listing->get_settings_for_display('gloo_fluid_visibility_conditions')[0]['gloo_fluid_visibility_condition_field']);
							// wp_reset_query();
							break;
						}
					}
				}
			}
			return $document->get_settings_for_display( $this->prefix . 'conditions' );
		}

		return false;
	}


	public function check_condition_by_id( $id, $conditions, $field = [], $last_value = null ) {

		if(!$conditions)
			return;
			
		$condition_meta_data = $this->current_condition_chain_data;
		
		
		
		
		
		$condition_index    = array_search( $id, array_column( $conditions, '_id' ) );
		$condition          = $conditions[ $condition_index ];
		$selected_condition = $condition[ $this->prefix . 'condition' ];
		if ( $condition[ $this->prefix . 'is_form_field' ] === 'yes') {
			$selected_condition = $condition[ $this->prefix . 'condition_js' ];
		}
		
		// elseif(isset($condition_meta_data['js_conditions']) && $condition_meta_data['js_conditions'] >= 1){
		// 	$selected_condition = $this->convert_php_to_js_condition($selected_condition);
		// }
		
		// $selected_condition = 'equal';
		// db($selected_condition);
		$condition_instance = $this->conditions->get_condition( $selected_condition );
		// db($condition_meta_data);
		// db($condition_instance);
		if ( ! $condition_instance ) {
			return false;
		}

		
		$type = ! empty( $field[ $this->prefix . 'ffc_action' ] ) ? $field[ $this->prefix . 'ffc_action' ] : 'show';
		
		$args = array(
			'type'          => $type,
			'user_role'     => null,
			'user_id'       => null,
			'field'         => null,
			'value'         => null,
			'data_type'     => null,
			'form_field_id' => null,
			'inverse'       => null,
			'terms_taxonomy' => null,
			'loop_item' 	=> null,
			'all_conditions' => $conditions,
			'current_condition' => $condition,
			'condition_manager' => $this->conditions,
		);


		foreach ( $args as $arg => $default ) {
			$key          = $this->prefix . 'condition_' . $arg;
			$args[ $arg ] = ! empty( $condition[ $key ] ) ? $condition[ $key ] : $default;
		}
		

		$is_dynamic_field = isset( $condition['__dynamic__'][ $this->prefix . 'condition_field' ] );
		$is_empty_field   = empty( $condition[ $this->prefix . 'condition_field' ] );

		$args['field_raw'] = ( ! $is_dynamic_field && ! $is_empty_field ) ? $condition[ $this->prefix . 'condition_field' ] : null;
		$args['condition_meta_data'] = $condition_meta_data;

		$args['custom_id']  = isset( $field['custom_id'] ) ? $field['custom_id'] : null;
		$args['field_type'] = isset( $field['field_type'] ) ? $field['field_type'] : null;

		$args['fluid_visibility_current_field'] = $field;

		if ( isset( $conditions['is_element'] ) ) {
			$args['is_element'] = $conditions['is_element'];
			$args['type']       = $conditions['is_element']['action'];
			$condition['_id']   .= '_element';
		}

		$list_args = [];


		if(isset($condition_meta_data['count']) && isset($condition_meta_data['php_conditions']) && $condition_meta_data['php_conditions'] === $condition_meta_data['count'] && isset($condition_meta_data['js_generated'])){
			unset($this->current_condition_chain_data['js_generated']);
			unset($condition_meta_data['js_generated']);
			// if(!$this->is_all_php_conditions()){
			// 	$this->add_js_condition_to_list($this->generate_php_conditions_js($args) );
			// }
			
		}


		if ( $condition[ $this->prefix . 'is_form_field' ] === 'yes' ) {
			
			// db($this->prefix);
			// db($id);
			// db($field);
			// db($condition);
			// db($field['custom_id']);
			// db($this->js_functions);
			// db($condition_meta_data);
			if(!(isset($condition) && isset($condition[$this->prefix . 'condition_case_sensitive_status']) && $condition[$this->prefix . 'condition_case_sensitive_status'] == 'yes'))
				$args['value'] = trim(strtolower($args['value']));
			
			if(isset($condition_meta_data['js_conditions']) && $condition_meta_data['js_conditions'] >= 1 && isset( $field[$this->prefix.'ffc_action'] ) && $field[$this->prefix.'ffc_action'] && isset($field[$this->prefix.'ffc_chain']) && ($field[$this->prefix.'ffc_chain'] == $id || $id == $this->current_condition_chain_data['has_js'])){
				$this->add_js_condition_to_list( $condition_instance->get_js( $args ) );
			}else if(isset($condition_meta_data['js_conditions']) && $condition_meta_data['js_conditions'] >= 1 && isset( $conditions['is_element']) && isset($conditions['is_element']['js_generated']) && $conditions['is_element']['js_generated'] == 'no'|| ($id == $this->current_condition_chain_data['has_js'])){
				$this->add_js_condition_to_list( $condition_instance->get_js( $args ) );
				$conditions['is_element']['js_generated'] = 'yes';
			}
			
			// db($this->js_functions);
			
			$list_args['type'] = 'js';
		}else{
			
			if((isset($condition_meta_data['js_conditions']) && $condition_meta_data['js_conditions'] >= 1) && !isset($this->current_condition_chain_data['redirected'])){
				
				if ( isset( $condition[ $this->prefix . 'condition_next_status' ] ) && $condition[ $this->prefix . 'condition_next_status' ] && isset( $condition[ $this->prefix . 'condition_next' ] ) && $condition[ $this->prefix . 'condition_next' ] ) {
					$this->current_condition_chain_data['redirected'] = $condition[ $this->prefix . 'condition_next' ];
					$this->check_condition_by_id($this->current_condition_chain_data['has_js'], $conditions, $field, $last_value);
					return;
				}
			}

		}

		


		
		

		


		$this->add_debug_string( $condition[ $this->prefix . 'condition_name' ] . $this->get_condition_value_html( $condition_instance->evaluate( $args ), $list_args ) );

		if ( isset( $condition[ $this->prefix . 'condition_next_status' ] ) && $condition[ $this->prefix . 'condition_next_status' ] && isset( $condition[ $this->prefix . 'condition_next' ] ) && $condition[ $this->prefix . 'condition_next' ] ) {

			$list_args['logic'] = isset( $condition[ $this->prefix . 'condition_next_logic' ] ) && $condition[ $this->prefix . 'condition_next_logic' ] ? $condition[ $this->prefix . 'condition_next_logic' ] : 'and';
			$list_args['next']  = $condition[ $this->prefix . 'condition_next' ];

			$this->add_debug_string( '<br><b>' . $list_args['logic'] . '</b><br>' );

			$this->add_condition_to_list( $condition_instance->evaluate( $args ), $list_args );
			if ( $field[ $this->prefix . 'ffc_debug' ] ) 
			return $this->check_condition_by_id( $condition[ $this->prefix . 'condition_next' ], $conditions, $field, $condition_instance->evaluate( $args ) );

		}

		$this->add_condition_to_list( $condition_instance->evaluate( $args ), $list_args );

	}

	public function get_condition_value_html( $value, $list_args = [] ) {
		$message = $value ? ' <div class="gloo-ffc-message-success">(Evaluation:<b> True</b>)</div>' : ' <div class="gloo-ffc-message-fail">(Evaluation:<b> False</b>)</div>';
		if ( isset( $list_args['type'] ) && $list_args['type'] === 'js' ) {
			$message = '<div class="gloo-ffc-message-success">(Evaluation:<b> Input Dependent</b>)</div>';
		}

		return $message;
	}

	public function add_js_condition_to_list( $value ) {

		$this->js_functions[] = $value;

	}

	public function add_condition_to_list( $value, $args = [] ) {


		$this->condition_list[] = [
			'value' => $value,
			'next'  => isset( $args['next'] ) ? $args['next'] : '',
			'logic' => isset( $args['logic'] ) ? $args['logic'] : '',
			'type'  => isset( $args['type'] ) ? $args['type'] : '',
		];
	}

	public function check_form_field_conditions( $instance, $form ) {
		
		$instance_form_fields_list = $instance['form_fields'];

		$submit_button_fluid_logic = $instance[$this->prefix . 'button_status'];
		$submit_button_fluid_chain_id = $instance[$this->prefix . 'button_condition'];
		$submit_button_fluid_action = $instance[$this->prefix . 'button_action'];
		if($submit_button_fluid_logic == 'yes' && $submit_button_fluid_action){
			// db($submit_button_fluid_action);exit();
			$form->add_render_attribute( 'button', 'id', 'form-field-gloo_submit_button_'.$form->get_id() );
			$submit_button_field = array(
				'custom_id' => 'gloo_submit_button_'.$form->get_id(),
				'is_submit' => 'yes',
				$this->prefix . 'ffc_chain' => $submit_button_fluid_chain_id,
				$this->prefix . 'ffc_action' => $submit_button_fluid_action,
			);
			$instance_form_fields_list[] = $submit_button_field;
		}
		
		foreach ( $instance_form_fields_list as $item_index => $field ) {
			// if($field['custom_id'] == 'email'){
				
			// }
			if ( ! $field[ $this->prefix . 'ffc_action' ] ) {
				continue;
			}
			// db($instance['form_fields']);
			$chain = $field[ $this->prefix . 'ffc_chain' ];
			
			// db($chain);db($field);exit();
			// $conditions = $this->get_elementor_condition_settings( get_the_ID() );
			$conditions = $this->get_elementor_condition_settings(\Elementor\Plugin::instance()->documents->get_current()->get_main_id() );
			$this->current_condition_chain_data = array();
			
			$this->get_condition_chain_data_by_id($chain, $conditions);
			// db($this->current_condition_chain_data); 
			// exit(); 
			$this->check_condition_by_id( $chain, $conditions, $field );


			$dom_removable = $js_controllable_only = $contains_js_condition = false;

			$check = boolval( $this->condition_list[0]['value'] );

			foreach ( $this->condition_list as $key => $item ) {
				if ( $item['type'] === 'js' ) {
					$contains_js_condition = true;
				}

				if ( $item['logic'] ) {
					switch ( $item['logic'] ) {
						case 'and' :
							$check = ( $check and $this->condition_list[ $key + 1 ]['value'] );
							if ( ! $check && $this->condition_list[ $key ]['type'] !== 'js' && $this->condition_list[ $key + 1 ]['type'] !== 'js' ) {
								// check is false, one or more of the conditions is php
								$dom_removable = true;
							}
							break;
						case 'or' :
							$check = ( $check or $this->condition_list[ $key + 1 ]['value'] );
							if ( $this->condition_list[ $key ]['type'] === 'js' ) {
								// current condition is a js condition
								if ( ! $this->condition_list[ $key + 1 ]['type'] != 'js' && ! $this->condition_list[ $key + 1 ]['value'] ) {
									// hidden initially, shown with js
									$js_controllable_only = true;
								}

							}
							break;

					}
				}
			}

			// db($this->current_condition_chain_data['php_and_is_false']);exit();
			// TODO add compatibility for selects too

			switch ( $field[ $this->prefix . 'ffc_action' ] ) {
				case 'hide' :
					if ( $check && ! $js_controllable_only ) {
						$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-hidden-form-field' );
					}
					// db($this->current_condition_chain_data['php_and_is_false']);
					if($this->is_all_php_conditions() && $this->current_condition_chain_data['php_evaluate']){
					// if ( $dom_removable ) {
						// delete from dom
						$form->add_render_attribute( 'input' . $item_index, $this->prefix . 'ffc_remove_field', true );
						if($field[ 'is_submit' ] == 'yes')
							$form->add_render_attribute( 'submit-group', 'class', 'gloo_ffc_remove_field');
					}
					// else if(!isset($this->current_condition_chain_data['php_and_is_false'])){
					// 	$form->add_render_attribute( 'input' . $item_index, $this->prefix . 'ffc_remove_field', true );
					// }

					break;
				case 'disable' :
					if ( $check && ! $js_controllable_only ) {
						$form->add_render_attribute( 'input' . $item_index, 'disabled', 'disabled' );
						$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-disabled-form-field' );
					}
					break;
				case 'show' :

					if ( $this->is_all_php_conditions() && !$this->current_condition_chain_data['php_evaluate']) {
						$form->add_render_attribute( 'input' . $item_index, $this->prefix . 'ffc_remove_field', true );
						if($field[ 'is_submit' ] == 'yes')
							$form->add_render_attribute( 'submit-group', 'class', 'gloo_ffc_remove_field');
					}
					else if(isset($this->current_condition_chain_data['php_and_is_false'])){
						$form->add_render_attribute( 'input' . $item_index, $this->prefix . 'ffc_remove_field', true );
						if($field[ 'is_submit' ] == 'yes')
							$form->add_render_attribute( 'submit-group', 'class', 'gloo_ffc_remove_field');
					}
					if ( ( ! $check ) || ( $check && $js_controllable_only ) || $contains_js_condition ) { // hide if condition not met

						$form->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-hidden-form-field' );
						$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-hidden-form-field' );
					}

					break;

			}

			
			if ( isset($field[ $this->prefix . 'ffc_required' ]) &&  $field[ $this->prefix . 'ffc_required' ]) {
				
				$form->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo_fluid_visibility_required_group' );
			}
			

			if ( $field[ $this->prefix . 'ffc_debug' ] ) {
				$string = $field[ $this->prefix . 'ffc_action' ] . " if<br><br>" . $this->get_debug_string();

				$message_args = [
					'string' => $string,
					'value'  => $check
				];
				$form->add_render_attribute( 'input' . $item_index, $this->prefix . 'ffc_debug_message', $message_args );
			}
			$form->add_render_attribute( 'field-group' . $item_index, 'data-field_type', $field['field_type'] );
			$form->add_style_depends( 'gloo-for-elementor' );

		}

	}


	public function add_debug_string( $value ) {
		$this->debug_string .= $value;
	}

	public function get_debug_string() {
		$string             = $this->debug_string;
		$this->debug_string = '';

		return $string;
	}

	public function update_fields_controls( $widget ) {
		$elementor    = Plugin::elementor();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
			return;
		}


		$field_controls         = [
			$this->prefix . 'ffc_tab'    => [
				'type'         => 'tab',
				'tab'          => 'content',
				'label'        => __( 'Conditions', 'gloo-for-elementor' ),
				'conditions'   => [
					'terms' => [
						[
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => [ 'hidden', 'step' ]
						]
					]
				],
				'tabs_wrapper' => 'form_fields_tabs',
				'name'         => $this->prefix . 'ffc_tab'
			],
			$this->prefix . 'ffc_action' => [
				'name'         => $this->prefix . 'ffc_action',
				'label'        => __( 'Action', 'gloo-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'options'      => [
					''        => 'None',
					'hide'    => 'Hide',
					'show'    => 'Show',
					'disable' => 'Disable',
				],
				'tab'          => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab'    => $this->prefix . 'ffc_tab'
			],
			$this->prefix . 'ffc_chain'  => [
				'name'         => $this->prefix . 'ffc_chain',
				'label'        => __( 'Condition Chain', 'gloo-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'description'  => 'Make sure input is not required by elementor.',
				'options'      => [],
				'condition'    => [ $this->prefix . 'ffc_action!' => '' ],
				'tab'          => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab'    => $this->prefix . 'ffc_tab'
			],
			$this->prefix . 'ffc_required'  => [
				'name'         => $this->prefix . 'ffc_required',
				'label'        => __( 'Required', 'gloo-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => [ $this->prefix . 'ffc_action!' => '' ],
				'tab'          => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab'    => $this->prefix . 'ffc_tab'
			],
			$this->prefix . 'ffc_debug'  => [
				'name'         => $this->prefix . 'ffc_debug',
				'label'        => __( 'Debug Mode', 'gloo-for-elementor' ),
				'description'  => 'Will output condition evaluation above the field (Visible to users with edit permissions and admins only)',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => [ $this->prefix . 'ffc_action!' => '' ],
				'tab'          => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab'    => $this->prefix . 'ffc_tab'
			],
		];
		$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Check render conditions
	 *
	 * @param  [type] $result  [description]
	 * @param  [type] $element [description]
	 *
	 * @return [type]          [description]
	 */
	public function evaluate_condition( $result, $element ) {

		if ( empty( $this->condition_list ) ) {
			return $result;
		}
		
		$js_controllable_only = $dom_removable = $contains_js_condition = false;
		$check                = boolval( $this->condition_list[0]['value'] );

		// if(isset($this->current_condition_chain_data) && isset($this->current_condition_chain_data['count']) && isset($this->current_condition_chain_data['php_conditions']) && $this->current_condition_chain_data['count'] == $this->current_condition_chain_data['php_conditions'] && $this->current_condition_chain_data['php_evaluate']){
		// 	$dom_removable = true;
		// }
		foreach ( $this->condition_list as $key => $item ) {
			if ( $item['type'] === 'js' ) {
				$contains_js_condition = true;
			}

			if ( $item['logic'] ) {
				switch ( $item['logic'] ) {
					case 'and' :
						$check = ( $check and $this->condition_list[ $key + 1 ]['value'] );
						if ( ! $check && $this->condition_list[ $key ]['type'] !== 'js' && $this->condition_list[ $key + 1 ]['type'] !== 'js' ) {
							// check is false, one or more of the conditions is php
							// $dom_removable = true;
						}

						break;
					case 'or' :
						$check = ( $check or $this->condition_list[ $key + 1 ]['value'] );
						if ( $this->condition_list[ $key ]['type'] === 'js' ) {
							// current condition is a js condition
							if ( ! $this->condition_list[ $key + 1 ]['type'] != 'js' && ! $this->condition_list[ $key + 1 ]['value'] ) {
								// hidden initially, shown with js
								$js_controllable_only = true;
							}

						}
						break;
				}
			}
		}

		// reset
		$this->condition_list = [];

		$dynamic_settings = $element->get_settings_for_display();
		$action           = $dynamic_settings[ $this->prefix . 'elements_action' ];
		// db($this->current_condition_chain_data);
		if(isset($this->current_condition_chain_data['current_listing_id'])){
			// $element->add_render_attribute( '_wrapper', [
			// 	'class' => [
			// 		'gloo-listing-id-'.$this->current_condition_chain_data['current_listing_id'],
			// 	],
			// ] );
			$element->add_render_attribute( '_wrapper', [
				'data-gloo-listing-id' => [
					$this->current_condition_chain_data['current_listing_id'],
				],
			] );
		}
		
		// sdf
		switch ( $action ) {
			case 'hide' :
				if ( $check && ! $contains_js_condition ) {
					// add hide class and show  with js
					$element->add_render_attribute( '_wrapper', [
						'class' => [
							'gloo-hidden-elementor-element',
						],
					] );

				}

				if ( $dom_removable || ($this->is_all_php_conditions() && $this->current_condition_chain_data['php_evaluate'])) {
					$element->add_render_attribute( '_wrapper', [
						'class' => [
							'gloo-remove-elementor-element',
						],
					] );
					$this->fix_removed_element_css($element);
					// delete from dom
					return false;
				}
				// else if(!isset($this->current_condition_chain_data['php_and_is_false'])){
				// 	$this->fix_removed_element_css($element);
				// 	return false;
				// }
				break;
			case 'show' :
				if ( ( ! $check ) || ( $check && $js_controllable_only ) || $contains_js_condition ) { // hide if condition not met
					// add hide class and show  with js
					$element->add_render_attribute( '_wrapper', [
						'class' => [
							'gloo-hidden-elementor-element',
						],
					] );
				}
				if ( $dom_removable || ($this->is_all_php_conditions() && !$this->current_condition_chain_data['php_evaluate'])) {
					// var_dump($result);db($element);exit();
					$element->add_render_attribute( '_wrapper', [
						'class' => [
							'gloo-remove-elementor-element',
						],
					] );
					$this->fix_removed_element_css($element);
					// delete from dom
					return false;
					
				}
				else if(isset($this->current_condition_chain_data['php_and_is_false'])){
					$element->add_render_attribute( '_wrapper', [
						'class' => [
							'gloo-remove-elementor-element',
						],
					] );
					$this->fix_removed_element_css($element);
					return false;
				}
				break;
		}


		return $result;
	}

	public function check_condition( $result, $element ) {

		$settings   = $element->get_settings();
		$is_enabled = ! empty( $settings[ $this->prefix . 'elements_status' ] ) ? $settings[ $this->prefix . 'elements_status' ] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_enabled ) {
			return $result;
		}

		$element->add_style_depends( 'gloo-for-elementor' );

		$dynamic_settings         = $element->get_settings_for_display();
		$conditions               = $this->get_elementor_condition_settings( \Elementor\Plugin::instance()->documents->get_current()->get_main_id() );
		// $conditions               = $this->get_elementor_condition_settings( get_the_ID() );
		$chain										= $dynamic_settings[ $this->prefix . 'elements_condition' ];
		$conditions['is_element'] = [
			'id'     => $element->get_id(),
			'action' => $dynamic_settings[ $this->prefix . 'elements_action' ],
			'condition_chain' => $chain,
			'js_generated' => 'no',
		];
		
		$this->current_condition_chain_data = array();
		
		$this->get_condition_chain_data_by_id($chain, $conditions);
		
		$check                    = $this->check_condition_by_id($chain , $conditions);
		
		return $result;

	}


	public function add_fluid_visibility_section( $element ) {

		if ( $element->get_name() === 'widget' ) {
			return;
		}

		$this->set_current_settings( $element );

		$element->start_controls_section(
			$this->prefix . 'section',
			[
				'label' => __( 'Fluid Logic', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			$this->prefix . 'enable_conditions',
			[
				'label'        => __( 'Enable', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			$this->prefix . 'condition_name', [
				'label'       => __( 'Condition Name', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			$this->prefix . 'is_form_field',
			[
				'label'        => __( 'Is Form Field', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			$this->prefix . 'condition_form_field_id', [
				'label'       => __( 'Form Field ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					$this->prefix . 'is_form_field' => 'yes',
				),
			]
		);

		/*$repeater->add_control(
			$this->prefix . 'condition_loop_item',
			[
				'label'        => __( 'Loop Item Content', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->prefix . 'is_form_field!' => 'yes',
				),
			]
		);*/


		//wrap
		$repeater->add_control(
			$this->prefix . 'condition_inverse',
			[
				'label'     => __( 'Condition', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => 'Is',
					'yes'     => "Isn't"
				],
				'condition' => array(
					$this->prefix . 'is_form_field!' => 'yes',
				),
			]
		);

		$repeater->add_control(
			$this->prefix . 'condition',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'groups'      => $this->conditions->get_grouped_conditions_for_options(),
				'condition'   => array(
					$this->prefix . 'is_form_field!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			$this->prefix . 'condition_js',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Condition (JS)', 'gloo_for_elementor' ),
				'label_block' => true,
				'groups'      => array(
					'general' => array(
						'label'   => __( 'Input Based', 'gloo_for_elementor' ),
						'options' => $this->conditions->get_js_conditions_for_options(),
					),
				),
				'condition'   => array(
					$this->prefix . 'is_form_field' => 'yes',
				),
			)
		);

		global $wp_roles;
		$user_roles = array();

		foreach ( $wp_roles->roles as $role_id => $role ) {
			$user_roles[ $role_id ] = $role['name'];
		}

		$repeater->add_control(
			$this->prefix . 'condition_user_role',
			array(
				'label'       => __( 'User role', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $user_roles,
				'label_block' => true,
				'condition'   => array(
					$this->prefix . 'condition' => array( 'user-role', 'user-role-not' ),
				),
			)
		);

		$repeater->add_control(
			$this->prefix . 'condition_user_id',
			array(
				'label'       => __( 'User IDs', 'gloo_for_elementor' ),
				'description' => __( 'Set comma separated IDs list (10, 22, 19 etc.). Note: ID Guest user is 0', 'gloo' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'condition'   => array(
					$this->prefix . 'condition' => array( 'user-id', 'user-id-not' ),
				),
			)
		);

		$repeater->add_control(
			$this->prefix . 'condition_field',
			array(
				'label'       => __( 'Field', 'gloo_for_elementor' ),
				'description' => __( 'Enter meta field name or select dynamic tag to compare value against. <br><b>Note!</b> If your meta field contains array you need to set meta field name manually (not with dynamic capability)', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
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
				'condition'   => array(
					$this->prefix . 'condition'      => $this->conditions->get_conditions_for_fields(),
					$this->prefix . 'is_form_field!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			$this->prefix . 'condition_value',
			array(
				'label'       => __( 'Returned Value', 'gloo_for_elementor' ),
				'description' => __( 'Set value to compare. Separate values with commas to set values list.', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => $this->prefix . 'condition',
							'operator' => 'in',
							'value'    => $this->conditions->get_conditions_with_value_detect(),
						],
						[
							'name'  => $this->prefix . 'is_form_field',
							'value' => 'yes',
						]
					]
				],
			)
		);
		
		$taxonomy = [];

		$tax_args = [
			'public' => true,
		];

		$taxonomies = get_taxonomies($tax_args);

		if(!empty($taxonomies)) {
			foreach ($taxonomies as $tax) {
				$tax_info = get_taxonomy($tax);
				$taxonomy[$tax] = $tax_info->label;
			}
		}

		$repeater->add_control(
			$this->prefix . 'condition_terms_taxonomy',
			array(
				'label'       => __( 'Taxonomy', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $taxonomy,
				'label_block' => true,
				'condition'   => array(
					$this->prefix . 'condition' => array( 'post-has-terms' ),
				),
			)
		);
		

		$data_types = apply_filters( 'gloo/modules/fluid_visibility/data-types', array(
			'chars'   => __( 'Chars (alphabetical comparison)', 'gloo_for_elementor' ),
			'numeric' => __( 'Numeric', 'gloo_for_elementor' ),
			'date'    => __( 'Datetime', 'gloo_for_elementor' )
		) );


		$repeater->add_control(
			$this->prefix . 'condition_data_type',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Data type', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'chars',
				'options'     => $data_types,
				'condition'   => array(
					$this->prefix . 'condition' => $this->conditions->get_conditions_with_type_detect(),
				),
			)
		);


		$repeater->add_control(
			$this->prefix . 'condition_case_sensitive_status',
			[
				'label'        => __( 'Case Sensitive', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				// 'default'     => 'No',
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);

		// wrap


		$repeater->add_control(
			$this->prefix . 'condition_next_status',
			[
				'label'        => __( 'Next Condition', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			$this->prefix . 'condition_next',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => __( 'Next Condition', 'gloo_for_elementor' ),
				'description' => __( 'Choose one condition to check next.', 'gloo_for_elementor' ),
				'options'     => $this->get_conditions(),
				'multiple'    => false,
				'condition'   => [
					$this->prefix . 'condition_next_status' => 'yes',
				],
			]
		);

		$repeater->add_control(
			$this->prefix . 'condition_next_logic',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'description' => __( 'The logical operator used for the current and the next condition selected.<br><br><b>OR</b> will evaluate to TRUE if <u>one</u> of the conditions is met.<br><b>AND</b> will evaluate to TRUE only if <u>both</u> conditions are met.', 'gloo_for_elementor' ),
				'options'     => [
					'or'  => 'OR',
					'and' => 'AND',
				],
				'condition'   => [
					$this->prefix . 'condition_next_status' => 'yes'
				]
			)
		);


		$element->add_control(
			$this->prefix . 'conditions',
			[
				'label'       => __( 'Conditions', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ ' . $this->prefix . 'condition_name' . ' }}}',
				'prevent_empty' => false,
				'condition'   => [
					$this->prefix . 'enable_conditions' => 'yes',
				]
			]
		);


		//Fix repeater bug when interactor is not active
		$is_interactor_active = gloo()->modules->is_module_active('interactor');
		if(!$is_interactor_active){
			$interactor_prefix = 'gloo_interactor_';
			$events_repeater = new \Elementor\Repeater();
	
			$events_repeater->add_control(
				$interactor_prefix . 'event_title',
				[
					'label'       => __( 'Title', 'gloo_for_elementor' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'Event Title', 'gloo_for_elementor' ),
				]
			);
	
			$element->add_control(
				$interactor_prefix . 'events',
				[
					'label'         => __( 'Events', 'gloo_for_elementor' ),
					'type'          => Controls_Manager::REPEATER,
					'prevent_empty' => false,
					'fields'        => $events_repeater->get_controls(),
					'title_field' => '{{{ ' . $interactor_prefix . 'event_title }}}',
					'condition'   => [
						$this->prefix . 'gloo_enable_conditions' => 'yes',
					]
				]
			);
		}

		$element->end_controls_section();

	}

	public function set_current_settings( $element ) {
		$control = (array) $element;
		if ( ! isset( $control["\0Elementor\Controls_Stack\0data"]['settings'] ) ) {
			return;
		}
		$this->settings = $control["\0Elementor\Controls_Stack\0data"]['settings'];

		return $this->settings;
	}

	public function get_current_settings() {
		return $this->settings;
	}

	public function get_conditions() {
		return $this->get_settings_as_options( $this->prefix . 'conditions', $this->prefix . 'condition_name' );
	}


	public function get_settings_as_options( $repeater_key, $title_key ) {

		if ( isset( $this->options[ $repeater_key ] ) && ! empty( $this->options[ $repeater_key ] ) ) {
			return $this->options[ $repeater_key ];
		}

		$current_settings = $this->get_current_settings();

		$settings = isset( $current_settings[ $repeater_key ] ) && ! empty( $current_settings[ $repeater_key ] ) ? $current_settings[ $repeater_key ] : false;
		$results  = [];
		if ( $settings ) {

			foreach ( $settings as $key => $setting ) {
				if ( ! isset( $setting['_id'] ) ) {
					continue;
				}
				$title                      = isset( $setting[ $title_key ] ) && $setting[ $title_key ] ? $setting[ $title_key ] : 'Item #' . ( $key + 1 );
				$results[ $setting['_id'] ] = $title;
			}
		}
		$this->options[ $repeater_key ] = $results;


		return $results;
	}

	public function get_condition_chain_data_by_id($id, $conditions){
		
		if(!$conditions)
			return;

		if(isset($conditions['current_listing_id']))
			$this->current_condition_chain_data['current_listing_id'] = $conditions['current_listing_id'];

		$this->current_condition_chain_data['js_generated'] = 'no';
		$conditions_operators = array('and' => '&&', 'or' => '||');

		if(isset($this->current_condition_chain_data['count']))
			$this->current_condition_chain_data['count']++;
		else
			$this->current_condition_chain_data['count'] = 1;

		if(!isset($this->current_condition_chain_data['php_conditions']))
			$this->current_condition_chain_data['php_conditions'] = 0;

		if(!isset($this->current_condition_chain_data['php_evaluate']))
			$this->current_condition_chain_data['php_evaluate'] = true;

		if(!isset($this->current_condition_chain_data['js_conditions']))
			$this->current_condition_chain_data['js_conditions'] = 0;

		$condition_index    = array_search( $id, array_column( $conditions, '_id' ) );
		$condition          = $conditions[ $condition_index ];
		$selected_condition = $condition[ $this->prefix . 'condition' ];

		if ( $condition[ $this->prefix . 'is_form_field' ] === 'yes' ) {
			$this->current_condition_chain_data['js_conditions']++;
			$selected_condition = $condition[ $this->prefix . 'condition_js' ];
			$this->current_condition_chain_data['has_js'] = $condition[ '_id' ];
		}else{

			
			$this->current_condition_chain_data['php_conditions']++;
			$condition_instance = $this->conditions->get_condition( $selected_condition );
			
			if ( ! $condition_instance ) {
				return false;
			}


			$args = array(
				'type'          => null,
				'user_role'     => null,
				'user_id'       => null,
				'field'         => null,
				'value'         => null,
				'data_type'     => null,
				'form_field_id' => null,
				'inverse'       => null,
				'terms_taxonomy' => null,
				'loop_item' 	=> null,
				'all_conditions' => $conditions,
				'current_condition' => $condition,
				'condition_manager' => $this->conditions,
			);
	
	
			foreach ( $args as $arg => $default ) {
				$key          = $this->prefix . 'condition_' . $arg;
				$args[ $arg ] = ! empty( $condition[ $key ] ) ? $condition[ $key ] : $default;
			}

			$is_dynamic_field = isset( $condition['__dynamic__'][ $this->prefix . 'condition_field' ] );
			$is_empty_field   = empty( $condition[ $this->prefix . 'condition_field' ] );
			// if( ! $is_dynamic_field && ! $is_empty_field ){
			// 	$args['field_raw'] = $condition[ $this->prefix . 'condition_field' ];
			// }
			// elseif(!$is_empty_field){
			// 	$args['field_raw'] = $condition[ $this->prefix . 'condition_field' ];
			// }
			// else
				$args['field_raw'] = null;
			$php_evaluate = $condition_instance->evaluate( $args );
			$this->current_condition_chain_data['php_results'][$id] = $php_evaluate;
			$current_php_evaluate = $php_evaluate;
			// db($args['field_raw']);
			if(!$current_php_evaluate){
				// db($is_dynamic_field);
				// db($is_empty_field);
				// db($args['field_raw']);
				// db($condition_instance);
				// db($args['field']);
				// db($condition[ $this->prefix . 'condition_field' ] );
				// db(empty( $condition[ $this->prefix . 'condition_field' ] ));
				// db($is_dynamic_field);db($is_empty_field);
				// db($args);
				// db($current_php_evaluate);
			}
			
			if(isset($this->current_condition_chain_data['condition_operator'])){
				if($this->current_condition_chain_data['condition_operator'] == 'or'){
					if($php_evaluate || $this->current_condition_chain_data['php_evaluate'])
						$php_evaluate = true;
					else
						$php_evaluate = false;
				}else{
					if($php_evaluate && $this->current_condition_chain_data['php_evaluate'])
						$php_evaluate = true;
					else
						$php_evaluate = false;
				}
			}
			else if($php_evaluate && $this->current_condition_chain_data['php_evaluate']){
				$php_evaluate = true;
			}
			else{
				$php_evaluate = false;
			}

			$this->current_condition_chain_data['php_evaluate'] = $php_evaluate;
			
			if(!isset($this->current_condition_chain_data['has_js'])){
				if(isset($this->current_condition_chain_data['before_js_operators']))
					$this->current_condition_chain_data['before_js_operators'] .= ($current_php_evaluate ? 'true' : 'false');
				else
					$this->current_condition_chain_data['before_js_operators'] = ($current_php_evaluate ? 'true' : 'false');
			}

			if($current_php_evaluate){
				if ( isset( $condition[ $this->prefix . 'condition_next_status' ] ) && $condition[ $this->prefix . 'condition_next_status' ] && isset( $condition[ $this->prefix . 'condition_next' ] ) && $condition[ $this->prefix . 'condition_next' ]) {
					$condition_operator = (isset( $condition[ $this->prefix . 'condition_next_logic' ] ) && $condition[ $this->prefix . 'condition_next_logic' ] ? $condition[ $this->prefix . 'condition_next_logic' ] : 'and');
					if($condition_operator == 'or'){
						$this->current_condition_chain_data['php_or_is_true'] = $condition['_id'];
					}
					else
						$condition_operator_and = 'ok';
				}
				if(isset($this->current_condition_chain_data['has_or'])){
					$this->current_condition_chain_data['php_or_is_true'] = $condition['_id'];
				}
			}

			$this->current_condition_chain_data['php_evaluate'] = $php_evaluate;
		}

		if(isset($current_php_evaluate) && $current_php_evaluate == false){
			// alert($this->current_condition_chain_data['condition_operator']);
			// if((isset($this->current_condition_chain_data['condition_operator']) && $this->current_condition_chain_data['condition_operator'] == 'and'))
			// alert('test');
			if((isset($this->current_condition_chain_data['condition_operator']) && $this->current_condition_chain_data['condition_operator'] == 'and') || isset($condition_operator_and)){
				$this->current_condition_chain_data['php_and_is_false'] = 'ok';
			}
		}

		if ( isset( $condition[ $this->prefix . 'condition_next_status' ] ) && $condition[ $this->prefix . 'condition_next_status' ] && isset( $condition[ $this->prefix . 'condition_next' ] ) && $condition[ $this->prefix . 'condition_next' ]) {
			$condition_operator = (isset( $condition[ $this->prefix . 'condition_next_logic' ] ) && $condition[ $this->prefix . 'condition_next_logic' ] ? $condition[ $this->prefix . 'condition_next_logic' ] : 'and');
			$this->current_condition_chain_data['condition_operator'] = $condition_operator;
			if($condition_operator == 'or'){

				if(!isset($this->current_condition_chain_data['has_js'])){
					$this->current_condition_chain_data['before_js_operators'] .= ' || ';
				}

				$this->current_condition_chain_data['has_or'] = true;
			}else{

				if(!isset($this->current_condition_chain_data['has_js'])){
					$this->current_condition_chain_data['before_js_operators'] .= ' && ';
				}

			}

			$condition_index    = array_search( $condition[ $this->prefix . 'condition_next' ], array_column( $conditions, '_id' ) );
			$next_condition          = $conditions[ $condition_index ];
			$selected_condition = $next_condition[ $this->prefix . 'condition' ];

			$this->get_condition_chain_data_by_id($condition[ $this->prefix . 'condition_next' ], $conditions);
		}
		// return $output;
	}

	function generate_php_conditions_js($args){
		$output = '';
		if($args['current_condition'][''])
		$field_id   = $args['form_field_id']; 
		// db($args);
		$action   = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$opposite = $this->opposite_action( $action );
		$value = $args['value'];

		$field_selector = "#form-field-$field_id, .elementor-field-type-checkbox.elementor-field-group-$field_id [value=\"$value\"], .elementor-field-type-radio.elementor-field-group-$field_id input,.elementor-field-type-gloo_terms_field.elementor-field-group-$field_id input,.elementor-field-type-gloo_terms_field.elementor-field-group-$field_id [value=\"$value\"],.elementor-field-type-gloo_cpt_field.elementor-field-group-$field_id input,.elementor-field-type-gloo_cpt_field.elementor-field-group-$field_id [value=\"$value\"]";
		if(isset($args['condition_meta_data']) && isset($args['condition_meta_data']['count']) && isset($args['condition_meta_data']['php_conditions']) && $args['condition_meta_data']['count'] == $args['condition_meta_data']['php_conditions']){
			if(isset($args['is_element']) && isset($args['is_element']['id']) && $args['is_element']['id']){
			
				if($args['type'] == 'hide' && $args['condition_meta_data']['php_evaluate']){
					$output .= "jQuery(document).ready(function($){
						jQuery('.elementor-element-".$args['is_element']['id']."').remove();
					});";
				}
				elseif($args['condition_meta_data']['php_evaluate']){
					$output .= "jQuery(document).ready(function($){
						jQuery('.elementor-element-".$args['is_element']['id']."').$action();
					});";
				}
				else{
					$output .= "jQuery(document).ready(function($){
						jQuery('.elementor-element-".$args['is_element']['id']."').$opposite();
					});";
				}
			
		}else{

			$custom_id  = $args['custom_id'];

			if ( $action === 'disable' ) {
				$action   = "prop('disabled', true)";
				$opposite = "prop('disabled', false)";
			} else {
				$action   = "closest('.elementor-field-group').$action()";
				$opposite = "closest('.elementor-field-group').$opposite()";
			}

			// db($args['condition_meta_data']);
				if($args['type'] == 'hide' && $args['condition_meta_data']['php_evaluate']){
					$output .= "jQuery(document).ready(function($){
						jQuery('#form-field-$custom_id').closest('.elementor-field-group').remove();
					});";
				}
				elseif($args['condition_meta_data']['php_evaluate']){
					$output .= "jQuery(document).ready(function($){
						jQuery('#form-field-$custom_id').$action;
					});";
				}
				else{
					$output .= "jQuery(document).ready(function($){
					jQuery('#form-field-$custom_id').$opposite;
				});";
				}
			
		}
		}
		
		return $output;
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

	public function get_file_time($file){
			return date("ymd-Gis", filemtime( $file ));
	}
	
	public function editor_scripts() {
		$script_abs_path = gloo()->modules_path( 'fluid-visibility/assets/admin/js/script.js');
		wp_enqueue_script( 'gloo-fluid-visibility-script', gloo()->plugin_url() . 'includes/modules/fluid-visibility/assets/admin/js/script.js', [ 'jquery' ], $this->get_file_time($script_abs_path) );

		wp_enqueue_script( 'gloo-interactor-button-js', gloo()->plugin_url() . 'assets/js/admin/gloo-interactor-button.js', [ 'jquery' ]);
		wp_enqueue_style('gloo-interactor-button-css', gloo()->plugin_url() . 'assets/css/gloo-interactor-button.css',);
		
	}

	public function fix_removed_element_css($element){
		// $this->need_unregistered_inline_css_widget = false;
		if ( 'widget' === $element->get_type() ) {
			$is_inline_css_mode = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_css_loading' );
			if ( $is_inline_css_mode /*&& ! in_array( $element->get_name(), $element::$registered_inline_css_widgets )*/ ) {
				// if(isset($_GET['test'])){
						if ( in_array( $element->get_name(), $element::$registered_inline_css_widgets ) ) {
							$registered_inline_css_widgets = $element::$registered_inline_css_widgets;
							$index = array_search( $element->get_name(), $registered_inline_css_widgets );
							unset( $registered_inline_css_widgets[ $index ] );
							$element::$registered_inline_css_widgets = $registered_inline_css_widgets;
						}
				// }
				// $this->need_unregistered_inline_css_widget = true;
			}
		}
	}


	public function is_all_php_conditions(){
		if(isset($this->current_condition_chain_data) && isset($this->current_condition_chain_data['count']) && isset($this->current_condition_chain_data['php_conditions']) && $this->current_condition_chain_data['count'] == $this->current_condition_chain_data['php_conditions']/*) || isset($this->current_condition_chain_data['php_and_is_false'])*/)
			return true;
		else
			return false;
	}

}