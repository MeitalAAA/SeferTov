<?php
namespace Gloo\Modules\SignatureField;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	
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
    
		// add javascript and css to wp-admin dashboard.
		add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

		add_action( 'elementor_pro/init', function() {
			new Field_Signature();
		});

		
  }// construct function end here



	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

		wp_register_script( 'gloo_signature_field_lib', 'https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js', array('jquery'));
		$script_abs_path = gloo()->plugin_path( 'includes/modules/signature-field/assets/frontend/js/script.js');
		wp_register_script( 'gloo_signature_field',  gloo()->plugin_url( 'includes/modules/signature-field/assets/frontend/js/script.js'), array('gloo_signature_field_lib'), get_file_time($script_abs_path));

  }// wp_admin_style_scripts


	
} // BBWP_CustomFields class

