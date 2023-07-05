<?php
namespace Gloo\Modules\Powerlink_Form_Action;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PowerlinkFormSetup {

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_powerlink_submit_action';
  static $options = array();


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

    add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );

  }// construct function end here
  

  /******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
    // Instantiate the action class
    $PowerlinkForceAfterSubmit = new PowerlinkFormAfterSubmit();
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $PowerlinkForceAfterSubmit->get_name(), $PowerlinkForceAfterSubmit );
    
  }
 
} // BBWP_CustomFields class