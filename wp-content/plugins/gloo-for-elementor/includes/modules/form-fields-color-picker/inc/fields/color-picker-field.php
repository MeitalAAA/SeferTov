<?php
namespace Gloo\Modules\Form_Fields_Color_Picker\Fields;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Color_Picker_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {
	
	private $prefix = 'gloo_cpick_';

	public function __construct() {
		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this,'add_control_section_to_form'], 10, 2 );
		parent::__construct();
	}

	public function get_type() {
		return 'color';
	}

	public function get_name() {
		return __( 'Color Picker', 'elementor-pro' );
	}

	public function render( $item, $item_index, $form ) {
		$form->add_render_attribute( 'input' . $item_index, 'class', 'color-picker' );
		echo '<input size="1" ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';
	}
	
	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'fields_style',
			[
				'label' => __( 'Color Picker Fields', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_checkbox_heading',
			[
				'label'     => __( 'Color Picker', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'

			]
		);
 
		$element->add_responsive_control(
			$this->prefix.'width',
			[
				'label'      => __( 'Width', 'gloo' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .color-picker'  => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			$this->prefix.'height',
			[
				'label'      => __( 'Height', 'gloo' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .color-picker' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->end_controls_section();
	}

}