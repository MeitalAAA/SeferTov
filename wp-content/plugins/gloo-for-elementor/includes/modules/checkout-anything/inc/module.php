<?php
namespace Gloo\Modules\CheckoutAnything;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	private $field_prefix = '_gloo_order_';

	public $gloo_meta_keys = array();
	public $gloo_meta_key_id = '_gloo_order_meta_keys';

	public $slug = 'checkout_anything';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
		
		add_action('woocommerce_before_calculate_totals', array($this,'checkout_anything_cart_item_price'), 20, 1);
		add_filter( 'woocommerce_cart_item_price', array($this,'gloo_change_cart_table_price_display'), 30, 3 );

	//	add_action( 'init', array($this,'display_gloo_meta'));
		add_filter('woocommerce_add_cart_item_data', array($this,'gloo_checkout_add_custom_data_to_woocommerce_session'),1,2);
		add_filter('woocommerce_get_cart_item_from_session', array($this,'gloo_checkout_get_user_custom_data_session'), 1, 3 );
		 
		add_filter( 'woocommerce_get_item_data', array($this,'display_cart_item_custom_meta_data'), 10, 2 );
		//add_action('woocommerce_add_order_item_meta', array($this,'gloo_checkout_add_custom_data_to_order_item_meta'),1,2);
		add_action('woocommerce_before_cart_item_quantity_zero', array($this,'wdm_remove_user_custom_data_options_from_cart'),1,1);
		add_action( 'woocommerce_after_order_itemmeta',  array($this,'gloo_checkout_user_custom_meta_customized_display'),10, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array($this,'gloo_checkout_hide_order_item_user_custom_meta_field') );

		add_action( 'woocommerce_checkout_create_order_line_item', array($this,'gloo_checkout_add_order_item_meta'), 10, 4 );
		add_action('woocommerce_checkout_create_order', array($this, 'checkout_anything_add_values_to_order_item_meta'));
		add_action('woocommerce_admin_order_data_after_order_details', array($this,'checkout_anything_display_order_data_in_admin'));

		add_action( 'woocommerce_email_before_order_table', array($this,'action_woocommerce_email_before_order_table'), 1, 4 );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array($this,'order_item_get_formatted_meta_data'), 10, 2 );

	}

	function order_item_get_formatted_meta_data($formatted_meta, $that) {

		$ref_name_globals_var = isset( $GLOBALS ) ? $GLOBALS : '';
		$email_id = isset( $ref_name_globals_var['email_id'] ) ? $ref_name_globals_var['email_id'] : '';

		if(!empty($email_id)) {
			$order = $that->get_order();
			$gloo_order_data = $order->get_meta('_gloo_order_data');
			$hidden_meta = array();
	
			if(!empty($gloo_order_data)) {
				$gloo_data = unserialize($gloo_order_data);
	
				foreach($gloo_data as $gloo_meta) {
					if( (isset($gloo_meta['send_with_email']) && $gloo_meta['send_with_email'] != 'yes') && (isset($gloo_meta['order_item_meta']) && $gloo_meta['order_item_meta'] == 'yes')) {
						$hidden_meta[] = $gloo_meta['label'];
					}	
				}
			}
	
			if(!empty($hidden_meta)) {
	
				$temp_metas = [];
				foreach($formatted_meta as $key => $meta) {
					if ( isset( $meta->key ) && ! in_array( $meta->key, $hidden_meta) ) {
						$temp_metas[ $key ] = $meta;
					}
				}
			}

			return $temp_metas;
		}

		return $formatted_meta;
	}

	function action_woocommerce_email_before_order_table( $order, $sent_to_admin, $plain_text, $email ) {
		$GLOBALS['email_id'] = $email->id;
	}
			
	function gloo_checkout_add_order_item_meta( $item, $cart_item_key, $values, $order ) {

		if(!empty($values['gloo_item_meta_data'])) {
			foreach( $values['gloo_item_meta_data'] as $meta ) {
				if(isset($meta['order_item_meta']) && $meta['order_item_meta'] == 'yes') {
					$item->add_meta_data( $meta['label'], $meta['value'] );
				}
			}
		}
	}

	function gloo_checkout_hide_order_item_user_custom_meta_field( $fields ) {
		$fields[] = 'gloo_checkout_order_meta'; //Add all the custom fields here in this array and it will not be displayed.
		return $fields;
	}

	function gloo_checkout_user_custom_meta_customized_display( $item_id, $item, $product ) {

		$all_meta_data = wc_get_order_item_meta( $item_id, 'gloo_checkout_order_meta', true);

		if(!empty($all_meta_data)) {
			$all_meta_data = unserialize($all_meta_data);

			if(!empty($all_meta_data)) {
				$html_string = "<table class='display_meta'>";
				foreach( $all_meta_data as $meta ) {
					$html_string .= "<tr>
						<th>" . $meta['label'] . "</th>
						<td>". $meta['value'] ."</td>
					</tr>";
				}
				$html_string .= "</table>";
			}
			echo $html_string;			
		}
	}

 	function gloo_checkout_remove_custom_datas_from_item_meta( $cart_item_key ) {
		global $woocommerce;

		// Get cart
		$cart = $woocommerce->cart->get_cart();
	
		// For each item in cart, if item is upsell of deleted product, delete it
		foreach( $cart as $key => $values) {
			if ( $values['gloo_item_meta_data'] == $cart_item_key )
				unset( $woocommerce->cart->cart_contents[ $key ] );
		}
	}

	// function gloo_checkout_add_custom_data_to_order_item_meta( $item_id, $values ) {
	// 	global $woocommerce,$wpdb;
	
	// 	if(!empty($values['gloo_item_meta_data'])) {
	// 		$gloo_checkout_user_custom_datas = $values['gloo_item_meta_data'];
	// 		wc_add_order_item_meta($item_id, 'gloo_checkout_order_meta', serialize($gloo_checkout_user_custom_datas));
	// 	}
	// }

	function display_gloo_meta() {
		// echo '<pre>'; print_r($data); echo '</pre>';
 			global $woocommerce;
			$items = $woocommerce->cart->get_cart();
			//echo '<pre>'; print_r($items); echo '</pre>';
  	}

	function display_cart_item_custom_meta_data( $item_data, $cart_item ) {
		
		if(is_checkout()) {
			$current_page = 'checkout';
		} elseif(is_cart()) {
			$current_page = 'cart';
		} 
		
		if(isset($cart_item['gloo_item_meta_data']) && !empty($cart_item['gloo_item_meta_data'])) {
			foreach( $cart_item['gloo_item_meta_data'] as $meta ) {

				$show_order_meta = $meta['show_order_meta'];

				if($show_order_meta == 'yes') {
					$show_meta = $meta['show_meta'];

					if($show_meta != 'cart_checkout' && $show_meta['show_order_meta']) {
						if($show_meta != $current_page ) {
							continue;
						}
					}
					
					if(isset($meta['order_item_meta']) && $meta['order_item_meta'] == 'yes') {
						$item_data[] = array(
							'key'       => $meta['label'],
							'value'     => $meta['value'], 
						);
					}
				}
 			}
		}

		return $item_data;
	}
	
	// function gloo_checkout_display_custom_data_in_cart( $product_name, $values, $cart_item_key ) {
	// 	global $wpdb;

	// 	if(!empty($values['gloo_item_meta_data'])) {
	// 		$return_string = '<table>';
			
	// 		foreach( $values['gloo_item_meta_data'] as $meta ) {
	// 			$return_string .='<tr>
	// 			<th>' . $meta['label'] . '</th>'
	// 			.'<td>' . $meta['value'] . '</td>
	// 			</tr>';
	// 		}
			
	// 		$return_string .= '</table>';
	// 		return $return_string;

	// 	}
	// }

	function wdm_remove_user_custom_data_options_from_cart($cart_item_key)
    {
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values) {
        if ( $values['gloo_item_meta_data'] == $cart_item_key )
            unset( $woocommerce->cart->cart_contents[ $key ] );
        }
    }


	function gloo_checkout_get_user_custom_data_session( $item, $values, $key ) {
		// echo '<pre>'; print_r($values); echo '</pre>';
		//Check if the key exist and add it to item variable.
		if (array_key_exists( 'gloo_item_meta_data', $values ) ) {
			$item['gloo_item_meta_data'] = $values['gloo_item_meta_data'];
		}

		return $item;
	}
	
	function gloo_checkout_add_custom_data_to_woocommerce_session( $cart_item_data, $product_id ) {

		global $woocommerce;
		$gloo_meta = WC()->session->get( 'gloo_order_meta' ); // Get custom data from session
		//Unset our custom session variable
		$gloo_ca_prices = WC()->session->get( 'gloo_ca_prices' ); // Get custom data from session
		
		$options = array();

		if( ! empty( $gloo_ca_prices ) ) {
			$cart_item_data['gloo_el_field_price'] = $gloo_ca_prices['price'];
		}

		if(empty($gloo_meta)) {
			return $cart_item_data;
		} else { 
 
			$options['gloo_item_meta_data'] = $gloo_meta;
			//WC()->session->__unset( 'gloo_order_meta' ); // Remove session variable
			if(empty($cart_item_data)) {
				return $options;
			} else {
				return array_merge($cart_item_data, $options);
			}
		}
 	}

	function checkout_anything_add_values_to_order_item_meta($order) {
		$gloo_meta = WC()->session->get( 'gloo_order_meta' ); // Get custom data from session

		if(!empty($gloo_meta)) {
			foreach($gloo_meta as $key => $meta) {
				$order_item_meta = $meta['order_item_meta'];

				$order->update_meta_data( '_gloo_order_data', serialize($gloo_meta) );

				if($order_item_meta != 'yes') {
					if((isset($meta['is_relation_field']) && $meta['is_relation_field'] == 'acf') && !empty($meta['value'])) {

						if(isset($meta['is_relation_key']) && !empty($meta['is_relation_key'])) {
							$arr_values = explode(',', $meta['value']);
		
							if(!empty($arr_values) && is_array($arr_values)) {
								$value = $arr_values;
								$order->update_meta_data( $meta['is_relation_key'], $value );
							}
						}
	
					} else {
						$this->gloo_meta_keys[] = $key;
						$order->update_meta_data( $key, $meta );	
					}
				}
			}

			if(!empty($this->gloo_meta_keys)) {
				$order->update_meta_data( $this->gloo_meta_key_id, $this->gloo_meta_keys );
			}
		}
 	
	//	WC()->session->__unset( 'gloo_order_meta' ); // Remove session variable
	}
  
	// display the extra data in the order admin panel
	function checkout_anything_display_order_data_in_admin( $order ) { 
		$order_meta_keys = get_post_meta( $order->id, '_gloo_order_meta_keys', true ); 
		$output = '';

		if(!empty($order_meta_keys) && is_array($order_meta_keys)) {
			foreach($order_meta_keys as $field_key) {

				$gloo_meta = get_post_meta( $order->id, $field_key, true );  
 
				if(!isset($gloo_meta['is_relation_field'])) {
					$output .= '<p><strong>' . $gloo_meta['label'] . ' : </strong>' . $gloo_meta['value'] . '</p>';
				}
			}
		} 
		
		if(!empty($output)) : ?>
			<div class="gloo-meta form-field form-field-wide ">
				<h3><?php _e( 'Extra Details' ); ?></h3>
				<?php echo $output; ?>
			</div>
		<?php endif; ?>
	<?php }

	function checkout_anything_cart_item_price($cart) {
		if (is_admin() && !defined('DOING_AJAX'))
			return;

		// Must be required since Woocommerce version 3.2 for cart items properties changes
		if (did_action('woocommerce_before_calculate_totals') >= 2)
			return;

		foreach ($cart->get_cart() as $cart_item) {
			if( isset($cart_item['gloo_el_field_price']) && !empty($cart_item['gloo_el_field_price']) ) {
				$cart_item['data']->set_price($cart_item['gloo_el_field_price']);
			}
		}
	}

	function gloo_change_cart_table_price_display( $price, $values, $cart_item_key ) {
	
		if( isset($values['gloo_el_field_price']) && !empty($values['gloo_el_field_price']) ) {
			$price = wc_price($values['gloo_el_field_price']);
		}
		return $price;
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		require gloo()->modules_path( 'checkout-anything/inc/autoload.php' );
		Plugin::instance();
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
