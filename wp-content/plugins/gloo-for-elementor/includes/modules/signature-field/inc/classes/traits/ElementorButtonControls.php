<?php
namespace Gloo\Modules\SignatureField\Traits;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait ElementorButtonControls{

  public function add_button_style_controls($element, $prefix){
    $prefix .= '_';

    $element->add_control(
			$prefix.'heading_button',
			[
				'label' => __( 'Button style', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);

    $element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => $prefix. 'button_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '.gloo-signature-button-clear',
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(), [
				'name' => $prefix. 'button_border',
				'selector' => '.gloo-signature-button-clear',
				'exclude' => [
					'color',
				],
			]
		);


    $element->start_controls_tabs( $prefix.'tabs_button_style' );
      $element->start_controls_tab(
        $prefix.'tab_button_normal',
        [
          'label' => __( 'Normal', 'gloo_for_elementor' ),
        ]
    );
		
		$element->add_control(
			$prefix .'button_background_color',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo-signature-button-clear' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$prefix .'button_text_color',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo-signature-button-clear' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$prefix .'button_border_color',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo-signature-button-clear' => 'border-color: {{VALUE}};',
				],
				// 'condition' => [
				// 	$prefix. 'button_border!' => '',
				// ],
			]
		);
		$element->end_controls_tab();
		$element->start_controls_tab(
			$prefix.'tab_button_hover',
			[
				'label' => __( 'Hover', 'gloo_for_elementor' ),
			]
		);
		$element->add_control(
			$prefix .'button_background_color_hover',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo-signature-button-clear:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$prefix .'button_text_color_hover',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo-signature-button-clear:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$prefix .'button_border_color_hover',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo-signature-button-clear:hover' => 'border-color: {{VALUE}};',
				],
				// 'condition' => [
				// 	$prefix. 'button_border!' => '',
				// ],
			]
		);
		$element->end_controls_tab();
		$element->end_controls_tabs();
  }
}