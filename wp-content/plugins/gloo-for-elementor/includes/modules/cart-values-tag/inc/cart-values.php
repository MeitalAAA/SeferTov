<?php
namespace Gloo\Modules\Cart_Values_Dynamic_Tags;

Class Cart_Values extends \Elementor\Core\DynamicTags\Tag {
	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'gloo-cart-values';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Cart Values Dynamic Tag', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		 ];
	}

	
	/**
	 * Register Controls
	 *
	 * Registers the Dynamic tag controls
	 *
	 * @return void
	 * @since 2.0.0
	 * @access protected
	 *
	 */
	protected function _register_controls() {

		$cart_values = array(
			'item' => 'Items',
			'total' => 'Cart Total',
			'coupons' => 'Coupons',
			'price' => 'Price'
		);

		$this->add_control(
			'cart_values',
			array(
				'label'   => __( 'Cart Values', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $cart_values
			)
		);

		$prices = array(
			'price_before_vat' => 'Price Before Vat',
			'price_of_shipping' => 'Price Of Shipping',
			'price_of_vat' => 'Price Of Vat'
		);

		$this->add_control(
			'cart_prices',
			array(
				'label'   => __( 'Cart Prices', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $prices,
				'condition' => [
					'cart_values' => 'price'
				],
			)
		);

		$item_options = array(
			'title' => 'Title',
			'sku' => 'Sku',
			'price' => 'Prices',
			'quantity' => 'Quantity',
			'tl_qt_pr' => 'Title + Qt + Price',
			'sku_tl_at_pr' => 'Sku + Title + Qt + Price',
			'product_ids' => 'Product ID\'s',
			'current_terms' => 'Current Terms'
		);

		$this->add_control(
			'item_output',
			array(
				'label'   => __( 'Items Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $item_options,
				'condition' => [
					'cart_values' => 'item'
				],
			)
		);

		$taxonomy = [];

		$tax_args = [
			'public' => true,
		];

		$taxonomies = get_taxonomies($tax_args);

		if(!empty($taxonomies)) {
			foreach ($taxonomies as $tax) {
				$tax_info = get_taxonomy($tax);
				$taxonomy[$tax] = $tax_info->label;
			}
		}

		$this->add_control(
			'select_taxonomy',
			array(
				'label'   => __( 'Select Taxonomy', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $taxonomy,
				'condition' => [
					'item_output' => 'current_terms'
				],
			)
		);


		$this->add_control(
			'terms_delimiter',
			array(
				'label'     => __( 'Terms Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'select_taxonomy!' => '',
					'item_output' => 'current_terms'
				],
			)
		);

		$this->add_control(
			'price_symbol',
			[
				'label' => __( 'Price Symbol', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'gloo_for_elementor' ),
				'label_off' => __( 'Hide', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'cart_values',
							'operator' => 'in',
							'value' => array('price','total')
						],
						[
							'name' => 'item_output',
							'operator' => 'in',
							'value' => array('price','tl_qt_pr','sku_tl_at_pr')
						]
					]
				]
			]
		);

		$output_option = [	
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $output_option,
			)
		);

		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_output' => 'type_limeter'
				],
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					'field_output' => 'type_array'
				],
			)
		);
		
	}

	public function render() {
		
		$cart_value = $this->get_settings( 'cart_values' );
		$cart_prices = $this->get_settings( 'cart_prices' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$array_index = $this->get_settings( 'array_index' );
		$item_output = $this->get_settings( 'item_output' );
		$price_symbol = $this->get_settings( 'price_symbol' );
		$select_taxonomy = $this->get_settings( 'select_taxonomy' );
		$terms_delimiter = $this->get_settings( 'terms_delimiter' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		if ( !class_exists( 'WooCommerce' ) ) { 
			return;
		}
		
		if ( WC()->cart && WC()->cart->is_empty() )  {
			return false;
		}
        
		$cart = WC()->cart;
		$cart_data = array();
		$items = WC()->cart->get_cart();

		if($cart_value == 'item') {

			foreach($items as $item => $values) { 
				
				$_product =  wc_get_product( $values['data']->get_id()); 
				$title = $_product->get_title();
				$quantity = $values['quantity'];
				$sku = $_product->get_sku();
				$product_price = $_product->get_price();
				$product_id = $values['data']->get_id();

				$price = $this->get_price_with_symbol($product_price, $price_symbol);

				if(!empty($item_output)) {
				
					if($item_output== 'title') {
						
						$cart_data[] = $title;
					
					} elseif($item_output== 'sku') {
						
						$cart_data[] = $sku;

					} elseif($item_output== 'price') {

						$cart_data[] = $price;

					} elseif($item_output== 'quantity') {

						$cart_data[] = $quantity;

					} elseif($item_output== 'tl_qt_pr') {

						$cart_data[] = $title.' '.$quantity.' '.$price;
						
					} elseif($item_output== 'sku_tl_at_pr') {

						$cart_data[] = $sku.' '.$title.' '.$quantity.' '.$price;

					} elseif ($item_output == 'product_ids') {
						
						$cart_data[] = $product_id;

					} elseif($item_output == 'current_terms') {

						if(!empty($select_taxonomy)) {

							$post_terms = wp_get_post_terms($product_id, $select_taxonomy, array('fields' => 'all'));

							if(!empty($post_terms)) {
								foreach($post_terms as $term) {
									$terms_data[] = $term->name;
								}
								
								if(!empty($terms_data)) {
									$cart_data[] = implode( $terms_delimiter, $terms_data );
								}
								unset($terms_data);
							}
						}
					}
				}

			}

		} elseif($cart_value == 'total') {
			
			$cart_data[] = $this->get_price_with_symbol($cart->total, $price_symbol);

		} elseif($cart_value == 'coupans') {

			// cpopons alrady an array
			$cart_data = $cart->get_applied_coupons();

		} elseif($cart_value == 'price') {

			if($cart_prices == 'price_before_vat') {

				$cart_data[] = $this->get_price_with_symbol($cart->subtotal, $price_symbol);

			} elseif($cart_prices == 'price_of_shipping') {
				
				$shipping_total = $cart->get_shipping_total();
				$cart_data[] = $this->get_price_with_symbol($shipping_total, $price_symbol);

			} elseif($cart_prices == 'price_of_vat') {
				
				$cart_data[] = $this->get_price_with_symbol($cart->tax_total, $price_symbol);

			}
		} 

		$output = '';

		if(!empty($cart_data) && is_array($cart_data)) {

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="tax-ul">';

				foreach ( $cart_data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="tax-ol">';

				foreach ( $cart_data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $cart_data );

			} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $cart_data );

			} else if ( $field_output == 'type_array' && is_numeric($array_index) ) {

				if ( isset( $cart_data[ $array_index ] ) && ! empty( $cart_data[ $array_index ] ) ) {
					$output = $cart_data[ $array_index ];
				}

			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $cart_data );
				else
					$output = implode( PHP_EOL, $cart_data );

			}

			echo $output;

		}
	}

	public function get_price_with_symbol($price = 0, $price_symbol = 'yes') {

		if($price_symbol == 'yes') {
			$price = wc_price($price);
		}

		return $price;
	}
}
