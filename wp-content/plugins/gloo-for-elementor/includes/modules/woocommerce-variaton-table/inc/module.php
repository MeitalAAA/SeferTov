<?php

namespace Gloo\Modules\Woo_Variaton_Table;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-woocommerce-variation-table';

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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_table_widget']);

	}


	public function register_table_widget() {

		include_once( gloo()->modules_path( 'woocommerce-variaton-table/inc/widget-variation-table.php' ) );
		$widget_object = new Variation_Table_Widget();
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
