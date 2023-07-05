<?php
namespace Gloo\Modules\SignatureField;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin{

  private static $instance = null;
  public $prefix = 'signature_field';
  static $options = array();

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){

    self::$options = JsonStringToArray(get_option('signature_field_options'));
    $ver = "0.1";
    $default_values = array('ver' => $ver);
    self::$options = array_merge($default_values, self::$options);

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

  /******************************************/
  /***** get option function**********/
  /******************************************/
  public function get_option($key){

    if(isset(self::$options[$key]))
      return self::$options[$key];
    else
      return NULL;

  }// get_option


	/******************************************/
  /***** get option function **********/
  /******************************************/
  public function set_option($key, $value){

		self::$options[$key] = $value;
		update_option($this->prefix.'_options', ArrayToJsonString(self::$options));
	}// set_option

  /******************************************/
  /***** get option function **********/
  /******************************************/
  public function update_option($key, $value){

		$this->set_option($key, $value);

	}// set_option

  /******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.

} // BBWP_CustomFields class