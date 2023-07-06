<?php
namespace Gloo\Modules\Bundle_Maker_Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
/**
 * Elementor OTW Swatch Display Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Widget_Bundle_Maker extends \Elementor\Widget_Base {
	
	private $prefix = 'bm_';

	public static $slug = 'gloo_for_elementor';

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		//wp_register_style('swatch-widget', gloo()->plugin_url( 'assets/css/swatch-widget.css' ), [], '1.1' );
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
		return 'bundle_maker';
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
		return __( 'Bundle Maker', 'gloo_for_elementor' );
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
				'label' => __( 'Bundle Settings', self::$slug ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			$this->prefix . 'redirect',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Redirect', self::$slug ),
				'options'     => [
					'cart' => 'Cart',
					'checkout' => 'Checkout'
				],
				'default' => 'cart',
			)
		);
	
		$this->add_control(
			$this->prefix . 'button_label',
			[
				'label' => __( 'Button Label', self::$slug ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Add To Cart',
				'label_block' => false,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$attributes_array = [
			'manual' => 'Manual',
		   	'acf_relation_field' => 'Acf Relation Field'
	   	];

		$this->add_control(
			$this->prefix . 'source',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Source', self::$slug ),
				'options'     => $attributes_array,
				'default' => 'manual',
			)
		);

		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			$this->prefix . 'product_id',
			[
				'label' => __( 'Product ID', self::$slug ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => false,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			$this->prefix . 'product_quantity',
			[
				'label' => __( 'Quantity', self::$slug ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'default' => 1,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			$this->prefix . 'product_list',
			[
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'product_id}}}',
				'label_block'   => false,
				'condition' => [
					$this->prefix . 'source' => 'manual'
				],
			]
		);

		$this->add_control(
			$this->prefix . 'acf_field_data',
			[
				'label' => __( 'Acf relation meta key', self::$slug ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'condition' => [
					$this->prefix . 'source' => 'acf_relation_field'
				],
			]
		);

		$this->add_control(
			$this->prefix . 'current_product',
			[
				'label' => __( 'Include current product', self::$slug ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			$this->prefix . 'bundle_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'if you are using acf relation dynamic tag then tag should return comma(,) separated id\'s', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					$this->prefix . 'source' => 'acf_relation_field'
				],
			]
		);

		$this->add_control(
			$this->prefix . 'acf_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'ACF relationship field option will use the woocommerce default variation defined in the product.quantity of each item will be automatically set to 1 item.', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					$this->prefix . 'source' => 'acf_relation_field'
				],
			]
		);
 
		$this->end_controls_section();

		$this->start_controls_section(
			$this->prefix . 'style',
			[
				'label' => esc_html__( 'Button', self::$slug ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .gloo-bundle-button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .gloo-bundle-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			$this->prefix . 'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', self::$slug ),
			]
		);

		$this->add_control(
			$this->prefix . 'button_text_color',
			[
				'label' => esc_html__( 'Text Color', self::$slug ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gloo-bundle-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => esc_html__( 'Background', self::$slug ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .gloo-bundle-button',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$this->prefix . 'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', self::$slug ),
			]
		);

		$this->add_control(
			$this->prefix . 'hover_color',
			[
				'label' => esc_html__( 'Text Color', self::$slug ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gloo-bundle-button:hover, {{WRAPPER}} .gloo-bundle-button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gloo-bundle-button:hover svg, {{WRAPPER}} .gloo-bundle-button:focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'label' => esc_html__( 'Background', self::$slug ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .gloo-bundle-button:hover, {{WRAPPER}} .gloo-bundle-button:focus',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			$this->prefix . 'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', self::$slug ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .gloo-bundle-button:hover, {{WRAPPER}} .gloo-bundle-button:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$this->prefix . 'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', self::$slug ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .gloo-bundle-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->prefix . 'border_radius',
			[
				'label' => esc_html__( 'Border Radius', self::$slug ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .gloo-bundle-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .gloo-bundle-button',
			]
		);

		$this->add_responsive_control(
			$this->prefix . 'text_padding',
			[
				'label' => esc_html__( 'Padding', self::$slug ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gloo-bundle-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
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
		$this->add_bundle_cart_button();
	}

	public function add_bundle_cart_button() {

		$settings = $this->get_settings_for_display();
		
		$this->add_render_attribute( 'bundle-button', 'class', 'gloo-bundle-button elementor-button' );
 		
		if ( $settings[$this->prefix . 'hover_animation'] ) {
			$this->add_render_attribute( 'bundle-button', 'class', 'elementor-animation-' . $settings[$this->prefix . 'hover_animation'] );
		}

		$redirect = $settings[$this->prefix . 'redirect'];
		$button_label = $settings[$this->prefix . 'button_label'];
		$source = $settings[$this->prefix . 'source'];
		$product_id = $settings[$this->prefix . 'product_id'];
		$redirect = $settings[$this->prefix . 'redirect'];

		$cart_url = wc_get_cart_url();

		if($redirect == 'checkout') {
			$cart_url = wc_get_checkout_url();
		}
		
		$product_ids = array();
		$quantities = array();
	
		if($settings[$this->prefix . 'current_product'] == 'yes') {
			
			$product = wc_get_product( get_the_id() );
			
			if ( is_a($product, 'WC_Product') ) {
				$product_ids[] = $product->get_id();
				$quantities[] = 1;
			}
		}
	
		if($source == 'manual') {
			$products = $settings[$this->prefix . 'product_list'];

			if(!empty($products)) {
				foreach( $products as $product ) {

					if(!empty($product[$this->prefix . 'product_id'])) {
						$product_ids[] = $product[$this->prefix . 'product_id'];
					}

					if(!empty($product[$this->prefix . 'product_quantity'])) {
						$quantities[] = $product[$this->prefix . 'product_quantity'];	
					}
				}

				$cart_url .= '?add-to-cart='.implode(',', $product_ids).'&quantity='.implode(',', $quantities);
			}

		} else if($source == 'acf_relation_field') {

			$acf_field_data = $settings[$this->prefix . 'acf_field_data'];
		
			if(!empty($acf_field_data)) {

				if( strpos($acf_field_data, ',') !== false ) {
 
					$ids = explode(',', $acf_field_data);
					$product_ids = array_merge($product_ids, $ids);
					
				} else {
					
					$related_products = get_field($acf_field_data, get_the_id());
					$field_object = get_field_object($acf_field_data);
					
					if(!empty($related_products)) {
						
						if(isset($field_object['return_format']) && !empty($field_object['return_format'])) {
							$return_format = $field_object['return_format'];
			
							if($return_format == 'object') {
								foreach($related_products as $item) {
									$product_ids[] = $item->ID;
								}
							} else {
								$product_ids = $related_products;
							}
						}
					}	
				}
		  
				$cart_url .= '?add-to-cart='.implode(',', $product_ids);
			}
		} ?>
		
		<div class="gloo-bundle-wrap">
			<a href="<?php echo $cart_url; ?>" <?php $this->print_render_attribute_string( 'bundle-button' ); ?>>
				<span class="gloo-bundle-text"><?php echo $button_label; ?></span>
			</a>
		</div>
	<?php }
}
