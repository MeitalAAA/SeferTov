<?php
namespace Gloo\Modules\ImageUploadUI;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin{

  private static $instance = null;
  public $prefix = 'gloo_form_image_upload_ui';
  static $options = array();

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){

    // self::$options = SerializeStringToArray(get_option('gloo_form_image_upload_ui_options'));
    // $ver = "0.1";
    // $default_values = array('ver' => $ver);
    // self::$options = array_merge($default_values, self::$options);

    PluginDefault::instance();

  }// construct function end here

  /******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

} // BBWP_CustomFields class