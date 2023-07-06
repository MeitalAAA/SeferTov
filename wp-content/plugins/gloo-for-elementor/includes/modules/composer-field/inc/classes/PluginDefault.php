<?php
namespace Gloo\Modules\ComposerField;

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
    
		
      add_action( 'elementor_pro/init', function() {
        new Field_Composer();
      });

		// add javascript and css to wp-admin dashboard.
		// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

			// add_action('wp_footer', array($this, 'wp_footer'));
  }// construct function end here



	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      wp_register_style( $this->prefix.'_wp_admin_css', bbwp_engine()->plugin_url().'modules/db-backup/css/style.css', array(), '1.0.0' );
      wp_enqueue_style($this->prefix.'_wp_admin_css');

      wp_register_script( $this->prefix.'_wp_admin_script', bbwp_engine()->plugin_url().'modules/db-backup/js/script.js', array('jquery'), '1.0.0' );
      //wp_enqueue_script( $this->prefix.'_wp_admin_script' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );

		}

  }// wp_admin_style_scripts

	public function wp_footer(){
		?>
		<style>
			.elementor-field-type-gloo_composer_field.elementor-field-group.elementor-column {
				flex-direction: column;
				align-items: flex-start;
			}
		</style>
		<?php
	}
	
} // BBWP_CustomFields class

