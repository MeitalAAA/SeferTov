<?php
namespace Gloo\Modules\Form_Actions_Pro;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin{

  private static $instance = null;

  public $prefix = 'form_actions_pro';
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


		PluginDefault::instance();



    if(is_admin()){

      // add javascript and css to wp-admin dashboard.
      // add_action( 'admin_enqueue_scripts', array($this, 'wp_style_scripts') );

      //add settings page link to plugin activation page.
      // add_filter( 'plugin_action_links_'.BBWP_FLUID_DYNAMICS_PLUGIN_FILE, array($this, 'plugin_action_links') );

      // Plugin activation hook
      // register_activation_hook(BBWP_FLUID_DYNAMICS_PLUGIN_FILE, array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(BBWP_FLUID_DYNAMICS_PLUGIN_FILE, array($this, 'PluginDeactivation'));

		}else{
      // add javascript and css to front end.
      // add_action( 'wp_enqueue_scripts', array($this, 'wp_style_scripts') );
    }

  }// construct function end here


	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.



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
      update_option($this->prefix.'_options', ArrayToSerializeString(self::$options));

	}// set_option



	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_style_scripts() {


    // wp_register_script( $this->prefix('script'),  gloo()->plugin_url( 'includes/modules/form-actions-pro/js/script.js'), array(), '1.0');
    // wp_enqueue_script( $this->prefix('script') );

    // $js_variables = array(
    //   'input_element_class' => $this->get_option('input_element_class'),
    //   'supported_countries' => SerializeStringToArray($this->get_option('supported_countries')),
    // );
    // wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );




  }// wp_style_scripts


  /******************************************/
  /***** Check if elementor is loaded. **********/
  /******************************************/
  public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			//add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}
		return true;

  }

  /******************************************/
  /***** plugin_url functions **********/
  /******************************************/
  public function plugin_url() {    
    return trailingslashit(gloo()->plugin_url( 'includes/modules/form-actions-pro/'));
  }

  public function plugin_path() {
    return trailingslashit(gloo()->plugin_path('includes/modules/form-actions-pro'));
  }

  public function modules_path( $path = null ) {
    return trailingslashit(gloo()->plugin_path('includes/modules'));
  }
} // BBWP_CustomFields class