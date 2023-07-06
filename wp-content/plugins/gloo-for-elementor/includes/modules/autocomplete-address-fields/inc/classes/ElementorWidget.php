<?php
namespace ByteBunch\FluidDynamics;

class ElementorWidget extends \Elementor\Widget_Base {

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'gloo_for_elementor' ),
			]
		);

		$this->add_control(
			'google_autocomplete',
			[
				'label' => __( 'Google Autocomplete', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Google Autocomplete', 'gloo_for_elementor' ),
			]
		);

		$this->end_controls_section();

	}

}