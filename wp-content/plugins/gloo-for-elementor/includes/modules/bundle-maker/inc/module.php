<?php

namespace Gloo\Modules\Bundle_Maker_Widget;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-bundle-maker';

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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_bundle_maker_widget']);
		add_action( 'wp_loaded', [$this, 'woocommerce_maybe_add_multiple_products_to_cart'], 15 );
 	}

	function woocommerce_maybe_add_multiple_products_to_cart() {
		// Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
		if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
			return;
		}
		
		// Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );
		
		$product_ids = explode( ',', $_REQUEST['add-to-cart'] );

		$quantities = (!empty( $_REQUEST['quantity'] )) ? explode( ',', $_REQUEST['quantity'] ) : '';

		$count       = count( $product_ids );
		$number      = 0;
		$i=0;
		foreach ( $product_ids as $key => $product_id ) {
			
			if ( $i === $count ) {
				// Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
				$_REQUEST['add-to-cart'] = $product_id;
		
				return \WC_Form_Handler::add_to_cart_action();
			}
			
			$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
			$was_added_to_cart = false;
			$adding_to_cart    = wc_get_product( $product_id );
 			
			if ( ! $adding_to_cart ) {
				continue;
			}
		
			//	$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart );
		
			/*
			 * Sorry.. if you want non-simple products, you're on your own.
			 *
			 * Related: WooCommerce has set the following methods as private:
			 * WC_Form_Handler::add_to_cart_handler_variable(),
			 * WC_Form_Handler::add_to_cart_handler_grouped(),
			 * WC_Form_Handler::add_to_cart_handler_simple()
			 *
			 * Why you gotta be like that WooCommerce?
			 */
 
			if(!empty($quantities) && is_array($quantities)) {
				$quantity = $quantities[$key];
			} else {
				$quantity =  1;
			}

			if ( $adding_to_cart->is_type( 'simple' ) ) {

				// quantity applies to all products atm
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	  
				if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
					wc_add_to_cart_message( array( $product_id => $quantity ), true );
				}
			} else {
				
				$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) );
				$missing_attributes = array();
				$variations         = array();
				$adding_to_cart     = wc_get_product( $product_id );
	  
				if ( ! $adding_to_cart ) {
				  continue;
				}
				// If the $product_id was in fact a variation ID, update the variables.
				
				if( $adding_to_cart->is_type( 'variable' ) ) {
					
					// $variation_id   = $product_id;
					$product = wc_get_product( $product_id );
					$default_attributes = get_post_meta($adding_to_cart->get_id(), '_default_attributes');

					foreach( $product->get_available_variations() as $variation_values ) {
						foreach($variation_values['attributes'] as $key => $attribute_value ) {
							$attribute_name = str_replace( 'attribute_', '', $key );
							$default_value = $product->get_variation_default_attribute($attribute_name);
						
							if( $default_value == $attribute_value ){
								$is_default_variation = true;
							} else {
								$is_default_variation = false;
								break; // Stop this loop to start next main lopp
							}
						}

						if( $is_default_variation ) {
							$variation_id = $variation_values['variation_id'];
							$adding_to_cart = wc_get_product( $product_id );
							if ( ! $adding_to_cart ) {
								continue;
							}
							//print_r($adding_to_cart);
							break; // Stop the main loop
						}
					}
			 
				} else if ( $adding_to_cart->is_type( 'variation' ) ) {
					
					$variation_id   = $product_id;
					$product_id     = $adding_to_cart->get_parent_id();
					$adding_to_cart = wc_get_product( $product_id );

					if ( ! $adding_to_cart ) {
						continue;
					}
				}
 	  
				// Gather posted attributes.
				$posted_attributes = array();
	  
				foreach ( $adding_to_cart->get_attributes() as $attribute ) {
				  if ( ! $attribute['is_variation'] ) {
					continue;
				  }
				  $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
	  
				  if ( isset( $_REQUEST[ $attribute_key ] ) ) {
					if ( $attribute['is_taxonomy'] ) {
					  // Don't use wc_clean as it destroys sanitized characters.
					  $value = sanitize_title( wp_unslash( $_REQUEST[ $attribute_key ] ) );
					} else {
					  $value = html_entity_decode( wc_clean( wp_unslash( $_REQUEST[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
					}
	  
					$posted_attributes[ $attribute_key ] = $value;
				  }
				}
 
				// Do we have a variation ID?
				// if ( empty( $variation_id ) ) {
				//   throw new \Exception( __( 'Please choose product options&hellip;', 'woocommerce' ) );
				// }
				
				// Check the data we have is valid.
				
				if(!empty($variation_id)) {
					$variation_data = wc_get_product_variation_attributes( $variation_id );
				
				foreach ( $adding_to_cart->get_attributes() as $attribute ) {
				  if ( ! $attribute['is_variation'] ) {
					continue;
				  }
	  
				  // Get valid value from variation data.
				  $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
				  $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';

				  /**
				   * If the attribute value was posted, check if it's valid.
				   *
				   * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
				   */
				  if ( isset( $posted_attributes[ $attribute_key ] ) ) {
					$value = $posted_attributes[ $attribute_key ];
	  
					// Allow if valid or show error.
					if ( $valid_value === $value ) {
					  $variations[ $attribute_key ] = $value;
					} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
					  // If valid values are empty, this is an 'any' variation so get all possible values.
					  $variations[ $attribute_key ] = $value;
					} else {
					//   throw new \Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
					}
				  } elseif ( '' === $valid_value ) {
					$missing_attributes[] = wc_attribute_label( $attribute['name'] );
				  }
				}
			}
				// if ( ! empty( $missing_attributes ) ) {
				//   throw new \Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
				// }

	  
			  $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
	  
			  if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			  }
			}

			$i++;
		}
	}
 
	public function register_bundle_maker_widget() {

		include_once( gloo()->modules_path( 'bundle-maker/inc/widget-bundle-maker.php' ) );
		$widget_object = new Widget_Bundle_Maker();
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type($widget_object);

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
