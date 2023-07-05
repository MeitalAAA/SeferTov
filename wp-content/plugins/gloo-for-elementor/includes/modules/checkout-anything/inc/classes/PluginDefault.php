<?php
namespace Gloo\Modules\CheckoutAnything;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	
	public $element_types = [
		'section',
		'column',
		'widget',
	];

	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    
		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

		add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );

		// add_action( "elementor/element/after_section_end", array( $this, 'register_controls' ), 11, 2 );

  }// construct function end here

	  /******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
		// new \Gloo\Modules\CheckoutAnything\Field_Wysiwyg();
		// new RepeaterField();
		// new RepeaterStartField();
		// new RepeaterEndField();
    
    // Instantiate the action class
    $CheckoutFormSubmitAction = new CheckoutFormSubmitAction('checkout_anything', 'gca1');
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $CheckoutFormSubmitAction->get_name(), $CheckoutFormSubmitAction );
		
    // $quantity = 1;
    // for($i = 1; $i <= $quantity; $i++){
    //   $SalesForceAfterSubmitAPI = new SalesForceAfterSubmitAPI('salesforceapi', $i);
    //   \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $SalesForceAfterSubmitAPI->get_name(), $SalesForceAfterSubmitAPI );
    // }

		

  }

	public function register_controls( $element, $section ) {
		
		if ( empty( $element ) ) {
			return;
		}

		if ( ! in_array( $element->get_type(), $this->element_types ) ) {
			return;
		}
		// db($section);
	}

} // BBWP_CustomFields class


