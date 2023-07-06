<?php
namespace Gloo\Modules\TypographyPlus;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'typography-plus-gloo-modules';

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

		//require gloo()->modules_path( 'woocommerce-price-widget/inc/autoload.php' );  
		//otw_woocommerce_price_widget();

		add_action( 'elementor/element/after_section_end', function( $controls_stack, $section_id ){
		
		$typography_control_names = array('typography_typography', '_typography', /*'math_type_typography_typography'*/);
			foreach($typography_control_names as $typography_control_name){
				$typography_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), $typography_control_name );
			
				if ( !is_wp_error( $typography_data ) ){
					$controls_stack->start_injection( [
						'type' => 'control',
						'at' => 'before',
						'of' => $typography_control_name,
					] );
					
					$controls_stack->add_control(
						'gloo_text_stroke_toggle',
						[
							'label' => __( 'Text Stroke', 'gloo_for_elementor' ),
							'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
							'label_off' => __( 'Default', 'gloo_for_elementor' ),
							'label_on' => __( 'Custom', 'gloo_for_elementor' ),
							'return_value' => 'yes',
							'default' => 'yes',
						]
					);
					$controls_stack->start_popover();
			
					$controls_stack->add_control(
						'gloo_text_stroke',
						[
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Text Stroke', 'gloo' ),
						]
					);
			
					$controls_stack->add_control(
						'gloo_text_stroke_fill_color',
						[
							'label' => __( 'Stroke fill color', 'gloo' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'render_type' => 'ui',
							'scheme' => [
								'type' => \Elementor\Core\Schemes\Color::get_type(),
								'value' => '',
							],
							'selectors' => [
								'{{WRAPPER}}' => '-webkit-text-fill-color: {{VALUE}}',
							],
							'condition' => [
								'gloo_text_stroke_toggle' => 'yes',
								'gloo_text_stroke'=> 'yes'
							]
						]
					);
			
					$controls_stack->add_control(
						'gloo_text_stroke_stroke_color',
						[
							'label' => __( 'Stroke color', 'gloo_for_elementor' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'render_type' => 'ui',
							'scheme' => [
								'type' => \Elementor\Core\Schemes\Color::get_type(),
								'value' => '',
							],
							'selectors' => [
								'{{WRAPPER}} > .elementor-widget-container' => '-webkit-text-stroke-color: {{VALUE}}',
								//'{{SELECTOR}}' => '-webkit-text-stroke-color: {{VALUE}}',
							],
							//'selector_value' => '-webkit-text-stroke-color: {{VALUE}}',
							'condition' => [
								'gloo_text_stroke_toggle' => 'yes',
								'gloo_text_stroke'=> 'yes'
							],        
						]
					);
					
					$controls_stack->add_control(
					'gloo_text_stroke_width',
						array(
						'label' => __( 'Stroke size', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'render_type' => 'ui',
						'size_units' => array('px', 'em', 'rem', 'vw'),
						'range' => array('px' => array('min' => 1, 'max' => 200), 'vw' => array('min' => 0.1, 'max' => 10, 'step' => 0.1)),
						//'selector_value' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}}',
						'selectors' => array('{{WRAPPER}} > .elementor-widget-container' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}}'),
						'classes' => 'elementor-group-control-typography elementor-group-control elementor-group-control-gloo_text_stroke_width',
						'responsive' => array('max' => 'desktop'),
						'default' => array('unit' => 'px', 'size' => '', 'sizes' => array()),
						'condition' => [
							'gloo_text_stroke_toggle' => 'yes',
							'gloo_text_stroke'=> 'yes'
						]
					));

					$controls_stack->add_control(
						'gloo_stroke_gradient',
						[
							'label' => __( 'Gradient Stroke', 'gloo_for_elementor' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,          
							'selectors' => [
								'{{WRAPPER}} > .elementor-widget-container' =>'-webkit-text-stroke: {{gloo_text_stroke_width.SIZE}}{{gloo_text_stroke_width.UNIT}} transparent;',
							],
						]
					);

					$controls_stack->add_control(
						'gloo_stroke_image_toggle',
						[
							'label' => __( 'Stroke Image', 'gloo_for_elementor' ),
							'type' => \Elementor\Controls_Manager::SWITCHER,          
							'condition' => [
								'gloo_stroke_gradient' => 'yes',
 							],
						]
					);

					$controls_stack->add_control(
						'gloo_stroke_image',
						[
							'label' => __( 'Stroke Background', 'gloo_for_elementor' ),
							'type' => \Elementor\Controls_Manager::MEDIA,
							'render_type' => 'ui',
							'default' => [
								'url' => \Elementor\Utils::get_placeholder_image_src(),
							],
							'condition' => [
								'gloo_stroke_image_toggle' => 'yes',
								'gloo_stroke_gradient' => 'yes',
							],
							'selectors' => [
								'{{WRAPPER}} > .elementor-widget-container' => 'background: url({{URL}}) no-repeat; background-size: cover; -webkit-background-clip: text; -ms-background-clip: text; -moz-background-clip: text; background-clip: text;',
							],
						]
					);

					$controls_stack->add_control(
					'gloo_color_a',
						array(
						'label' => _x( 'First Color', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#f2295b',
						'render_type' => 'ui',
						'of_type' => 'gradient',
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
					));
			
					$controls_stack->add_control(
					'gloo_color_a_stop',
						array(
						'label' => _x( 'Location', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ '%' ],
						'default' => [
							'unit' => '%',
							'size' => 0,
						],
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
						'render_type' => 'ui',
						'of_type' => 'gradient',
					));
			
					$controls_stack->add_control(
						'gloo_color_b',
						array(
						'label' => _x( 'Second Color', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#f2295b',
						'render_type' => 'ui',
						'of_type' => 'gradient',
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
					));
			
					$controls_stack->add_control(
						'gloo_color_b_stop',
						array(
						'label' => _x( 'Location', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ '%' ],
						'default' => [
							'unit' => '%',
							'size' => 100,
						],
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
						'render_type' => 'ui',
						'of_type' => 'gradient',
					));

					$controls_stack->add_control(
						'gloo_color_c',
						array(
						'label' => _x( 'Third Color', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#f2295b',
						'render_type' => 'ui',
						'of_type' => 'gradient',
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
					));
			
					$controls_stack->add_control(
						'gloo_color_c_stop',
						array(
						'label' => _x( 'Location', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ '%' ],
						'default' => [
							'unit' => '%',
							'size' => 100,
						],
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
						'render_type' => 'ui',
						'of_type' => 'gradient',
					));
			
					$controls_stack->add_control(
						'gloo_gradient_angle',
						array(
						'label' => _x( 'Angle', 'Background Control', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'deg' ],
						'render_type' => 'ui',
						'default' => [
							'unit' => 'deg',
							'size' => 180,
						],
						'range' => [
							'deg' => [
								'step' => 10,
							],
						],
						'selectors' => [
							'{{WRAPPER}} > .elementor-widget-container' => 'background-color: transparent; background: -webkit-linear-gradient({{SIZE}}{{UNIT}},{{gloo_color_a.VALUE}} {{gloo_color_a_stop.SIZE}}{{gloo_color_a_stop.UNIT}}, {{gloo_color_b.VALUE}} {{gloo_color_b_stop.SIZE}}{{gloo_color_b_stop.UNIT}}, {{gloo_color_c.VALUE}} {{gloo_color_c_stop.SIZE}}{{gloo_color_c_stop.UNIT}}); -webkit-background-clip: text;',
						],
						'condition' => [
							'gloo_stroke_image_toggle!' => 'yes',
							'gloo_stroke_gradient' => 'yes'
						],
						'of_type' => 'gradient',
					));
			
					$controls_stack->end_popover();    
					$controls_stack->end_injection();
				}
			}
	
			if($section_id == '_section_background'){
	
				$controls_stack->start_injection( [
					'type' => 'section',
					'at' => 'end',
					'of' => $section_id,
				] );
	
				$controls_stack->add_control(
					'gloo_background_clip_text',
					[
						'label' => __( 'Background Clip Text', 'gloo_for_elementor' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,          
						'selectors' => [
							'{{WRAPPER}} > .elementor-widget-container' => '-webkit-text-fill-color: transparent; -ms-text-fill-color: transparent; -moz-text-fill-color: transparent; text-fill-color: transparent; -webkit-background-clip: text; -ms-background-clip: text; -moz-background-clip: text; background-clip: text;',
						],
					]
				);
	
				$controls_stack->end_injection();
	
			}
			
			//db($typography_data);
			
			//db(get_class_methods (\Elementor\Plugin::instance()->controls_manager));
			//db(\Elementor\Plugin::instance()->controls_manager->get_controls_names());
			
			//db(\Elementor\Plugin::instance()->controls_manager->get_control_groups());
			//db(\Elementor\Plugin::instance()->controls_manager->get_control_groups()['typography']);
			//db(get_class_methods (\Elementor\Plugin::instance()->controls_manager->get_control_groups()['typography']));
			//db(get_class_methods(\Elementor\Plugin::instance()->controls_manager->get_control_groups()['typography']));exit();
			
			
			//if ( is_wp_error( $typography_data ) )
				//return;
	
			//db($section_id);
			
			/*if('section_typography' == $section_id){
				db($controls_stack->get_controls());
				exit();
			}*/
			//if ( 'section_typography' == $section_id) {
	
				//db(get_class_methods($controls_stack));
				//db(get_class_methods (\Elementor\Plugin::instance()->controls_manager));
				//db(\Elementor\Plugin::instance()->controls_manager->get_controls());
	
				/*$controls_stack->start_controls_section(
					'content_section',
					[
						'label' => __( 'tes s  sd Global Fonts', 'elementor' ),
						'tab' => 'content',
					]
				);
				$controls_stack->add_control(
					'gloo_test_family',
					array(
						'label'   => __( 'test g Fallback Family', 'gloo_for_elementor' ),
										'type'    => \Elementor\Controls_Manager::FONT,
						'default' => '',
					)
				);
				$controls_stack->end_controls_section();
				//$typography_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), "system_typography" );
				$typography_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), "custom_typography" );
				if ( is_wp_error( $typography_data ) )
					return;
				
				$tmp = new \Elementor\Repeater();
				$tmp->add_control(
					'otw_text_stroke_width',
					array(
						'label' => 'Text stroke size',
						'type' => \Elementor\Controls_Manager::SLIDER,
						'tab' => 'content',          
						'size_units' => array('px', 'em', 'rem', 'vw'),
						'range' => array('px' => array('min' => 1, 'max' => 200), 'vw' => array('min' => 0.1, 'max' => 10, 'step' => 0.1)),
						'selector_value' => 'font-size: {{SIZE}}{{UNIT}}',
						'selectors' => array('{{WRAPPER}}' => '--e-global-typography-{{_id.VALUE}}-font-size: {{SIZE}}{{UNIT}}'),
						'condition' => array('typography_typography!' => ''),
						'classes' => 'elementor-group-control-typography elementor-group-control elementor-group-control-otw_text_stroke_width',
						'groupPrefix' => 'typography_',
						'groupType' => 'typography',
						'responsive' => array('max' => 'desktop'),
						'name' => 'otw_text_stroke_width',
						'default' => array('unit' => 'px', 'size' => '', 'sizes' => array()),
						'popover' => array('end' => true),
					));
	
				$otw_text_stroke_width = $tmp->get_controls();
				$otw_text_stroke_width = $otw_text_stroke_width['otw_text_stroke_width'];
				
				foreach ( $typography_data['fields'] as $field_key => $field ) {
					unset($typography_data['fields'][$field_key]);
				}
				$typography_data['fields']['otw_text_stroke_width'] = $otw_text_stroke_width;
				$controls_stack->update_control(
					'custom_typography',
					$typography_data
				);*/
				
				/*$typography_data['fields']['otw_text_stroke_width'] = array(
					'type' => 'slider',
					'tab' => 'content',
					'label' => 'size',
					'size_units' => array('px', 'em', 'rem', 'vw'),
					'range' => array('px' => array('min' => 1, 'max' => 200), 'vw' => array('min' => 0.1, 'max' => 10, 'step' => 0.1)),
					'selector_value' => 'font-size: {{SIZE}}{{UNIT}}',
					'selectors' => array('{{WRAPPER}}' => '--e-global-typography-{{_id.VALUE}}-font-size: {{SIZE}}{{UNIT}}'),
					'condition' => array('typography_typography!' => ''),
					'classes' => 'elementor-group-control-typography elementor-group-control elementor-group-control-otw_text_stroke_width',
					'groupPrefix' => 'typography_',
					'groupType' => 'typography',
					'responsive' => array('max' => 'desktop'),
					'name' => 'otw_text_stroke_width',
					'default' => array('unit' => 'px', 'size' => '', 'sizes' => array()),
				);
				$controls_stack->update_control(
					'system_typography',
					$typography_data
				);*/
				//$typography_data['fields']['field_type']['options']['google_address'] = 'Google Address';
				//db(\Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), "system_typography" ));
				
			//}
	
			/*if ( 'advanced' == $section_id ) {
				foreach ( [ 'before', 'after', 'fallback' ] as $control_id ) {
					$control_data = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), $control_id );
					if($control_data){
						$control_data['dynamic'] = [
							'active' => true,
						];
						$controls_stack->update_control( $control_id, $control_data );
					}
				}
			}*/
	
			
			
			
		}, 10, 2);

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
