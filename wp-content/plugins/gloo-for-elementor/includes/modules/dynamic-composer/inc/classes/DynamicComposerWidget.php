<?php
namespace OTW\DynamicComposer;

class DynamicComposerWidget extends \Elementor\Widget_Base {

	public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
		
    wp_register_script( 'dynamic-composer-widget', gloo()->plugin_url('includes/modules/').'dynamic-composer/js/dynamic-composer-widget.js', array('jquery', 'elementor-frontend'), '1.1.0', true );
	}
	
	public function get_name() {
		return 'otwdynamiccomposer';
	}

	public function get_title() {
		return __( 'Dynamic Composer', 'gloo_for_elementor' );
	}


	public function get_categories() {
		return [ 'gloo' ];
	}

	public function get_icon() {
		return 'gloo-elements-icon-power';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'Dynamic Composer' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_variable_name', [
				'label' => __( 'Variable Name', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Title' , 'gloo' ),
				'label_block' => true,
				'classes' => "otw_variable_name",
			]
		);

		$repeater->add_control(
			'list_variable_value', [
				'label' => __( 'Variable Value', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Content' , 'gloo' ),
				//'show_label' => false,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		

		$this->add_control(
			'list',
			[
				'label' => __( 'Items', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_variable_name' => __( 'Variable Name', 'gloo_for_elementor' ),
						'list_variable_value' => __( 'Variable Value.', 'gloo_for_elementor' ),
					],
				],
				'title_field' => '{{{ list_variable_name }}}',
			]
		);



		$this->add_control(
			'return_type',
			[
				'label' => __( 'Type', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'html',
				'options' => [
					'html'  => __( 'HTML Markup', 'gloo_for_elementor' ),
					'calculations' => __( 'Calculations', 'gloo_for_elementor' ),
				],
			]
		);

		

		

		$this->add_control(
			'return_type_container',
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
				'condition' => ['return_type' => 'calculations']
			]
		);

		$this->add_control(
			'composer_math_before_text',
			[
				'label' => __( 'Before Text', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => ['return_type' => 'calculations']
			]
		);

		$this->add_control(
			'composer_math_after_text',
			[
				'label' => __( 'After Text', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => ['return_type' => 'calculations']
			]
		);

		$this->add_control(
      'composer_math_thousand_seperator_switch',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Thousand Seperator', 'gloo_for_elementor' ),
				'condition' => ['return_type' => 'calculations']
      ]
    );

		$this->add_control(
			'composer_math_thousand_seperator_value',
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
						['name' => 'composer_math_thousand_seperator_switch', 'operator' => '===', 'value' => 'yes'],
						['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
					],
				],
			]
		);

		$this->add_control(
      'composer_math_decimal',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Decimal', 'gloo_for_elementor' ),
				'condition' => ['return_type' => 'calculations']
      ]
    );

		$this->add_control(
			'composer_math_decimal_amount',
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
						['name' => 'composer_math_decimal', 'operator' => '===', 'value' => 'yes'],
						['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
					],
				],
			]
		);

		$this->add_control(
			'composer_math_decimal_seperator',
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
						['name' => 'composer_math_decimal', 'operator' => '===', 'value' => 'yes'],
						['name' => 'return_type', 'operator' => '==', 'value' => 'calculations'],
						['name' => 'composer_math_decimal_amount', 'operator' => '>=', 'value' => '1'],
					],
				],
			]
		);
		

		$this->add_control(
			'composer',
			[
				'label' => __( 'Composer', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'label_block' => true,
				'classes' => "otw_composer_area",
				/*'dynamic' => [
					'active' => true,
				],*/
			]
		);

		$this->add_control(
			'composer_description',
			[
				//'label' => __( 'Description', 'gloo' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 
					'Here are some basic usage examples: <br />
					variable1 + variable2 <br />
					variable1 - variable2 <br />
					variable1 * variable2 <br />
					variable1 / variable2 <br />
					', 'gloo_for_elementor' 
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => ['return_type' => 'calculations']
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'gloo_for_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'gloo_composer_text_color',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .otw_typegraphy_element' => 'color: {{VALUE}}',
				],
				'condition' => ['return_type' => 'calculations']
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				//'name' => 'math_type_typography',
				'label' => __( 'Typography', 'gloo_for_elementor' ),
				//'scheme' => \Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .otw_typegraphy_element',
				//'condition' => ['return_type' => 'calculations']
			]
		);

		$this->end_controls_section();

	}


	public function get_script_depends() {
		return [ 'dynamic-composer-widget' ];
 	}


	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {


		$settings = $this->get_settings_for_display();

		//$this->get_settings_for_display( 'title' );
		$ouput_string = $settings['composer'];
		$calculated_varialbes_values = array();
		$total_varialbes = 0;
		$total_varialbes_exist = 0;
		//db($settings);
		if($ouput_string){
			if(isset($settings['list']) && is_array($settings['list']) && count($settings['list']) >= 1){
				foreach($settings['list'] as $key=>$value){
					$total_varialbes++;
					//if($value['list_variable_name'] && $value['list_variable_value'])
						$ouput_string = str_replace($value['list_variable_name'], $value['list_variable_value'], $ouput_string);
						$calculated_varialbes_values[$value['list_variable_name']] = $value['list_variable_value'];
						if(!empty($value['list_variable_value']))
							$total_varialbes_exist++;
					}
				
				
				
				//db(eval('return '.$ouput_string.';'));exit();
				if($settings['return_type'] == 'calculations' && preg_match( "/^[0-9,*{}().+-\/\^]/", $ouput_string)){
					
					$calculation_variables = array(
						'composer_math_decimal' => 'no', 
						'composer_math_decimal_amount' => 0,
						'composer_math_decimal_seperator' => '',
						'composer_math_thousand_seperator_switch' => 'no',
						'composer_math_thousand_seperator_value' => '',
					);
					
					if(!empty($settings['composer_math_thousand_seperator_switch']) && $settings['composer_math_thousand_seperator_switch'] == 'yes')
						$calculation_variables['composer_math_thousand_seperator_switch'] = 'yes';

					if(!empty($settings['composer_math_thousand_seperator_value']))
						$calculation_variables['composer_math_thousand_seperator_value'] = $settings['composer_math_thousand_seperator_value'];

					if(!empty($settings['composer_math_decimal']) && $settings['composer_math_decimal'] == 'yes')
						$calculation_variables['composer_math_decimal'] = 'yes';
					
					if(!empty($settings['composer_math_decimal_amount']) && $settings['composer_math_decimal_amount'] >= 1)
						$calculation_variables['composer_math_decimal_amount'] = $settings['composer_math_decimal_amount'];

					if(!empty($settings['composer_math_decimal_seperator']))
						$calculation_variables['composer_math_decimal_seperator'] = $settings['composer_math_decimal_seperator'];
						
					//db($settings['list']);db($ouput_string);exit();
					//$ouput_string = preg_replace('#\{(.+?)\}|[^0-9,*/+-{}()]+|(?<!\d),|,(?!\d)#', '$1', $ouput_string);
					$ouput_string = preg_replace("/[^0-9,*{}().+-\/\^]/", '', $ouput_string);
					if(!is_admin())
						$ouput_string = '<span class="gloo_composer_math_text" id="" data-calculated-text="'.$ouput_string.'" data-calculated-variables-count="'.$total_varialbes.'" data-calculated-variables-exist="'.$total_varialbes_exist.'" data-calculated-variables=\''.json_encode($calculation_variables).'\' data-calculated-variables-values=\''.json_encode($calculated_varialbes_values).'\'>'.'</span>';
					
					if($settings['composer_math_before_text'])
						$ouput_string = '<span class="composer_math_before_text">'.$settings['composer_math_before_text'].'</span>'.$ouput_string;

					if($settings['composer_math_after_text'])
						$ouput_string = $ouput_string.'<span class="composer_math_after_text">'.$settings['composer_math_after_text'].'</span>';
					
						/*try {
							//error_reporting(0);
							//ini_set('display_errors', '0');
							//$ouput_string = @eval('return true; return '.$ouput_string.';');
							//$ouput_string = math_eval($ouput_string);
							$ouput_string = '<script>document.write('.$ouput_string.');</script>';
						} catch (ParseError $e) {
							$ouput_string = "";
						} catch (Error $error) {
						// Output any unexpected errors.
							$ouput_string = "";							
						}catch (Exception $e) {
							$ouput_string = "";							
						}finally {
							//db("fin");exit();
						}*/
					///[^0-9.+-\/]/
					//db($ouput_string);exit();
					
					//$ouput_string = math_eval($ouput_string);
					
					//$executor = new \NXP\MathExecutor();
					//$ouput_string = $executor->execute($ouput_string);
					
					//$evaluator = new \Matex\Evaluator();
					//$ouput_string = $evaluator->execute($ouput_string);
				}
			}
		}


		echo '<div class="otw-dynamic-composer-elementor-widget">';

		//echo wp_kses_post('[elementor-tag id="d489be7" name="jet-options-page"]');
		if($settings['return_type'] == 'calculations'){
			$ouput_string = "<".$settings['return_type_container']." class='otw_typegraphy_element'>".$ouput_string."</".$settings['return_type_container'].">";
		}else{
			$ouput_string = "<div class='otw_typegraphy_element'>".$ouput_string."</div>";
		}
		//echo wp_kses_post($ouput_string);
		echo $ouput_string;
		echo '</div>';
		//exit();

	}



	protected function _content_template() {
		?>
		<div class="testing_typegraphy otw-dynamic-composer-elementor-widget">
		<#
			if(settings.composer){
				if(settings.list.length){
					_.each( settings.list, function( item ) {
						settings.composer = settings.composer.replace(item.list_variable_name, item.list_variable_value);
					});
				}
				if(settings.return_type == 'calculations'){
					settings.composer = "<"+settings.return_type_container+" class='otw_typegraphy_element'>"+settings.composer_math_before_text+settings.composer+settings.composer_math_after_text+"</"+settings.return_type_container+">";
				}
			}
		#>
			<div class="otw_typegraphy_element">{{{ settings.composer }}}</div>
		</div>


		<?php /*<# if ( settings.list.length ) { #>
		<dl>
			<# _.each( settings.list, function( item ) { #>
				<dt class="elementor-repeater-item-{{ item._id }}">{{{ item.list_variable_name }}}</dt>
				<dd>{{{ item.list_variable_value }}}</dd>
			<# }); #>
			</dl>
		<# } #> 

		<script>
		String.prototype.cleanup = function() {
					return this.toLowerCase().replace(/[^a-zA-Z0-9]+/g, "-");
			}

			
			
			
		</script>*/ ?>
		<?php
	}
}
