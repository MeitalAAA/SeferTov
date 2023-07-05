<?php

namespace Gloo\Modules\Form_Fields_For_Time_Span\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Time_Span_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $depended_styles = [ 'gloo-for-elementor'];
	public $depended_scripts = [ 'gloo_time_span','moment' ];
	private $prefix = 'gloo_time_span_';
	public function __construct() {

		add_action( 'elementor/element/form/section_form_style/after_section_end', [
			$this,
			'add_control_section_to_form'
		], 10, 2 );


		add_action( 'elementor/widget/print_template', function ( $template, $widget ) {
			if ( 'form' === $widget->get_name() ) {
				$template = false;
			}

			return $template;
		}, 10, 2 );

		parent::__construct();

		wp_register_script( 'gloo_time_span', gloo()->plugin_url( 'includes/modules/form-fields-time-span/assets/js/gloo-time-span-field.js'), array('jquery'), '1.0');
	}

	public function get_name() {
		return 'Time Span';
	}

	public function get_label() {
		return __( 'Time Span', 'gloo' );
	}

	public function get_type() {
		return 'gloo_time_span_field';
	}

	/**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$wordpress_date_format = get_option( 'date_format' );
		$default_date_options           = $this->get_default_date_options();
		$default_date_options['custom'] = 'Custom';
		
		$field_controls = [
			$this->prefix.'is_hidden_field'    => [
				'name'         => $this->prefix.'is_hidden_field',
				'label'        => __( 'Is Hidden ?', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'This will hide the field on frontend',
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'default_value' => [
				'name'         => $this->prefix.'default_value',
				'label' => __( 'Default Value', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => ['field_type' => $this->get_type()],
        		'default' => '0',
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'start_date_field_id'    => [
				'name'         => $this->prefix.'start_date_field_id',
				'label'        => __( 'Start Date Field ID ?', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'This will allow to populate start date value from a field',
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_start'    => [
				'name'         => $this->prefix.'date_difference_start',
				'label'       => __( 'Start Date', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Current Date',
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_start_format'    => [
				'name'         => $this->prefix.'date_difference_start_format',
				'label'       => __( 'Start Date Format', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'DD/MM/YYYY',
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'end_date_field_id'    => [
				'name'         => $this->prefix.'end_date_field_id',
				'label'        => __( 'End Date Field ID ?', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'This will allow to populate end date value from a field',
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_end'    => [
				'name'         => $this->prefix.'date_difference_end',
				'label'       => __( 'End Date', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Current Date',
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_end_format'    => [
				'name'         => $this->prefix.'date_difference_end_format',
				'label'       => __( 'End Date Format', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'DD/MM/YYYY',
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_output'    => [
				'name'         => $this->prefix.'date_difference_output',
				'label'   => __( 'Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $default_date_options,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'container' => [
				'name'         => $this->prefix.'container',
				'label' => __( 'HTML Wrapper', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'div',
				'options' => [
					'h1'  => __( 'H1', 'gloo_for_elementor' ),
					'h2'  => __( 'H2', 'gloo_for_elementor' ),
					'h3'  => __( 'H3', 'gloo_for_elementor' ),
					'h4'  => __( 'H4', 'gloo_for_elementor' ),
					'h5'  => __( 'H5', 'gloo_for_elementor' ),
					'h6'  => __( 'H6', 'gloo_for_elementor' ),
					'span'  => __( 'Span', 'gloo_for_elementor' ),
					'div'  => __( 'Div', 'gloo_for_elementor' ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix.'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],
				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'before_text'    => [
				'name'         => $this->prefix.'before_text',
				'label'       => __( 'Before Text', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix.'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'after_text'    => [
				'name'         => $this->prefix.'after_text',
				'label'       => __( 'After Text', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix.'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_output_custom'    => [
				'name'         => $this->prefix.'date_difference_output_custom',
				'label'       => __( 'Custom Output', 'gloo_for_elementor' ),
				'description' => 'Usable Variables: <br>%seconds%, %minutes% ,%hours% ,%days% ,%months% ,%years% <br>Example: %years% Year(s) and %months% Month(s)',
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'condition'   => [
					$this->prefix.'date_difference_output' => 'custom',
					'field_type' => $this->get_type(),
				],
				'dynamic'     => [
					'active' => true,
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'date_difference_handle_negative'    => [
				'name'         => $this->prefix.'date_difference_handle_negative',
				'label'        => __( 'Always Positive Value', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'Convert the value to a positive value',
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			
 		];
 
		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'range_styles',
			[
				'label' => __( 'Range Fields', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_range_heading',
			[
				'label'     => __( 'Range Field', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$element->add_control(
			$this->prefix.'active_slide_color',
			[
				'label'     => __( 'Active Track Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			$this->prefix.'active_background_color',
			[
				'label'     => __( 'Track Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-bg' => 'background-color: {{VALUE}};',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'slider_border_color',
			[
				'label'     => __( 'Track Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-bg' => 'border-color: {{VALUE}};',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'tooltip_bg_color',
			[
				'label'     => __( 'Tooltip Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-tooltip' => 'background-color: {{VALUE}};',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'tooltip_border_color',
			[
				'label'     => __( 'Tooltip Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .rs-tooltip' => 'border-color: {{VALUE}};',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'pointer_border_color',
			[
				'label'     => __( 'Pointer Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-pointer' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .rs-container .rs-pointer' => 'box-shadow: none;',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'pointer_bg_color',
			[
				'label'     => __( 'Pointer Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-pointer' => 'background-color: {{VALUE}};',
 				],
			]
		);

		$element->add_control(
			$this->prefix.'scale_color',
			[
				'label'     => __( 'Scale Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-scale span::before' => 'background-color: {{VALUE}};',
 				],
			]
		);
		
		$element->add_control(
			$this->prefix.'number_color',
			[
				'label'     => __( 'Number Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-scale span ins' => 'color: {{VALUE}};',
 				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'handler_size',
			[
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( 'Handler Size', 'gloo_for_elementor' ),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-pointer' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'vertical_align',
			[
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( 'Handler Vertical Position', 'gloo_for_elementor' ),
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-pointer' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'track_height',
			[
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( 'Track Height', 'gloo_for_elementor' ),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-bg' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rs-container .rs-selected' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

 		$element->end_controls_section();
	}
 
	
	public function get_time_span_field_settings($item, $item_index, $form) {
		$settings = $form->get_settings_for_display( 'form_fields' );
		$setting_array = array();

		/* global settings */
		$setting_array['start_date_field_id'] = false;
		$setting_array['end_date_field_id'] = false;
		$setting_array['is_hidden'] = false;
		$setting_array['allow_negative'] = true;

		if($settings[ $item_index ][$this->prefix.'is_hidden_field'] && $settings[ $item_index ][$this->prefix.'is_hidden_field'] == 'yes') {
			$setting_array['is_hidden'] = true;
		}

		if($settings[ $item_index ][$this->prefix.'start_date_field_id'] == 'yes') {
			$setting_array['start_date_field_id'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'date_difference_start'] ) {
			if($settings[ $item_index ][$this->prefix.'date_difference_start'] == 'today') {
				$setting_array['start_date']	= date('d/m/Y H:i:s');
			} else { 
				$setting_array['start_date'] = $settings[ $item_index ][$this->prefix.'date_difference_start'];
			}
		} else {
			$setting_array['start_date']	= date('d/m/Y H:i:s');
		}

		if($settings[ $item_index ][$this->prefix.'date_difference_start_format'] ) {
			$setting_array['start_format'] = $settings[ $item_index ][$this->prefix.'date_difference_start_format'];
		} else {
			$setting_array['start_format'] = 'DD/MM/YYYY';
		}

		if($settings[ $item_index ][$this->prefix.'end_date_field_id'] == 'yes') {
			$setting_array['end_date_field_id'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'date_difference_end'] ) {

			if($settings[ $item_index ][$this->prefix.'date_difference_end'] == 'today') {
				$setting_array['end_date']	= date('d/m/Y H:i:s');
			} else {
				$setting_array['end_date'] = $settings[ $item_index ][$this->prefix.'date_difference_end'];
			}
		} else {
			$setting_array['end_date']	= date('d/m/Y H:i:s');
		}

		if($settings[ $item_index ][$this->prefix.'date_difference_end_format'] ) {
			$setting_array['end_format'] = $settings[ $item_index ][$this->prefix.'date_difference_end_format'];
		} else {
			$setting_array['end_format'] = 'DD/MM/YYYY';
		}

		if($settings[ $item_index ][$this->prefix.'date_difference_output_custom'] ) {
			$setting_array['output_custom'] = $settings[ $item_index ][$this->prefix.'date_difference_output_custom'];
		} 

		if( $settings[ $item_index ][$this->prefix.'date_difference_output'] ) {
			$output = $settings[ $item_index ][$this->prefix.'date_difference_output'];
			$setting_array['output'] = $output;

			$custom_output = $settings[ $item_index ][$this->prefix.'date_difference_output_custom'] ;

			if(!empty($custom_output)) {
				
				preg_match_all( '/%([a-zA-Z])*%/', $custom_output, $custom_format_output );

				if($output == 'custom' && isset($custom_format_output[0]) && !empty($custom_format_output[0])) {
					$mapped = array_map(
						function ($val) {
							return str_replace('%','', $val);
						},
						$custom_format_output[0]
					);

					$setting_array['included_output'] = $mapped;
				}
			}
		} 
  
		if($settings[ $item_index ][$this->prefix.'date_difference_handle_negative'] ) {
			$setting_array['handle_negative'] = $settings[ $item_index ][$this->prefix.'date_difference_handle_negative'];
		} 

		if($settings[ $item_index ][$this->prefix.'date_difference_handle_negative'] && $settings[ $item_index ][$this->prefix.'date_difference_handle_negative'] == 'yes') {
			$setting_array['allow_negative'] = false;
		}
  		
		$setting_array['default_format'] = '';
		 
		return $setting_array;
	}

	public function get_default_date_options() {
		return [
			'seconds' => 'Seconds',
			'minutes' => 'Minutes',
			'hours'   => 'Hours',
			'days'    => 'Days',
			'weeks'   => 'Weeks',
			'months'  => 'Months',
			'years'   => 'Years',
		];
	}

 	/*	
	* @param Form $form
	*/
   	public function render( $item, $item_index, $form ) {

		$settings = $form->get_settings_for_display( 'form_fields' );
		$field_settings = $this->get_time_span_field_settings($item, $item_index, $form);
		
		$form->add_render_attribute( 'input' . $item_index, 'data-field-settings', wp_json_encode( $field_settings, JSON_NUMERIC_CHECK ) );
		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual gloo-time-span-field' );
		
		$html_output = '<input type="hidden" '.$form->get_render_attribute_string( 'input' . $item_index ).' value="'.$item[$this->prefix.'default_value'].'" />';
			
		if(isset($item[$this->prefix.'is_hidden_field']) && $item[$this->prefix.'is_hidden_field'] != 'yes') {
			if(!empty($item[$this->prefix.'container']))
				$html_output .= '<'.$item[$this->prefix.'container'].' class="time_span_field_value">';

			if(!empty($item[$this->prefix.'before_text']))
				$html_output .= '<span class="time_span_field_before_text">'.$item[$this->prefix.'before_text'].'</span>';
				
			$html_output .= '<span class="time_span_field_result">'.$item[$this->prefix.'default_value'].'</span>';

			if(!empty($item[$this->prefix.'after_text']))
				$html_output .= '<span class="time_span_field_after_text">'.$item[$this->prefix.'after_text'].'</span>';

			if(!empty($item[$this->prefix.'container']))
				$html_output .= '</'.$item[$this->prefix.'container'].'>';
		}
		echo $html_output;

   	}
}
