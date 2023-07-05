<?php

namespace Gloo\Modules\Checkbox_Radio_Field_Control;

class Module {

	private $prefix = 'gloo_crf_';
	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'checkbox_radio_field_control';

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
 
 		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'render_checkbox_radio_field'
		], 11 );

		add_action( 'elementor_pro/forms/render/item/checkbox', [ $this, 'render_field' ], 10, 3 );
		add_action( 'elementor_pro/forms/render/item/radio', [ $this, 'render_field' ], 10, 3 );

		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this,'add_control_section_to_form'], 10, 2 );
 	}
	
	public function render_field( $item, $item_index, $widget ) {

		$settings = $widget->get_settings_for_display( 'form_fields' );
		$field_type = $item['field_type'];
		
		if(isset($settings[ $item_index ][$this->prefix.'gloo_field_column_layout']) && $settings[ $item_index ][$this->prefix.'gloo_field_column_layout'] == 'yes' ) {
			$widget->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-column-'.$field_type );
		}
		return $item;
	}

	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'heading',
			[
				'label' => __( 'Checkbox & Radio Field Control', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_responsive_control(
			$this->prefix.'no_of_columns',
			[
				'label' => esc_html__( 'No. Of Column', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'step' => 1,
				'default' => 2,
				'selectors'  => [
					'{{WRAPPER}} .elementor-field-type-radio.gloo-column-radio .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option' => 'flex: 0 0 calc(100% / {{VALUE}} - {{gloo_crf_horizontal_gap.SIZE}}{{gloo_crf_horizontal_gap.UNIT}}); padding-inline: 8px; border: 1px solid #EEEEEE;',
					'{{WRAPPER}} .elementor-field-type-checkbox.gloo-column-checkbox .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option' => 'flex: 0 0 calc(100% / {{VALUE}} - {{gloo_crf_horizontal_gap.SIZE}}{{gloo_crf_horizontal_gap.UNIT}}); padding-inline: 8px; border: 1px solid #EEEEEE;',
				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'vetical_gap',
			[
				'label' => esc_html__( 'Vertical Gap', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .gloo-column-radio .elementor-field-subgroup' => 'row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-column-checkbox .elementor-field-subgroup' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'horizontal_gap',
			[
				'label' => esc_html__( 'Horizontal Gap', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .gloo-column-radio .elementor-field-subgroup' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-column-checkbox .elementor-field-subgroup' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
 
		$element->end_controls_section();

	}

	public function render_checkbox_radio_field( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			$this->prefix.'gloo_field_column_layout'       => [
				'name'         => $this->prefix.'gloo_field_column_layout',
				'label'        => __( 'Enable Custom Columns', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'conditions' => [
					'terms' => [
					[
						'name' => 'field_type',
						'operator' => 'in',
						'value' => array('checkbox', 'radio'),
					],
					],
				],
			],
 		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}
	
	public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'width', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
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