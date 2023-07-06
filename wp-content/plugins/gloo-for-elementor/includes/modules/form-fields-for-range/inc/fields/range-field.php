<?php

namespace Gloo\Modules\Form_Fields_For_Range\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Range_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $depended_styles = [ 'gloo-for-elementor', 'gloo_rslider_css' ];
	public $depended_scripts = [ 'gloo_form_fields_for_range', 'gloo_range' ];
	private $prefix = 'gloo_range_';
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
		// rSlider.min.js
		wp_register_script( 'gloo_form_fields_for_range', gloo()->plugin_url( 'includes/modules/form-fields-for-range/assets/js/rSlider.min.js'), array('jquery'), '1.0.5');
		wp_register_script( 'gloo_range', gloo()->plugin_url( 'includes/modules/form-fields-for-range/assets/js/gloo-range.js'), array('jquery'), '1.0');
		wp_register_style( 'gloo_rslider_css', gloo()->plugin_url( 'includes/modules/form-fields-for-range/assets/css/rSlider.min.css'));

	}

	public function get_name() {
		return 'Range Field';
	}

	public function get_label() {
		return __( 'Range Field', 'gloo' );
	}

	public function get_type() {
		return 'gloo_range_field';
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
 
		$field_controls = [
			$this->prefix.'custom_values'    => [
				'name'         => $this->prefix.'custom_values',
				'label'        => __( 'Custom Values', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'show or hide tooltips',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'min_number'    => [
				'name'         => $this->prefix.'min_number',
				'label' => esc_html__( 'Min. Number', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'step' => 1,
				'default' => 1,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '=',
							'value' => $this->get_type(),
						],
						[
							'name' => $this->prefix.'custom_values',
							'operator' => '!=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'yes',
						],
					],
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'max_number'    => [
				'name'         => $this->prefix.'max_number',
				'label' => esc_html__( 'Max. Number', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'step' => 1,
				'default' => 10,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '=',
							'value' => $this->get_type(),
						],
						[
							'name' => $this->prefix.'custom_values',
							'operator' => '!=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'yes',
						],
					],
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'values'  => [
				'name'         => $this->prefix.'values',
				'label'        => __( 'Values', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '1,2,3,4,5', 'gloo_for_elementor' ),
				'description' => esc_html__( 'Custom values with commas separated e.g 1,2,3,4,5', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'custom_values' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'set_intial'    => [
				'name'         => $this->prefix.'set_intial',
				'label'        => __( 'Set Initial Values', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '2,5', 'gloo_for_elementor' ),
				'description' => 'A flat array of one (single slider) or two (range slider) values to set initial values (optional) e.g 2,5',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'desktop_range_width' => [
				'name'         => $this->prefix.'desktop_range_width',
				'label' => esc_html__( 'Desktop Width', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'tablet_range_width' => [
				'name'         => $this->prefix.'tablet_range_width',
				'label' => esc_html__( 'Tablet Width', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'mobile_range_width' => [
				'name'         => $this->prefix.'mobile_range_width',
				'label' => esc_html__( 'Mobile Width', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'step_number'    => [
				'name'         => $this->prefix.'step_number',
				'label' => esc_html__( 'Step', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'default' => 1,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'range'    => [
				'name'         => $this->prefix.'range',
				'label'        => __( 'Range', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'if the slider is range or single type',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'tooltip'    => [
				'name'         => $this->prefix.'tooltip',
				'label'        => __( 'Tooltip', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'show or hide tooltips',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'scale'    => [
				'name'         => $this->prefix.'scale',
				'label'        => __( 'Scale', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'show or hide scale',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'labels'    => [
				'name'         => $this->prefix.'labels',
				'label'        => __( 'Labels', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => 'show or hide scale labels',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'alignment'    => [
				'name'         => $this->prefix.'alignment',
				'label' => esc_html__( 'Alignment', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
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
			$this->prefix.'active_track_border_color',
			[
				'label'     => __( 'Active Track Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rs-container .rs-selected' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .rs-container .rs-pointer' => 'border-color: {{VALUE}};'
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
 
	public function get_rangefield_settings($item, $item_index, $form) {
		$settings = $form->get_settings_for_display( 'form_fields' );
		$setting_array = array();

		/* global settings */
		$setting_array['range'] = false;
		$setting_array['labels'] = false;
		$setting_array['scale'] = false;
		$setting_array['tooltip'] = false;
		// $setting_array['width'] = '300px';

		if($settings[ $item_index ][$this->prefix.'custom_values'] && $settings[ $item_index ][$this->prefix.'custom_values'] == 'yes') {
			$custom_values = $settings[ $item_index ][$this->prefix.'values'];

			if(!empty($custom_values)) {
				$values = explode(',', $custom_values);
				$setting_array['values'] = $values;
			}
		} else {

			if(isset($settings[ $item_index ][$this->prefix.'min_number']) && isset($settings[ $item_index ][$this->prefix.'max_number'])) {
				$min_number = $settings[ $item_index ][$this->prefix.'min_number'];
				$max_number = $settings[ $item_index ][$this->prefix.'max_number'];

				$setting_array['values'] = array(
					'min' => $min_number,
					'max' => $max_number
				);
			}
		}

		if(isset($settings[ $item_index ][$this->prefix.'set_intial'])) {

			if(isset($item['field_value']) && !empty($item['field_value'])) {
				$set_intial = $item['field_value'];
			} else {
				$set_intial = $settings[ $item_index ][$this->prefix.'set_intial'];
			}

			$intial = explode(',', $set_intial);
			$setting_array['set'] = $intial;
		}
 		
		if($settings[ $item_index ][$this->prefix.'range'] && $settings[ $item_index ][$this->prefix.'range'] == 'yes') {
			$setting_array['range'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'labels'] && $settings[ $item_index ][$this->prefix.'labels'] == 'yes') {
			$setting_array['labels'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'tooltip'] && $settings[ $item_index ][$this->prefix.'tooltip'] == 'yes') {
			$setting_array['tooltip'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'scale'] && $settings[ $item_index ][$this->prefix.'scale'] == 'yes') {
			$setting_array['scale'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'step_number'] && !empty($settings[ $item_index ][$this->prefix.'step_number'])) {
			$setting_array['step'] = $settings[ $item_index ][$this->prefix.'step_number'];
		} 

		$desktop_width = $settings[ $item_index ][$this->prefix.'desktop_range_width'];
		$tablet_width = $settings[ $item_index ][$this->prefix.'tablet_range_width'];
		$mobile_width = $settings[ $item_index ][$this->prefix.'mobile_range_width'];
		
		$width_data = [
			'desktop_width' => $desktop_width['size'].$desktop_width['unit'],
			'tablet_width' => $tablet_width['size'].$tablet_width['unit'],
			'mobile_width' => $mobile_width['size'].$mobile_width['unit']
		];

		$setting_array['width'] = $width_data;
		
		return $setting_array;
	}

	/*	
	* @param Form $form
	*/
   public function render( $item, $item_index, $form ) {

		$settings = $form->get_settings_for_display( 'form_fields' );
		$field_settings = $this->get_rangefield_settings($item, $item_index, $form);

		$form->add_render_attribute( 'input' . $item_index, 'data-field-settings', wp_json_encode( $field_settings, JSON_NUMERIC_CHECK ) );
		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual gloo-range-field' );
		$form->add_render_attribute( 'input' . $item_index, 'type', 'range' );
		$form->add_render_attribute( 'field-group' . $item_index, 'class', 'field-align-center' );

		$alignmnet_class = '';
		
		if(isset( $settings[ $item_index ][$this->prefix.'alignment'] ) && !empty($settings[ $item_index ][$this->prefix.'alignment'])) {
			$alignmnet_class = ' gloo-field-alignment-'.$settings[ $item_index ][$this->prefix.'alignment'];
		}

		//$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual gloo-range-field' );
		echo '<div class="gloo-range-wrap'.$alignmnet_class.'"><input size="1" ' . $form->get_render_attribute_string( 'input' . $item_index ) . '></div>';
   }
}
