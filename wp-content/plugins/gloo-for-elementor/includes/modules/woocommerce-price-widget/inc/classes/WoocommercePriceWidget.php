<?php
namespace OTW\WoocommercePriceWidget;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WoocommercePriceWidget{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'otw_woocommerce_price_widget';
  static $options = array();

  public static $allowedWidgets = array();

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
    
		// get the plugin options/settings.
    self::$options = SerializeStringToArray(get_option($this->prefix('options')));
    
    
    if ( $this->is_compatible() ) {
      add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
    }
    //localization hook
    //add_action( 'init', array($this, 'plugins_loaded') );
    add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
    
    //add_action( 'init', [ $this, 'init' ] );

    if(is_admin()){

      //$PageSettings = new PageSettings();

      // add javascript and css to wp-admin dashboard.
      //add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

      //add settings page link to plugin activation page.
      //add_filter( 'plugin_action_links_'.plugin_basename(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE), array($this, 'plugin_action_links') );

      // Plugin activation hook
      //register_activation_hook(plugin_basename(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE), array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(plugin_basename(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE), array($this, 'PluginDeactivation'));

		}else{
      // add javascript and css to front end.
      //add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
    }

  }// construct function end here


	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.


	/******************************************/
	/***** localization function **********/
	/******************************************/
	public function plugins_loaded(){
    
    load_plugin_textdomain( 'gloo_for_elementor', false, gloo()->modules_path( 'woocommerce-price-widget/languages/'));
    
    //if ( $this->is_compatible() ) {
     
      //add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
    
    //}
    
	}// plugin_loaded


	/******************************************/
	/***** add settings page link in plugin activation screen.**********/
	/******************************************/
  /*public function plugin_action_links( $links ) {

     $links[] = '<a href="'. esc_url(get_admin_url(null, 'options-general.php?page='.$this->prefix)) .'">'.__('Settings', 'gloo_for_elementor').'</a>';
     return $links;

  }*/
  // localization function


	/******************************************/
  /***** Plugin activation function **********/
  /******************************************/
  /*public function PluginActivation() {

		global $wpdb;
		
    $ver = "1.0.0";
    if(!(isset(self::$options['ver']) && self::$options['ver'] == $ver))
      $this->set_option('ver', $ver);


  }// plugin activation
*/

	/******************************************/
  /***** plugin deactivation function **********/
  /******************************************/
  //public function PluginDeactivation(){
    
  //}// plugin deactivation
  

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
  /***** Check if elementor is loaded. **********/
  /******************************************/
  public function is_compatible() {
    
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
      $this->message = __('OTW Woocommerce Price + Widget require Elementor and woocommerce to be installed and active.', 'gloo_for_elementor');
      $this->messageClass = 'warning';
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			return false;
		}
    return true;
    
  }
  


  /******************************************/
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notices() {
    //Value of $class can be error, success, warning and info
    if($this->message && $this->messageClass){ 
      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-'.$this->messageClass.' is-dismissible' ), esc_html( $this->message ) );
    }
  }
  
  /******************************************/
  /***** Intialize the elementor and other plugins extended classes and functions. **********/
  /******************************************/
  public function init_widgets() {

    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new PriceWidget() );

  }

  
} // BBWP_CustomFields class