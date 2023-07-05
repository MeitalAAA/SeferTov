<?php
namespace Gloo\Modules\Woocommerce_Swatches_Widget;
/**
 * Elementor OTW Swatch Display Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Widget_Product_Swatch extends \Elementor\Widget_Base {
	
	private $prefix = 'otw_field_';

	public static $slug = 'gloo_for_elementor';

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style('swatch-widget', gloo()->plugin_url( 'assets/css/swatch-widget.css' ), [], '1.1' );
	}
	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'otw_swatch_display';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Woo Swatches', 'gloo_for_elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'gloo-elements-icon-woo';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'gloo' ];
	}

	/**
	 * Enqueue styles.
	 */
	public function get_style_depends() {
		return array( 'swatch-widget' );
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			$this->prefix,
			[
				'label' => __( 'OTW Attributes', self::$slug ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$attributes_array = [];

		if(function_exists('wc_get_attribute_taxonomies')) {

			$attributes = wc_get_attribute_taxonomies();

			if(!empty($attributes))  {
				foreach ($attributes as $attribute) {
					$attributes_array[$attribute->attribute_name] = ucwords($attribute->attribute_label);
				}
			}
		}

		$repeater->add_control(
			$this->prefix . 'attribute',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Select Attributes', self::$slug ),
				'label_block' => true,
				
				'options'     => $attributes_array
			)
		);

		$repeater->add_control(
			$this->prefix . 'type',
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label'       => __( 'Detect Type', self::$slug ),
				'label_on'     => __( 'Yes', self::$slug ),
				'label_off'    => __( 'No', self::$slug ),
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			$this->prefix . 'clickable',
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label'       => __( 'Clickable', self::$slug ),
				'label_on'     => __( 'Yes', self::$slug ),
				'label_off'    => __( 'No', self::$slug ),
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			$this->prefix . 'hide_condition',
			array(
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label'       => __( 'Hide If Empty', self::$slug ),
				'label_on'     => __( 'Yes', self::$slug ),
				'label_off'    => __( 'No', self::$slug ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			$this->prefix . 'attribute_list',
			[
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'attribute}}}',
				'label_block'   => false,

			]
		);

		$this->add_control(
			$this->prefix . 'filter_attributes',
			[
				'label' => __( 'Filter number of attributes', self::$slug ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', self::$slug ),
				'label_off' => __( 'No', self::$slug ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			$this->prefix . 'number_of_attributes',
			[
				'label' => __( 'Number of attributes', self::$slug ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 4,
				'condition' => [
					$this->prefix . 'filter_attributes' => 'yes'
				],
			]
		);

		$this->add_control(
			$this->prefix . 'filter_values',
			[
				'label' => __( 'Filter number of values', self::$slug ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', self::$slug ),
				'label_off' => __( 'No', self::$slug ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			$this->prefix . 'number_of_values',
			[
				'label' => __( 'Number of values', self::$slug ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 4,
				'condition' => [
					$this->prefix . 'filter_values' => 'yes'
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
			  'label' => __( 'Style Section', self::$slug ),
			  'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);		  

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background For Button', self::$slug )			,
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} li',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Button Label Color', self::$slug ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .otw-swatche-button span' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'otw_swatch_typography',
				'label' => __( 'Typography', self::$slug ),
				//'scheme' => \Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .otw-swatche-button span',
			]
		);

		$this->add_control(
			$this->prefix.'swatch_gap',
			[
				'label' => __( 'Gaps', self::$slug ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .otw-swatches li' => 'margin: 4px {{SIZE}}{{UNIT}} 4px 0',
					'{{WRAPPER}} .otw-swatches.otw-vertical li' => 'margin: 4px 0 {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		$this->add_control(
			$this->prefix.'swatch_size',
			  [
			   'label' => __( 'Size', self::$slug ),
			   'type' => \Elementor\Controls_Manager::SLIDER,
			   'separator' => 'after',
			   'show_label' => true,
			   'selectors' => [
				  '{{WRAPPER}} li	' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$this->prefix.'text_align',
			[
				'label' => __( 'Alignment', self::$slug ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', self::$slug ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', self::$slug ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', self::$slug ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'swatch_border',
				'label' => __( 'Border', self::$slug ),
				'selector' => '{{WRAPPER}} li',
			]
		);

		$this->add_responsive_control(
			$this->prefix.'border_radius',
			[
			 'label' => __( 'Border Radius', 'Spicy-extension' ),
			 'type' => \Elementor\Controls_Manager::DIMENSIONS,
			 'size_units' => [ 'px', '%' ],
			 'selectors' => [
				 '{{WRAPPER}} li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			 ],
		 	]
	 	);

		$this->add_control(
			$this->prefix . 'position',
			[
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Horizontal/Vertical', self::$slug ),
				'label_block' => true,
				'default'     => 'horizontal',
				'options'     => [
					'horizontal' => __('Horizontal'),
					'vertical' => __('Vertical')
				]
			]
		);

		$this->end_controls_section();

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
		$position = $this->get_settings( $this->prefix . 'position' );
		$alignment = $settings[$this->prefix.'text_align'];
		$filter_attributes = $settings[$this->prefix.'filter_attributes'];
		$number_of_attributes = $settings[$this->prefix.'number_of_attributes'];
		$filter_values = $settings[$this->prefix.'filter_values'];
		$number_of_values = $settings[$this->prefix . 'number_of_values'];
		
		$otw_position = '';
		$otw_alignment = '';

		if($position == 'vertical') {
			$otw_position = 'otw-vertical';
		}

		if(!empty($alignment)) {
			$otw_alignment = 'otw-'.$alignment;
		}

		global $product;

		if ( is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );

			if(!empty($settings['otw_field_attribute_list'])) {

				$repeater_attributes = $settings['otw_field_attribute_list'];
				$available_attributes = [];

				foreach($repeater_attributes as $attribute) {

					$product_attribute_name = $attribute['otw_field_attribute'];
					$product_attributes = wc_get_product_terms( $product->get_id(), 'pa_'.$product_attribute_name );
					
					if(!empty($product_attributes)) {

						if($filter_values == 'yes' && !empty($number_of_values)) {
							$product_attributes = array_slice($product_attributes, 0, $number_of_values);
						}

 						$available_attributes[$product_attribute_name] = $product_attributes;
 					}	
				}	

				/* attribute limit */
				if($filter_attributes == 'yes' && !empty($number_of_attributes)) {
					$available_attributes = array_slice($available_attributes, 0, $number_of_attributes);
				}

				if(!empty($available_attributes)) {

					foreach($available_attributes as $attribute_name => $available_attribute) {
						$attrubute_type = [];	
						$attribute_taxonomies = wc_get_attribute_taxonomies();

						if ( $attribute_taxonomies ) {
							foreach ( $attribute_taxonomies as $tax ) {
								if(isset($tax->attribute_type) && !empty($tax->attribute_type)) {
									$attrubute_type[$tax->attribute_name] = $tax->attribute_type;
								}
							}
						}	

					 	$type = $attrubute_type[$attribute_name];

						$options = [];

						foreach($available_attribute as $product_attribute) {
							$options[] = $product_attribute->slug;
						}

						if($filter_values == 'yes' && !empty($number_of_values) && !empty($options)) {
							$options = array_slice($options, 0, $number_of_values);
						}
				
						$args = [
							'product' => $product,
							'attribute' => 'pa_'.$attribute_name,
							'type' => $type,
							'selected' => false,
							'options'=> $options
						];

						$content =  '';

						if(function_exists('wvs_variable_item')){
							$content = wvs_variable_item( $type, $options, $args );
						}

						echo '<ul class="otw-swatches otw-swatche-'.$type.' '.$otw_position.' '.$otw_alignment.'">';
						echo $content;
						echo '</ul>';
					}				
				} 					
			}
		}
	}

}