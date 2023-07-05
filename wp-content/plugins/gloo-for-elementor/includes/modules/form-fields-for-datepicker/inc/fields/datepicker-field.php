<?php
namespace Gloo\Modules\Form_Fields_For_Datepicker\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Datepicker_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $prefix = 'gloo_datepicker_';
	public $depended_styles = [ 'gloo_lightpick_css' ];
	public $depended_scripts = [ 'moment_js', 'gloo_lightpick_js', 'gloo_datepicker_js' ];

	public function __construct() {

		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this,'add_control_section_to_form'], 10, 2 );

		add_action( 'elementor/widget/print_template', function ( $template, $widget ) {
			if ( 'form' === $widget->get_name() ) {
				$template = false;
			}

			return $template;
		}, 10, 2 );

		parent::__construct();

		wp_register_script( 'moment_js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js', array('jquery'), '1.0');
		wp_register_script( 'gloo_lightpick_js', gloo()->plugin_url( 'includes/modules/form-fields-for-datepicker/assets/js/lightpick.js'), array('jquery'), '1.0');
		wp_register_script( 'gloo_datepicker_js', gloo()->plugin_url( 'includes/modules/form-fields-for-datepicker/assets/js/gloo-datepicker.js'), array('jquery'), '1.0');

		wp_register_style( 'gloo_lightpick_css', gloo()->plugin_url( 'includes/modules/form-fields-for-datepicker/assets/css/lightpick.css'));
	}

	public function get_name() {
		return 'Datepicker';
	}

	public function get_label() {
		return __( 'Datepicker', 'gloo' );
	}

	public function get_type() {
		return 'gloo_datepicker_field';
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

		$taxonomies = get_taxonomies(
			[]
			, 'objects'
		);
		$options    = [ '' => '' ];
		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		$field_controls = [
			$this->prefix.'format'    => [
				'name'         => $this->prefix.'format',
				'label'        => __( 'Date Format', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'DD/MM/YYYY', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'language'    => [
				'name'         => $this->prefix.'language',
				'label'        => __( 'Language', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'default' 		=> 'en-GB',
				'placeholder' => esc_html__( 'auto', 'gloo_for_elementor' ),
				'description' => esc_html__( 'Language code for day and month names will try to fetch automatically the website\'s language. The auto option will try to detect user browser language.', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'disabledates'  => [
				'name'         => $this->prefix.'disabledates',
				'label'        => __( 'Disable Dates', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( "Date format with dates separated with commas", 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'firstday'    => [
				'name'         => $this->prefix.'firstday',
				'label'        => __( 'FirstDay', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 7,
				'step' => 1,
				'default' => 1,
				'description' => esc_html__( 'ISO day of the week (1: Monday, ..., 7: Sunday).', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'footer'    => [
				'name'         => $this->prefix.'footer',
				'label'        => __( 'Footer', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'inline'    => [
				'name'         => $this->prefix.'inline',
				'label'        => __( 'Disable Popup', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'autoclose'    => [
				'name'         => $this->prefix.'autoclose',
				'label'        => __( 'Auto Close', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Close calendar when date/range are picked', 'gloo_for_elementor' ),
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'hideonbodyclick'    => [
				'name'         => $this->prefix.'hideonbodyclick',
				'label'        => __( 'Hide On Body Click', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'dropdowns'    => [
				'name'         => $this->prefix.'dropdowns',
				'label'        => __( 'Dropdowns', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Dropdown selection for years, months. Leave as false to disable both dropdowns', 'gloo_for_elementor' ),
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'months'  => [
				'name'         => $this->prefix.'months',
				'label'        => __( 'Months', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Enable month dropdown', 'gloo_for_elementor' ),
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'dropdowns' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'years'    => [
				'name'         => $this->prefix.'years',
				'label'        => __( 'Years', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Enable years dropdown', 'gloo_for_elementor' ),
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'dropdowns' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'min_year'    => [
				'name'         => $this->prefix.'min_year',
				'label'        => __( 'Min. Year', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '1900', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'dropdowns' => 'yes',
					$this->prefix.'years' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'locale'    => [
				'name'         => $this->prefix.'locale',
				'label'        => __( 'Lable', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'prev'    => [
				'name'         => $this->prefix.'prev',
				'label'        => __( 'Prev Label', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '←', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'locale' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'next'    => [
				'name'         => $this->prefix.'next',
				'label'        => __( 'Next Label', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '→', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'locale' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'close'    => [
				'name'         => $this->prefix.'close',
				'label'        => __( 'Close Label', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( '×', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'locale' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'reset'    => [
				'name'         => $this->prefix.'reset',
				'label'        => __( 'Reset Label', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Reset', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'locale' => 'yes'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'apply'    => [
				'name'         => $this->prefix.'apply',
				'label'        => __( 'Apply', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Apply', 'gloo_for_elementor' ),
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'locale' => 'yes'
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
			$this->prefix.'datepicker',
			[
				'label' => __( 'Date Picker Fields', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_checkbox_heading',
			[
				'label'     => __( 'Datepicker', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$element->add_control(
			$this->prefix.'background_color',
			[
				'label'     => __( 'Background Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.lightpick__months .lightpick__month' => 'background-color: {{VALUE}};',
					'.lightpick__months .lightpick' => 'background-color: {{VALUE}};',
					'.lightpick__inner .lightpick__footer' => 'background-color: {{VALUE}};'
				],
			]
		);
		$element->add_control(
			$this->prefix.'days_color',
			[
				'label'     => __( 'Days Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.lightpick__day' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->prefix.'week_color',
			[
				'label'     => __( 'Week Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.lightpick__day-of-the-week' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->prefix.'is-today',
			[
				'label'     => __( 'Today Date Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.lightpick__days .lightpick__day.is-today' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->prefix.'is-today-bg-color',
			[
				'label'     => __( 'Date Background Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#DDD',
				'selectors' => [
					'.lightpick__days .is-today' => 'background-color: {{VALUE}};',
					'.lightpick__days .lightpick__day.is-start-date' => 'background-color: {{VALUE}};',
					'.lightpick__days .lightpick__day.is-end-date' => 'background-color: {{VALUE}};',
					'.lightpick__days .lightpick__day.is-start-date:hover' => 'background-color: {{VALUE}};',
					'.lightpick__days .lightpick__day.is-end-date:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
 
		$element->add_control(
			$this->prefix.'is-labels-color',
			[
				'label'     => __( 'Month/Year Label Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'.lightpick__month-title > .lightpick__select-months' => 'color: {{VALUE}};',
					'.lightpick__month-title > .lightpick__select-years' => 'color: {{VALUE}};'
				],
			]
		);
		
		$element->add_control(
			$this->prefix.'is-labels-color',
			[
				'label'     => __( 'Month/Year Label Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'.lightpick__month-title > .lightpick__select-months' => 'color: {{VALUE}};',
					'.lightpick__month-title > .lightpick__select-years' => 'color: {{VALUE}};'
				],
			]
		);
		$element->add_control(
			$this->prefix.'footer-btn-bg-color',
			[
				'label'     => __( 'Footer Button Background Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#DDD',
 				'selectors' => [
					'.lightpick__footer .lightpick__reset-action' => 'background-color: {{VALUE}};',
					'.lightpick__footer .lightpick__apply-action' => 'background-color: {{VALUE}};'
				],
			]
		);
		$element->add_control(
			$this->prefix.'footer-btn-color',
			[
				'label'     => __( 'Footer Button Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
 				'selectors' => [
					'.lightpick__footer .lightpick__reset-action' => 'color: {{VALUE}};',
					'.lightpick__footer .lightpick__apply-action' => 'color: {{VALUE}};'
				],
			]
		);
		$element->add_control(
			$this->prefix.'tools-bg-color',
			[
				'label'     => __( 'Tools Background Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#DDD',
				'description' => esc_html__( 'Prev,Next,Close Buttons', 'gloo_for_elementor' ),
				'selectors' => [
					'.lightpick__previous-action, .lightpick__next-action, .lightpick__close-action' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->prefix.'tools-color',
			[
				'label'     => __( 'Tools Text Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'.lightpick__previous-action, .lightpick__next-action, .lightpick__close-action' => 'color: {{VALUE}};',
				],
			]
		);
		
		$element->add_control(
			$this->prefix.'z_index',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Z-index', 'gloo_for_elementor' ),
				'min' => 0,
				'step' => 1,
				'default' => 1,
				'selectors' => [
					'.lightpick' => 'z-index: {{VALUE}} !important;',
				],
			]
		);
		$element->end_controls_section();
		
		// $element->add_group_control(
		// 	\Elementor\Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'gloo_term_fields_style_checkbox_text',
		// 		'label'    => __( 'Typography', 'gloo' ),
		// 		'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
		// 		'selector' => '{{WRAPPER}} .elementor-field-option',
		// 	]
		// );

		// $element->add_control(
		// 	'gloo_term_fields_style_checkbox_child_color',
		// 	[
		// 		'label'     => __( 'Child Terms Color', 'gloo' ),
		// 		'type'      => \Elementor\Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .elementor-field-option.gloo-child-term label' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );
		// $element->add_group_control(
		// 	\Elementor\Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'gloo_term_fields_style_checkbox_child_text',
		// 		'label'    => __( 'Child Terms Typography', 'gloo' ),
		// 		'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
		// 		'selector' => '{{WRAPPER}} .elementor-field-option.gloo-child-term',
		// 	]
		// );

		// $element->add_control(
		// 	'gloo_term_fields_style_indent',
		// 	[
		// 		'label'   => __( 'Indent Child Terms', 'gloo' ),
		// 		'type'    => \Elementor\Controls_Manager::SWITCHER,
		// 		'default' => '',

		// 	]
		// );

		// $element->add_responsive_control(
		// 	'gloo_term_fields_style_indent_width',
		// 	[
		// 		'label'      => __( 'Indent Amount', 'gloo' ),
		// 		'type'       => \Elementor\Controls_Manager::SLIDER,
		// 		'size_units' => [ 'px' ],
		// 		'default'    => [
		// 			'unit' => 'px',
		// 			'size' => 20,
		// 		],
		// 		'range'      => [
		// 			'px' => [
		// 				'min'  => 1,
		// 				'max'  => 500,
		// 				'step' => 1,
		// 			],
		// 		],
		// 		'condition'  => [
		// 			'gloo_term_fields_style_indent' => 'yes'
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-term'                   => 'margin: 0 {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-term.gloo-term-depth-3' => 'margin: 0 calc({{SIZE}}{{UNIT}} * 2);',
		// 		],
		// 	]
		// );


		$element->end_controls_section();
	}
 
	public function get_datepicker_settings($item, $item_index, $form) {
		$settings = $form->get_settings_for_display( 'form_fields' );
		$setting_array = array();

		/* global settings */
		$setting_array['dropdowns'] = false;
		$setting_array['autoclose'] = false;
		$setting_array['months'] = false;	
		$setting_array['hideOnBodyClick'] = false;

		if($settings[ $item_index ][$this->prefix.'format'] && !empty($settings[ $item_index ][$this->prefix.'format'])) {
			$setting_array['format'] = $settings[ $item_index ][$this->prefix.'format'];
		}

		if($settings[ $item_index ][$this->prefix.'language'] && !empty($settings[ $item_index ][$this->prefix.'language'])) {
			$setting_array['lang'] = $settings[ $item_index ][$this->prefix.'language'];
		}

		if($settings[ $item_index ][$this->prefix.'disabledates'] && !empty($settings[ $item_index ][$this->prefix.'disabledates'])) {
			$disable_dates = $settings[ $item_index ][$this->prefix.'disabledates'];

			if(!empty($disable_dates)) {
				$dates = explode(',', $disable_dates);
				$setting_array['disableDates'] = $dates;
			}
		}
		
		if($settings[ $item_index ][$this->prefix.'firstday'] && !empty($settings[ $item_index ][$this->prefix.'firstday'])) {
			$setting_array['firstDay'] = $settings[ $item_index ][$this->prefix.'firstday'];
		} 
		
		if($settings[ $item_index ][$this->prefix.'footer'] && $settings[ $item_index ][$this->prefix.'footer'] == 'yes') {

			if($settings[ $item_index ]['gloo_datepicker_inline'] != 'yes') {
				$setting_array['footer'] = true;
			}
		} 

		if($settings[ $item_index ][$this->prefix.'inline'] && $settings[ $item_index ][$this->prefix.'inline'] == 'yes') {
			$setting_array['inline'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'autoclose'] == 'yes') {
			$setting_array['autoclose'] = true;
		} 

		if( $settings[ $item_index ][$this->prefix.'hideonbodyclick'] == 'yes') {
			$setting_array['hideOnBodyClick'] = true;
		} 

		if($settings[ $item_index ][$this->prefix.'dropdowns'] == 'yes') {
			$months = $settings[ $item_index ][$this->prefix.'months'];

			if( $months == 'yes') {
				$setting_array['dropdowns']['months'] = true;
			}

			$years = $settings[ $item_index ][$this->prefix.'years'];
			
			if( $years == 'yes') {
				$setting_array['dropdowns']['years'] = [
					'min' => (isset($settings[ $item_index ][$this->prefix.'min_year'])) ? $settings[ $item_index ][$this->prefix.'min_year'] : 1990,
					'max' => null
				];
			}
		} 

		if($settings[ $item_index ][$this->prefix.'locale'] == 'yes') { 

			if(!empty($settings[ $item_index ][$this->prefix.'prev'])) {
				$setting_array['locale']['buttons']['prev'] = $settings[ $item_index ][$this->prefix.'prev'];	
			}

			if(!empty($settings[ $item_index ][$this->prefix.'next'])) {
				$setting_array['locale']['buttons']['next'] = $settings[ $item_index ][$this->prefix.'next'];	
			}

			if(!empty($settings[ $item_index ][$this->prefix.'close'])) {
				$setting_array['locale']['buttons']['close'] = $settings[ $item_index ][$this->prefix.'close'];	
			}

			if(!empty($settings[ $item_index ][$this->prefix.'reset'])) {
				$setting_array['locale']['buttons']['reset'] = $settings[ $item_index ][$this->prefix.'reset'];	
			}

			if(!empty($settings[ $item_index ][$this->prefix.'apply'])) {
				$setting_array['locale']['buttons']['apply'] = $settings[ $item_index ][$this->prefix.'apply'];	
			}
		}
						
		return $setting_array;
	}

	/*	
	* @param Form $form
	*/
   public function render( $item, $item_index, $form ) {

		$settings = $form->get_settings_for_display( 'form_fields' );
		$field_settings = $this->get_datepicker_settings($item, $item_index, $form);
		
		$form->add_render_attribute( 'input' . $item_index, 'data-config', wp_json_encode( $field_settings ) );
		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual' );
		echo '<input size="1" ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';
   }
}
