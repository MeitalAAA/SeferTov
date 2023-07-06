<?php

namespace Gloo\Modules\ComposerField;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class Field_Composer extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
	public $depended_scripts = [
		'gloo_composer_field_script',
	];

  public function get_type() {
      return 'gloo_composer_field';
  }

  public function get_name() {
      return __( 'Calculation Field', 'gloo_for_elementor' );
  }

  public function prefix() {
    return 'gloo_composer_field_';
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
		$context = 'content';

    $tmp = new \Elementor\Repeater();
	

    $tmp->add_control(
      $this->prefix().'is_hidden_field',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Is hidden field?', 'gloo_for_elementor' ),
				'condition' => ['field_type' => $this->get_type()],
        'default' => 'no',

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
      ]
    );
    $tmp->add_control(
			$this->prefix().'default_value',
			[
				'label' => __( 'Default Value', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => ['field_type' => $this->get_type()],
        'default' => '0',
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
    $tmp->add_control(
			$this->prefix().'container',
			[
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
						['name' => $this->prefix().'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
			$this->prefix().'before_text',
			[
				'label' => __( 'Before Text', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
			$this->prefix().'after_text',
			[
				'label' => __( 'After Text', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
      $this->prefix().'thousand_seperator_switch',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Thousand Seperator', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						// ['name' => $this->prefix().'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
      ]
    );

		$tmp->add_control(
			$this->prefix().'thousand_seperator_value',
			[
				'label' => __( 'Thousand Seperator Text', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => ',',
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'thousand_seperator_switch', 'operator' => '===', 'value' => 'yes'],
						// ['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
      $this->prefix().'decimal',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Decimal', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						// ['name' => $this->prefix().'is_hidden_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
      ]
    );

		$tmp->add_control(
			$this->prefix().'decimal_amount',
			[
				'label' => __( 'Decimal Amount', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '2',
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'decimal', 'operator' => '===', 'value' => 'yes'],
						// ['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
			$this->prefix().'decimal_seperator',
			[
				'label' => __( 'Decimal Seperator', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.',
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'decimal', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		

    $tmp->add_control(
      $this->prefix().'is_repeater_field',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Is repeater field?', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
      ]
    );

    $tmp->add_control(
			$this->prefix().'repeater_id',
			[
				'label' => __( 'Repeater ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
    $tmp->add_control(
			$this->prefix().'repeater_sub_field_id',
			[
				'label' => __( 'Repeater Subfield ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
    $tmp->add_control(
			$this->prefix().'repeater_operation',
			[
				'label' => __( 'Operation', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
          '' => __( 'None' ),
					'add'  => __( 'Addition (+)', 'gloo_for_elementor' ),
					'sub'  => __( 'Subtraction (-)', 'gloo_for_elementor' ),
					'multiply'  => __( 'Multiplication (*)', 'gloo_for_elementor' ),
					'division'  => __( 'Division (/)', 'gloo_for_elementor' ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
    $tmp->add_control(
			$this->prefix().'repeater_base_value',
			[
				'label' => __( 'Base Value', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
      $this->prefix().'is_percentage',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Is Percentage?', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				// 'condition' => ['return_type' => 'calculations']
      ]
    );
    $tmp->add_control(
      $this->prefix().'is_inverse',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Is inverse?', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '===', 'value' => 'yes'],
						['name' => $this->prefix().'is_percentage', 'operator' => '===', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				// 'condition' => ['return_type' => 'calculations']
      ]
    );

		$tmp->add_control(
			$this->prefix().'composer',
			[
				'label' => __( 'Composer', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'label_block' => true,
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '!=', 'value' => 'yes'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				// 'classes' => $this->prefix().'composer',
				/*'dynamic' => [
					'active' => true,
				],*/
			]
		);

		$tmp->add_control(
			$this->prefix().'description',
			[
				//'label' => __( 'Description', 'gloo' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 
					'Here are some basic usage examples: <br />
					fieldID1 + fieldID2 <br />
					fieldID1 - fieldID2 <br />
					fieldID1 * fieldID2 <br />
					fieldID1 / fieldID2 <br />
					', 'gloo_for_elementor' 
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()],
						['name' => $this->prefix().'is_repeater_field', 'operator' => '!=', 'value' => 'yes'],
						// ['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
						// ['name' => 'composer_math_decimal_amount', 'operator' => '>=', 'value' => '1'],
					],
				],

				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				// 'condition' => ['return_type' => 'calculations']
			]
		);
		
    $control_data['fields'] = $this->inject_controls($tmp->get_controls(), $control_data['fields']);

		$widget->update_control( 'form_fields', $control_data );
	}

    /**
     * @param      $item
     * @param      $item_index
     * @param Form $form
     */
    public function render( $item, $item_index, $form ) {

			$field_data = array();

			$field_data['is_hidden_field'] = 'no';
			if(isset($item[$this->prefix().'is_hidden_field']) && $item[$this->prefix().'is_hidden_field'] == 'yes')
				$field_data['is_hidden_field'] = $item[$this->prefix().'is_hidden_field'];


			$field_data['default_value'] = '';
			if(isset($item[$this->prefix().'default_value']))
				$field_data['default_value'] = $item[$this->prefix().'default_value'];

			$field_data['composer'] = '';
			if(isset($item[$this->prefix().'composer']))
				$field_data['composer'] = $item[$this->prefix().'composer'];

				
			$field_data['thousand_seperator_switch'] = '';
			if(isset($item[$this->prefix().'thousand_seperator_switch']) && $item[$this->prefix().'thousand_seperator_switch'] == 'yes')
				$field_data['thousand_seperator_switch'] = $item[$this->prefix().'thousand_seperator_switch'];

			$field_data['thousand_seperator_value'] = '';
			if(isset($item[$this->prefix().'thousand_seperator_value']))
				$field_data['thousand_seperator_value'] = $item[$this->prefix().'thousand_seperator_value'];
				

				
			$field_data['decimal'] = '';
			if(isset($item[$this->prefix().'decimal']) && $item[$this->prefix().'decimal'] == 'yes')
				$field_data['decimal'] = $item[$this->prefix().'decimal'];


			$field_data['decimal_amount'] = '';
			if(isset($item[$this->prefix().'decimal_amount']))
				$field_data['decimal_amount'] = $item[$this->prefix().'decimal_amount'];

			$field_data['decimal_seperator'] = '';
			if(isset($item[$this->prefix().'decimal_seperator']))
				$field_data['decimal_seperator'] = $item[$this->prefix().'decimal_seperator'];

			$input_class = 'gloo_composer_field_input_formula';
			$field_data['is_repeater_field'] = '';
			if(isset($item[$this->prefix().'is_repeater_field'])){
				if($item[$this->prefix().'is_repeater_field'] == 'yes')
					$input_class = 'gloo_composer_field_input_repeater';
				$field_data['is_repeater_field'] = $item[$this->prefix().'is_repeater_field'];
			}
			
			$field_data['repeater_id'] = '';
			if(isset($item[$this->prefix().'repeater_id']))
				$field_data['repeater_id'] = $item[$this->prefix().'repeater_id'];
			
			$field_data['repeater_sub_field_id'] = '';
			if(isset($item[$this->prefix().'repeater_sub_field_id']))
				$field_data['repeater_sub_field_id'] = $item[$this->prefix().'repeater_sub_field_id'];
				
			$field_data['repeater_operation'] = '';
			if(isset($item[$this->prefix().'repeater_operation']))
				$field_data['repeater_operation'] = $item[$this->prefix().'repeater_operation'];

			$field_data['repeater_base_value'] = '';
			if(isset($item[$this->prefix().'repeater_base_value']))
				$field_data['repeater_base_value'] = $item[$this->prefix().'repeater_base_value'];


			$field_data['is_percentage'] = '';
			if(isset($item[$this->prefix().'is_percentage']) && $item[$this->prefix().'is_percentage'] == 'yes')
				$field_data['is_percentage'] = $item[$this->prefix().'is_percentage'];

			$field_data['is_inverse'] = '';
			if(isset($item[$this->prefix().'is_inverse']) && $item[$this->prefix().'is_inverse'] == 'yes')
				$field_data['is_inverse'] = $item[$this->prefix().'is_inverse'];

			
				
				
				
			$form->add_render_attribute( 'input' . $item_index, 'data-composer-field', json_encode($field_data) );
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo_composer_field_input' );
			$form->add_render_attribute( 'input' . $item_index, 'class', $input_class );

			$html_output = '<input type="hidden" '.$form->get_render_attribute_string( 'input' . $item_index ).' value="'.$field_data['default_value'].'" />';
			if(isset($item[$this->prefix().'is_hidden_field']) && $item[$this->prefix().'is_hidden_field'] != 'yes'){
				if(!empty($item[$this->prefix().'container']))
					$html_output .= '<'.$item[$this->prefix().'container'].' class="calculated_field_value">';

				if(!empty($item[$this->prefix().'before_text']))
					$html_output .= '<span class="calculation_field_before_text">'.$item[$this->prefix().'before_text'].'</span>';
					
				$html_output .= '<span class="calculation_field_result">'.$field_data['default_value'].'</span>';

				if(!empty($item[$this->prefix().'after_text']))
					$html_output .= '<span class="calculation_field_after_text">'.$item[$this->prefix().'after_text'].'</span>';

				if(!empty($item[$this->prefix().'container']))
					$html_output .= '</'.$item[$this->prefix().'container'].'>';
			}
			echo $html_output;

			
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }
 
 
 
    public function sanitize_field( $value, $field ) {
        return wp_kses_post( $field['raw_value'] );
    }
 
    public function __construct() {
        parent::__construct();

		add_action( 'elementor/element/form/section_form_style/after_section_end', [
			$this,
			'add_style_control_section_to_form'
		], 10, 2 );

        add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );

				$script_abs_path = gloo()->plugin_path( 'includes/modules/composer-field/assets/frontend/js/script.js');
				wp_register_script( $this->prefix().'script',  gloo()->plugin_url( 'includes/modules/composer-field/assets/frontend/js/script.js'), array('jquery'), get_file_time($script_abs_path));
    }

    public function add_form_field_switcher($name, $label, $context = 'content', $default = 'yes'){
        $output = [
            'name'         => $name,
            'label'        => $label,
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'Yes', 'gloo' ),
            'label_off' => __( 'No', 'gloo' ),
            'return_value' => 'yes',
            'default' => $default,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => $context,
            'inner_tab'    => 'form_fields_'.$context.'_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        
        return $output;
    }

    public function add_form_field_divider($context = 'content', $name = ''){
        $output = [
            // 'name'         => $this->get_type().'_separator_'.$random_value,
            'type'         => \Elementor\Controls_Manager::DIVIDER,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => $context,
            'inner_tab'    => 'form_fields_'.$context.'_tab',
            // 'tab'          => 'content',
            // 'inner_tab'    => 'form_fields_content_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        if($name)
            $output['name'] = $name;
        
        return $output;
    }

	public function add_style_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			'gloo_composer_fields_style',
			[
				'label' => __( 'Calculation Field', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'gloo_composer_field_typography',
				'label' => __( 'Typography', 'gloo_for_elementor' ),
				//'scheme' => \Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .calculated_field_value',
				//'condition' => ['return_type' => 'calculations'],
				// 'condition' => ['field_type' => $this->get_type()],
	
				// 'tab'          => $context,
				// 'inner_tab'    => 'form_fields_'.$context.'_tab',
				// 'tabs_wrapper' => 'form_fields_tabs',
			]
		);

        $element->end_controls_section();
    }


  public function inject_controls($repeater_controls, $existing_controls = array()){
		
    if($repeater_controls && is_array($repeater_controls) && count($repeater_controls) >= 1){
      foreach($repeater_controls as $key=>$control){
        $existing_controls[$key] = $control;
      }
    }
    return $existing_controls;
	}
}
