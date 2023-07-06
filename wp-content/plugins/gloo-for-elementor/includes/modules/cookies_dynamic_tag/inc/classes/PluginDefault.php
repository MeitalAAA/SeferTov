<?php
namespace Gloo\Modules\CookiesDynamicTag;

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
    
		if(!is_admin()){
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			// add javascript and css to wp-admin dashboard.
			// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}

		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }
		// add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );
  }// construct function end here


	public function register_dynamic_tags( $dynamic_tags ) {
		$dynamic_tags->register_tag( "Gloo\Modules\CookiesDynamicTag\CookieTag" );
	}

	/******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {

  }


	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(!is_admin()){
      $script_abs_path = gloo()->plugin_path( 'includes/modules/cookies_dynamic_tag/assets/frontend/js/script.js');
      wp_register_script( $this->prefix, gloo()->plugin_url().'includes/modules/cookies_dynamic_tag/assets/frontend/js/script.js', array('jquery'), get_file_time($script_abs_path));
      // wp_enqueue_script( $this->prefix );
    }
  }// wp_admin_style_scripts


	
} // BBWP_CustomFields class

