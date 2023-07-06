<?php
namespace OTW\WoocommercePriceWidget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

class PriceWidget extends \Elementor\Widget_Base {

	/*public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
    wp_register_script( 'DynamicComposerWidget', plugin_dir_url(OTW_DYNAMICS_COMPOSER_PLUGIN_FILE).'js/DynamicComposerWidget.js', array('jquery', 'elementor-frontend'), '1.0.0', true );
	}*/
	
	public function get_name() {
		return 'otwwcpricewidget';
	}

	public function get_title() {
		return __( 'Price', 'gloo_for_elementor' );
	}


	public function get_categories() {
		return [ 'gloo' ];
	}

	public function get_icon() {
		return 'gloo-elements-icon-woo';
	}

	protected function _register_controls() {

		/*$this->start_controls_section(
			'section_price_style',
			[
				'label' => __( 'Price', 'gloo_for_elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);*/

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Price', 'gloo_for_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wc_style_warning',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		

		$this->add_control(
			'default_variation_price_type',
			[
			'label' => __( 'Default Variation Price', 'gloo_for_elementor' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'' => __( 'Default', 'gloo_for_elementor' ),
				'high_to_discount' => __( 'Highest Price and Discount Price', 'gloo_for_elementor' ),
				'low_to_discount' => __( 'Lowest Price and Discount Price', 'gloo_for_elementor' ),
				//'paragraphs' => __( 'Paragraphs', 'gloo_for_elementor' ),
				]
			]
		);
		$this->add_control(
			'wc_style_warnings',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Above Default variation price will show its effect only if there are no default varation selected from edit product page.', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);
		

		
		$this->add_control(
			'toggle_prices_location',
			[
				'label' => __( 'Toggle Regular and Sale price.', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-product-toggle_prices_location',
			]
		);
		
		$this->add_control(
			'loop_price',
			[
				'label' => __( 'Use for archive loop', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-loop-price-block-',
			]
		);

		$this->add_control(
			'display_both_prices',
			[
				'label' => __( 'Display both prices', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-both-price-block-',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Price', 'gloo_for_elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,				
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'gloo_for_elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'gloo_for_elementor' ),
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
				'label' => __( 'Color', 'gloo_for_elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'.woocommerce {{WRAPPER}} .price' => 'color: {{VALUE}}',
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
				'selector' => '.woocommerce {{WRAPPER}} .price',
			]
		);


		$this->add_control(
			'sale_heading',
			[
				'label' => __( 'Sale Price', 'gloo_for_elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label' => __( 'Color', 'gloo_for_elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.woocommerce {{WRAPPER}} .price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sale_price_typography',
				'selector' => '.woocommerce {{WRAPPER}} .price ins',
			]
		);

		$this->add_control(
			'price_block',
			[
				'label' => __( 'Stacked', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-product-price-block-',
			]
		);

		$this->add_responsive_control(
			'sale_price_spacing',
			[
				'label' => __( 'Spacing', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}:not(.elementor-product-price-block-yes) del' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}:not(.elementor-product-price-block-yes) del' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-product-price-block-yes del' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		

		$this->end_controls_section();
	}


	/*public function get_script_depends() {
		return [ 'DynamicComposerWidget' ];
 	}*/


	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		if($this->get_settings( 'loop_price' ) == 'yes'){
			update_option('woo_gloo_price_widget', ArrayToSerializeString($this->get_active_settings()));
			update_option('woo_gloo_price_widget_id', $this->get_id());
		}
		//update_option('woo_gloo_price_widget', ArrayToSerializeString($this->get_settings()));
		
		//db($this->get_id());
		//db($this->get_active_settings());
		//db(get_class_methods($this));exit();


		global $product, $post;
		//db($post->ID);
		if(is_product())
			$product = wc_get_product();
		elseif(isset($post) && $post)
			$product = wc_get_product($post->ID);
		
		
		if ( empty( $product ) ) {
			return;
		}

		$display_settings = $this->get_settings_for_display();
		
		
		if($this->get_settings( 'toggle_prices_location' ) == 'yes' && empty($this->get_settings('is_archive_loop'))){
			
			add_filter('woocommerce_get_price_html', function($price, $product){
				$display_both = false;
				if($this->get_settings( 'display_both_prices' ) == 'yes')
					$display_both = true;
								
				return otw_price_widget_simple_product_price_html($price, $product, $display_both);

			}, 100, 2);
			
			//add_filter('woocommerce_variation_sale_price_html', 'otw_price_widget_variable_product_price_html', 10, 2);
			//add_filter('woocommerce_variation_price_html', 'otw_price_widget_variable_product_price_html', 10, 2);
			
			//add_filter('woocommerce_variable_sale_price_html', 'otw_price_widget_variable_product_minmax_price_html', 10, 2);
			//add_filter('woocommerce_variable_price_html', 'otw_price_widget_variable_product_minmax_price_html', 10, 2);
			
			
		}
		

		//$settings = $this->get_settings_for_display();
		$default_variation_price_type = $this->get_settings( 'default_variation_price_type' );

		if ( $product->is_type( 'variable' )) {
			
				// Main Price
				//$main_prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
				
				// Sale Price
				//$regular_prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
				//$prices = array_merge($main_prices, $regular_prices);
				//sort( $prices );

				//if ( $price !== $saleprice && $product->is_on_sale() ) {
					//$price = '<del>' . wc_price($product->get_variation_regular_price( 'max', true )) . $product->get_price_suffix() . '</del> <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
				//}
				
				$lowest_sale_price = wc_price($product->get_variation_price( 'min', true ));
				if($default_variation_price_type == 'high_to_discount'){
					$price = '<ins>' . wc_price($product->get_variation_regular_price( 'max', true )) . $product->get_price_suffix() . '</ins> – <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
				}elseif($default_variation_price_type == 'low_to_discount'){
					if($lowest_sale_price < wc_price($product->get_variation_regular_price( 'min', true ))){
						$price = '<del>' . wc_price($product->get_variation_regular_price( 'min', true )) . $product->get_price_suffix() . '</del> <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
					}else if($lowest_sale_price == wc_price($product->get_variation_regular_price( 'min', true ))){
						$price = '<ins>' . wc_price($product->get_variation_regular_price( 'min', true )) . $product->get_price_suffix() . '</ins>';
					}
					else{
						$price = '<ins>' . wc_price($product->get_variation_regular_price( 'min', true )) . $product->get_price_suffix() . '</ins> – <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
					}
				}else{
					$default_attributes = $product->get_default_attributes();
					foreach($product->get_available_variations() as $variation_values ){
							foreach($variation_values['attributes'] as $key => $attribute_value ){
									$attribute_name = str_replace( 'attribute_', '', $key );
									$default_value = $product->get_variation_default_attribute($attribute_name);
									if( $default_value == $attribute_value ){
											$is_default_variation = true;
									} else {
											$is_default_variation = false;
											break; // Stop this loop to start next main lopp
									}
							}
							if( isset($is_default_variation) && $is_default_variation ){
									$variation_id = $variation_values['variation_id'];
									break; // Stop the main loop
							}
					}

					// Now we get the default variation data
					if(isset($is_default_variation) && $is_default_variation && isset($variation_id)){
							
							// Get the "default" WC_Product_Variation object to use available methods
							$default_variation = wc_get_product($variation_id);
							$regular_price = $default_variation->get_regular_price();
							$sale_price = $default_variation->get_sale_price();
							$price_amt = $default_variation->get_price();
							$price = otw_price_widget_commonPriceHtml($price_amt, $regular_price, $sale_price, $display_both);
							
					}else{
						$price = '<ins>' . wc_price($product->get_variation_price( 'max', true )) . $product->get_price_suffix() . '</ins> – <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
					}
				}
				
				if(isset($price) /*&& !$product->get_default_attributes() && $default_variation_price_type*/){
					echo '<p class="price">'.$price.'</p>
					<div class="hidden-variable-price" style="display:none;" >'.$price.'</div>';
				}else{
					wc_get_template( '/single-product/price.php' );
				}
			

		}else{
			wc_get_template( '/single-product/price.php' );
		}

		if(isset($product) && $product && is_product() && $product->is_type( 'variable' )){

			wp_register_script( 'otw-woocommerce-price-widget', gloo()->plugin_url('includes/modules/woocommerce-price-widget/') . 'assets/js/otw-woocommerce-price-widget.js', array('jquery'), '1.0');
			wp_enqueue_script( 'otw-woocommerce-price-widget' );

			//$js_variables = array('ajax_url' => admin_url('admin-ajax.php'));
			//wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );
		
			?>
			<style>
					div.woocommerce-variation-price,
					div.woocommerce-variation-availability,
					div.hidden-variable-price {
							height: 0px !important;
							overflow:hidden;
							position:relative;
							line-height: 0px !important;
							font-size: 0% !important;
					}
			</style>
			
			<?php
		}

	
	}



	protected function _content_template() {

	}

}
