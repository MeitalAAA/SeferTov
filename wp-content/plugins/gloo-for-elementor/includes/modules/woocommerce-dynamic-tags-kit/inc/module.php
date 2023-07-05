<?php

namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'wooocommerce-dynamic-tags';

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
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );
	}


	public function register_dynamic_tags( $dynamic_tags ) {

		// Include the Dynamic tags
		foreach ( glob( gloo()->modules_path( 'woocommerce-dynamic-tags-kit/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'Product_Attribute_Tag',
			'Product_Gallery_Tag',
			'Product_Gallery_Image_Tag',
			'Backorder_Products',
			'Downloadable_Products',
			'Virtual_Products',
			'Catalog_Visibility_Products',
			'Thank_You_Order_Details',
			'ActiveSubscriptions',
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\WooCommerce_Dynamic_Tags_Kit\\{$class}" );
		}

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