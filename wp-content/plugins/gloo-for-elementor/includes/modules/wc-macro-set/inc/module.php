<?php

namespace Gloo\Modules\WC_Macro_Set;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	public $control_overrides = [];

	public $slug = 'wc_macro_set';

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

		add_filter( 'jet-engine/listings/macros-list', [$this, 'add_macros'], 10 , 1);
        
    }
    
    public function add_macros( $macros_list ){
        $macros_list["le_wc_upsell"] = [ $this, "le_wc_upsell" ];
        $macros_list["le_wc_cross_sell"] = [ $this, "le_wc_cross_sell" ];

        return $macros_list;
    }

	public function le_wc_upsell() {
		$product = wc_get_product();
		if ( ! $product ) {
			return 0;
		}
		$upsell = $product->get_upsell_ids();
		if ( ! $upsell ) {
			return 0;
		}

		return implode( ",", $upsell );
	}

	public function le_wc_cross_sell() {
		$product = wc_get_product();
		if ( ! $product ) {
			return 0;
		}
		$cross_sell = $product->get_cross_sell_ids();
		if ( ! $cross_sell ) {
			return 0;
		}

		return implode( ",", $cross_sell );
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
