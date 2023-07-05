<?php

namespace Gloo\Modules\Woocommerce_Discount_Widget;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-woocommerce-discount';

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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_discount_widget']);

	}

	public function register_discount_widget() {

		include_once( gloo()->modules_path( 'woocommerce-discount/inc/widget-product-discount.php' ) );
			
		$widget_object = new Widget_Product_Discount();
		
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
