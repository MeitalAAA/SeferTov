<?php
namespace Gloo\Modules\Woocommerce_Discount_Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

/**
 * Elementor OTW Swatch Display Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Widget_Product_Discount extends \Elementor\Widget_Base {
	
	private $prefix = 'gloo';

	public static $slug = 'gloo_for_elementor';

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

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
		return 'gloo_product_discount';
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
		return __( 'Woo Product Discount', self::$slug);
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
			'content_section',
			[
				'label' => __( 'Content', self::$slug ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wc_notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Widget will display discount for woocommerce product', self::$slug),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Price', self::$slug ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,				
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', self::$slug ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', self::$slug ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', self::$slug ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', self::$slug ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Color', self::$slug ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .sale-perc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .sale-perc',
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
		
		global $product;

		if ( is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );			

			if ( ! $product->is_on_sale() ) return;

			if ( $product->is_type( 'simple' ) ) {
				
				$max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;

			} elseif ( $product->is_type( 'variable' ) ) {

				$max_percentage = 0;
				
				foreach ( $product->get_children() as $child_id ) {
					$variation = wc_get_product( $child_id );
					$price = $variation->get_regular_price();
					$sale = $variation->get_sale_price();
					
					if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / 	$price * 100;

					if ( $percentage > $max_percentage ) {
						$max_percentage = $percentage;
					}
				}
			}

			if ( $max_percentage > 0 ) echo "<div class='sale-perc'>-" . round($max_percentage) . "%</div>";
		}
	}

}