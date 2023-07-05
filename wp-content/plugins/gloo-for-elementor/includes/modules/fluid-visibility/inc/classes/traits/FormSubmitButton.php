<?php
namespace Gloo\Modules\Fluid_Visibility\Traits;
if ( ! defined( 'ABSPATH' ) )	exit;

trait FormSubmitButton {


	/******************************************/
  /***** initialize_form_submit_button function**********/
  /******************************************/
  public function initialize_form_submit_button(){

    add_action( 'elementor/element/form/section_buttons/before_section_end', [ $this, 'forms_section_buttons_before_section_end' ], 10, 2 );

  }// get_option


  public function forms_section_buttons_before_section_end( $element, $section_id ) {

    $element->start_injection( [
			'type' => 'control',
			'at' => 'after',
			'of' => 'button_css_id',
		] );
		// $element->start_injection( [
    //   'type' => 'section',
    //   'at' => 'end',
    //   'of' => $section_id,
    // ] );

    $element->add_control(
			$this->prefix . 'button_status',
			array(
				'type'           => \Elementor\Controls_Manager::SWITCHER,
				'label'          => __( 'Fluid Logic', 'gloo-for-elementor' ),
				'render_type'    => 'template',
				'prefix_class'   => 'jedv-enabled--',
				'style_transfer' => false,
			)
		);

		$element->add_control(
			$this->prefix . 'button_condition',
			array(
				'label'     => __( 'Condition Chain', 'gloo-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [],
				'condition' => [ $this->prefix . 'button_status' => 'yes' ],
			)
		);

		$element->add_control(
			$this->prefix . 'button_action',
			array(
				'label'     => __( 'Action', 'gloo-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'show' => 'Show',
					'hide' => 'Hide'
				],
				'condition' => [ $this->prefix . 'button_status' => 'yes' ],
			)
		);

    $element->end_injection();

	}

}