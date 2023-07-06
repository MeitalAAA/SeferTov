<?php
namespace Gloo\JSFPaginationWidget;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_jsf_pagination_widget';
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
    
    /*if(!(isset(self::$options['input_element_class']) && self::$options['input_element_class'])){
      $this->set_option('input_element_class', 'autocomplete_address');
    }*/
      
    
    add_action( 'init', [ $this, 'init' ] );

    if(is_admin()){

      //$PageSettings = new PageSettings();

      // add javascript and css to wp-admin dashboard.
      //add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

      //add settings page link to plugin activation page.
      //add_filter( 'plugin_action_links_'.plugin_basename(gloo_jsf_pagination_widget_PLUGIN_FILE), array($this, 'plugin_action_links') );

      // Plugin activation hook
      //register_activation_hook(plugin_basename(gloo_jsf_pagination_widget_PLUGIN_FILE), array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(plugin_basename(gloo_jsf_pagination_widget_PLUGIN_FILE), array($this, 'PluginDeactivation'));

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
  public function wp_admin_style_scripts() {

    /*if(is_admin()){
      wp_enqueue_script( 'postbox' );
    }
  
    wp_register_script( $this->prefix('script'), plugin_dir_url(gloo_jsf_pagination_widget_PLUGIN_FILE) . 'assets/admin/js/script.js', array(), '1.0');
    wp_enqueue_script( $this->prefix('script') );
    
    $js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );
*/

  }// wp_admin_style_scripts


  /******************************************/
  /***** add javascript and css to front end. **********/
  /******************************************/
  /*public function wp_style_scripts() {

    wp_register_style( $this->prefix('style'), plugin_dir_url(gloo_jsf_pagination_widget_PLUGIN_FILE) . 'assets/css/style.css', array(), '1.0.0' );
    wp_enqueue_style($this->prefix('style'));

    wp_register_script( $this->prefix('script'), plugin_dir_url(gloo_jsf_pagination_widget_PLUGIN_FILE) . 'assets/js/script.js', array(), '1.0');
    wp_enqueue_script( $this->prefix('script') );


    $js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );

  }*/
  


  /******************************************/
  /***** Check if elementor is loaded. **********/
  /******************************************/
  public function is_compatible() {
    
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
      $this->message = __('JSF Pagination Widget require Elementor Pro to be installed and active.', 'gloo_for_elementor');
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
  public function init() {
    
    if ( $this->is_compatible() ) {
      add_action( 'elementor/widgets/widgets_registered', array($this, 'widgets_registered'));
    }

    /*add_action( 'template_redirect', function() {
        
      global $wp_query;
      $page = get_query_var('paged');
      //$page = ( int ) $wp_query->get( 'page' );
      if ( $page > 1 ) {
          // convert 'page' to 'paged'
          //$wp_query->set( 'page', 1 );
          //$wp_query->set( 'paged', $page );

          // prevent redirect
          remove_action( 'template_redirect', 'redirect_canonical' );
      }      

    }, 0 ); */

    add_filter( 'jet-engine/listing/grid/posts-query-args', function($args, $widget){
      if ( 'jet-listing-grid' !== $widget->get_name() ) {
        return $args;
      }
      
      $settings = $widget->get_settings();
    
      if ( empty( $settings['_element_id'] ) ) {
        $query_id = 'default';
      } else {
        $query_id = $settings['_element_id'];
      }
      
      $paged = get_query_var('paged');
      $active_pagination_ids = SerializeStringToArray(get_option('active_pagination_ids'));
      if(in_array($query_id, $active_pagination_ids)){
        //db($_REQUEST['jet-smart-filters']);
        if($args['posts_per_page'] && $paged >= 2){
          $args['paged'] = $paged;
          $offset = ((($args['paged'] - 1) * $args['posts_per_page'] ) + 1);
          $args['offset'] = $offset;
        }
      }
      
      return $args;
    }, 11, 2 );
    
  }
  

  public function widgets_registered(){
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new JSF_Pagination_Widget() );
  }

  
} // BBWP_CustomFields class

