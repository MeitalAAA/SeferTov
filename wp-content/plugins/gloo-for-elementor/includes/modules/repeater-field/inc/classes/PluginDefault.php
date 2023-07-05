<?php
namespace Gloo\RepeaterField;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	public $is_repeater = false;
	public $repeater_index = 0;
	
	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    
		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

		add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );

		add_action( 'elementor/element/form/section_form_style/after_section_end', [
			$this,
			'add_control_section_to_form'
		], 10, 2 );

		add_action('wp_footer', [$this, 'wp_footer']);
  }// construct function end here


	
	/******************************************/
  /***** get_prefix. **********/
  /******************************************/
  public function get_prefix() {
		return $this->prefix();
	}

	  /******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
		// new \Gloo\Modules\CheckoutAnything\Field_Wysiwyg();
		// new RepeaterField();
		new RepeaterStartField();
		new RepeaterEndField();  
    

		add_filter( 'elementor_pro/forms/render/item', [ $this, 'elementor_pro_forms_render_item' ], 10, 3 );
		add_action('elementor_pro/forms/render_field/gloo_repeater_end_field', [ $this, 'render_field_gloo_repeater_end_field' ], 10, 3);
  }

	public function render_field_gloo_repeater_end_field($item, $item_index, $element){
		echo '<button> '.$item['field_new_item_label'] .'</button>';
	}

	public function elementor_pro_forms_render_item( $item, $item_index, $element ) {
		// $submit_actions = $element->get_settings_for_display( 'submit_actions' );
		// if ( ! $submit_actions || ! in_array( "checkout_anything", $submit_actions ) ) {
		// 	return $item;
		// }
		
		$field_type = $item['field_type'];
		$field_input_type = $field_type;

		if ( in_array($field_type, array('text', 'email', 'url', 'password', 'hidden', 'search', 'radio', 'checkbox'))){
			$input_type = 'input';
		}
		else if ( $field_type === 'select' ){
			$input_type = 'select';
		}
		else if ( in_array($field_type, array('textarea', 'gloo_wysiwyg'))){
			$input_type = 'textarea';
		}
		else{
			$input_type = 'input';
		}

		if($field_type == 'gloo_cpt_field' && isset($item['gloo_cpt_fields_output'])){
			$field_input_type = $item['gloo_cpt_fields_output'];
			if($item['gloo_cpt_fields_output'] == 'select'){
				$input_type = $item['gloo_cpt_fields_output'];
			}
		}

		if($field_type == 'gloo_terms_field' && isset($item['gloo_term_fields_output'])){
			$field_input_type = $item['gloo_term_fields_output'];
			if($item['gloo_term_fields_output'] == 'select'){
				$input_type = $item['gloo_term_fields_output'];
			}
		}
			
		


		// db(get_class_methods($element));exit();
		

		if($field_type == 'gloo_repeater_start_field'){
			$this->is_repeater = true;
			$element->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo_repeater_start_field');
			$element->add_render_attribute( 'field-group' . $item_index, 'data-field-id', $item['custom_id'] );
		}

		

		
		if($field_type == 'gloo_repeater_end_field'){
			$this->repeater_index++;
			$item['field_new_item_label'] = $item['field_label'];
			$item['field_label'] = '';
			$this->is_repeater = false;
			$element->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo_repeater_end_field');
		}
		
		

		if($this->is_repeater && $field_type != 'gloo_repeater_start_field'){
			// db($element);
			$element->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo_repeater_field gloo_repeater_field_'.$this->repeater_index);			
			$element->add_render_attribute( 'field-group' . $item_index, 'data-field-id', $item['custom_id'] );
			// $name = $element->get_render_attributes($input_type . $item_index, 'name');
			// $id = $element->get_render_attributes($input_type . $item_index, 'id');

			// $element->add_render_attribute( $input_type . $item_index, 'data-custom-id', $item['custom_id'] );
			
			// $element->add_render_attribute( 'field-group' . $item_index, 'data-field-name', $name );
			$element->add_render_attribute( 'field-group' . $item_index, 'data-field-input', $input_type );
			$element->add_render_attribute( 'field-group' . $item_index, 'data-field-type', $field_type );
			$element->add_render_attribute( 'field-group' . $item_index, 'data-field-input-type', $field_input_type );
			
			// $element->add_render_attribute( $input_type . $item_index, 'data-id', $id);
			// $element->remove_render_attribute( $input_type . $item_index, 'id', true);
			// $element->remove_render_attribute( 'field-subgroup' . $item_index, 'id');

			
			// db($item);
		}
		return $item;
		

	}
	
	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			'gloo_repeater_fields_style',
			[
				'label' => __( 'Repeater Field', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
      $this->prefix.'_list_style_type', 
      [
        'name'         => $this->prefix.'_list_style_type',
        'label'        => __('List Style Type', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
					'disc' => 'Disc',
					'decimal' => 'Decimal',
					// 'georgian' => 'georgian',
					// 'space-counter' => 'space-counter',
					'circle' => 'Circle',
					'square' => 'Square',
					'upper-roman' => 'Upper Roman',
					'lower-alpha' => 'Lower Alpha',
				],
				// 'default' => 'disc',
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item' => 'list-style-type: {{VALUE}};',
        ],
      ]
    );


		$element->add_control(
      $this->prefix.'_close_button_color', 
      [
        'name'         => $this->prefix.'_close_button_color',
        'label'        => __('Close Button Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove' => 'color: {{VALUE}};',
        ],
      ]
    );

    $element->add_control(
      $this->prefix.'_close_button_hover_color', 
      [
        'name'         => $this->prefix.'_close_button_hover_color',
        'label'        => __('Close Button Hover Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

		$element->add_control(
      $this->prefix.'_close_button_hover_bg_color', 
      [
        'name'         => $this->prefix.'_close_button_hover_bg_color',
        'label'        => __('Close Button Hover Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

		$element->add_control(
      $this->prefix.'_repeater_wrapper_bg_color', 
      [
        'name'         => $this->prefix.'_repeater_wrapper_bg_color',
        'label'        => __('Wrapper Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item' => 'background-color: {{VALUE}};',
        ],
      ]
    );
		
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'         => $this->prefix.'_repeater_wrapper_border',
				'label' => esc_html__( 'Border', 'plugin-name' ),
				'selector' => '.gloo_repeater_field_wrapper li.gloo_repeater_li_item',
			]
		);
		


		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'         => $this->prefix.'_repeater_wrapper_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'plugin-name' ),
				'selector' => '.gloo_repeater_field_wrapper li.gloo_repeater_li_item',
			]
		);




		// Button controls for every site
		$element->add_control(
			$this->get_prefix() .'new_item_button',
			[
				'label' => __( 'New Item Button style', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => $this->get_prefix() . 'button_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '.gloo_repeater_end_field button',
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(), [
				'name' => $this->get_prefix() . 'button_border',
				'selector' => '.gloo_repeater_end_field button',
				'exclude' => [
					'color',
				],
			]
		);

		$element->start_controls_tabs( $this->get_prefix().'tabs_button_style' );
		$element->start_controls_tab(
			$this->get_prefix().'tab_button_normal',
			[
				'label' => __( 'Normal', 'gloo_for_elementor' ),
			]
		);
		
		$element->add_control(
			$this->get_prefix() .'button_background_color',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo_repeater_end_field button' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_color',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo_repeater_end_field button' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_border_color',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo_repeater_end_field button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_prefix() .'button_border_border!' => '',
				],
			]
		);
		$element->end_controls_tab();
		$element->start_controls_tab(
			$this->get_prefix().'tab_button_hover',
			[
				'label' => __( 'Hover', 'gloo_for_elementor' ),
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_background_color_hover',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo_repeater_end_field button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_color_hover',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo_repeater_end_field button:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_border_color_hover',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo_repeater_end_field button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_prefix() .'button_border_border!' => '',
				],
			]
		);
		$element->end_controls_tab();
		$element->end_controls_tabs();
		$element->add_control(
			$this->get_prefix() .'button_border_radius',
			[
				'label' => __( 'Border Radius', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'.gloo_repeater_end_field button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_padding',
			[
				'label' => __( 'Text Padding', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'.gloo_repeater_end_field button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		// Button style end here



    $element->end_controls_section();
  }

	public function wp_footer(){?>
	<style>
		li.gloo_repeater_li_item {
			position: relative;
			padding: 10px;
			/* box-shadow: 0 0 10px 0 rgba(0,0,0,0.2); */
			margin-bottom: 10px;
			list-style:none;
		}
		li.gloo_repeater_li_item a.remove {
			display: block;
			font-size: 1.5em;
			height: 1em;
			width: 1em;
			text-align: center;
			line-height: 1;
			border-radius: 100%;
			text-decoration: none;
			font-weight: 700;
			border: 0;
			z-index: 99;
			margin-inline-start: auto;
			color: red;
		}
		li.gloo_repeater_li_item a.remove:hover{
			color:#fff;
			background-color:red;
		}
	</style>
	
	<?php }
} // BBWP_CustomFields class


