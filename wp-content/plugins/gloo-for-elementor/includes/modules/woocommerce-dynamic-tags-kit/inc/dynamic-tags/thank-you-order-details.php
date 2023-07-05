<?php

namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

class Thank_You_Order_Details extends \Elementor\Core\DynamicTags\Data_Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'gloo-wdt-thankyou-order-details';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Thank You Page Order Details', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
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

		$this->add_control(
			'notice',
			[
				'raw'             => __( 'This dynamic tag only works in the WooCommerce Thank You page context that is shown after a customer places an order.', 'gloo_for_elementor' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);


		$this->add_control(
			'order_details',
			array(
				'label'   => __( 'Details', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'id'                   => 'ID',
					'parent_id'            => 'Parent ID',
					'status'               => 'Status',
					'currency'             => 'Currency',
					'version'              => 'Version',
					'prices_include_tax'   => 'Prices Including Tax',
					'date_created'         => 'Date Created',
					'date_modified'        => 'Date Modified',
					'date_paid'            => 'Date Paid',
					'date_completed'       => 'Date Completed',
					'discount_total'       => 'Discount Total',
					'discount_tax'         => 'Discount Tax',
					'shipping_total'       => 'Shipping Total',
					'shipping_tax'         => 'Shipping Tax',
					'cart_tax'             => 'Cart Total',
					'total'                => 'Total',
					'total_tax'            => 'Total Tax',
					'customer_id'          => 'Customer ID',
					'order_key'            => 'Order Key',
					'billing'              => 'Billing Details (Select)',
					'shipping'             => 'Shipping Details (Select)',
					'payment_method'       => 'Payment Method',
					'payment_method_title' => 'Payment Method Title',
					'customer_ip_address'  => 'Customer IP Address',
					'customer_user_agent'  => 'Customer User Agent',
				],
			)
		);

		$shipping_details = [
			'first_name' => 'First Name',
			'last_name'  => 'Last Name',
			'company'    => 'Company',
			'address_1'  => 'Address Line 1',
			'address_2'  => 'Address Line 2',
			'city'       => 'City',
			'state'      => 'State',
			'postcode'   => 'Post Code',
			'country'    => 'Country',
		];

		$billing_details = array_merge( $shipping_details, [
			'email' => 'Email',
			'phone' => 'Phone',
		] );

		$this->add_control(
			'order_billing_details',
			array(
				'label'     => __( 'Billing Details', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $billing_details,
				'condition' => [
					'order_details' => 'billing'
				],
			)
		);

		$this->add_control(
			'order_shipping_details',
			array(
				'label'     => __( 'Shipping Details', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $shipping_details,
				'condition' => [
					'order_details' => 'shipping'
				],
			)
		);


		$default_date_format = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'd-m-Y';
		$this->add_control(
			'date',
			array(
				'label'       => __( 'Date Format', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => $default_date_format,
				'condition'   => [
					'order_details' => [ 'date_completed', 'date_paid', 'date_modified', 'date_created' ],
				],

			)
		);

	}

	public function get_value( array $options = array() ) {

		$order_details = $this->get_settings_for_display( 'order_details' );

		if ( ! $order_details ) {
			return;
		}

		if (  is_user_logged_in() )  {
			
			$user_id = get_current_user_id();
			$customer = new \WC_Customer( $user_id );
			$last_order = $customer->get_last_order();

			if(!empty($last_order)) {
				$order_data = $last_order->get_data();
			}

		} else {

			if( isset( $_COOKIE['myslika_order_id'] )  && !empty( $_COOKIE['myslika_order_id'] ) ) {
				$order = wc_get_order( $_COOKIE['myslika_order_id'] );
		 		$order_data = $order->get_data();
			}
		}

		$value = isset( $order_data[ $order_details ] ) ? $order_data[ $order_details ] : false;

		// billing and shipping info
		if ( ($order_details === 'billing' || $order_details === 'shipping') && !empty($value)) {
			$order_address_details = $this->get_settings_for_display( "order_{$order_details}_details" );
			if ( $value && is_array( $value ) ) {
				$value = isset( $value[ $order_address_details ] ) ? $value[ $order_address_details ] : false;
			}
		}

		// date info
		if ( ($order_details === 'date_created' || $order_details === 'date_modified' || $order_details === 'date_paid' || $order_details === 'date_completed') && !empty($value) ) {
			$default_date_format = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'd-m-Y';
			$value               = $value->date( $default_date_format );
		}

		return $value;

	}

}