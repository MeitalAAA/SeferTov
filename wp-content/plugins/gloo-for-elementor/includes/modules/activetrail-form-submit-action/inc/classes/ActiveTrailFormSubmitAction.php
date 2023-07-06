<?php
namespace Gloo\Modules\ActiveTrailFormSubmitAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActiveTrailFormSubmitAction{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_activetrail_form_submit_action';
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

    // get the plugin options/settings.
    self::$options = SerializeStringToArray(get_option($this->prefix('options')));
    /*if(!isset(self::$options['zoho_data_center']))
      $this->set_option('zoho_data_center', 'com');*/
      
    if(is_admin()){

      //$PageSettings = new PageSettings();

      // add javascript and css to wp-admin dashboard.
      //add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

      //add settings page link to plugin activation page.
      //add_filter( 'plugin_action_links_'.plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'plugin_action_links') );

      // Plugin activation hook
      //register_activation_hook(plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'PluginDeactivation'));

		}else{
      // add javascript and css to front end.
      //add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
    }

    add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );


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
	/*public function plugins_loaded(){

		load_plugin_textdomain( 'gloo', false, plugin_dir_path(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE) . 'languages/' );

    if ( $this->is_compatible() ) {
      add_action( 'elementor/init', [ $this, 'elementor_init' ] );
      //add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );
    }
    

  }*/
  // plugin_loaded


	/******************************************/
	/***** add settings page link in plugin activation screen.**********/
	/******************************************/
  /*public function plugin_action_links( $links ) {

     $links[] = '<a href="'. esc_url(get_admin_url(null, 'tools.php?page='.$this->prefix)) .'">'.__('Settings', 'gloo').'</a>';
     return $links;

  }*/
  // localization function


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
      $this->message = __('OTW Elementor Form CRM require Elementor Pro to be installed and active.', 'gloo_for_elementor');
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
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
    // Instantiate the action class
    $ActiveTrailFormSubmitActionClass = new ActiveTrailFormSubmitActionClass();
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ActiveTrailFormSubmitActionClass->get_name(), $ActiveTrailFormSubmitActionClass );
  
  }
  
}

