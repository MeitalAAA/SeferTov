<?php
namespace Gloo\Modules\AjaxReloadPrevention;

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
		// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }
		// alert('test');

		if(is_admin()) {
			add_action( 'elementor/element/before_section_end', [ $this, 'elementor_form_widgets_modifier'], 10, 2);	
		}
		add_action( 'elementor/frontend/before_render', [ $this, 'before_render_content' ], 10, 1);

		// print out js
		add_action( 'wp_footer', [ $this, 'wp_footer' ]);
		
  }// construct function end here


	public function elementor_form_widgets_modifier($element, $section_id) {
		if($element->get_name() != 'form')
			return;

		// $element->start_injection( [
		// 	'type' => 'section',
		// 	'at' => 'end',
		// 	'of' => $section_id,
		// ] );

		$element->start_injection( [
			'type' => 'control',
			'at' => 'after',
			'of' => 'input_size',
		] );
		
		$element->add_control(
			'gloo_keep_form_data_after_submission',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __( 'Keep input data after submission?', 'gloo_for_elementor' ),
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);
		$element->end_injection();
		
	}

	public function before_render_content($widget){

		if($widget->get_name() == 'form') {
			if($widget->get_settings('gloo_keep_form_data_after_submission') == 'yes') {
				$widget->add_render_attribute( '_wrapper', 'class', 'gloo_keep_input_data' );
			}
		}
  }

	public function wp_footer() {?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				if($(".gloo_keep_input_data .elementor-form").length >= 1){
					$('body').on('reset', ".gloo_keep_input_data .elementor-form", function(){
						return false;
					});
				}
			});
		</script>
		<?php 
	}



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


	
} // BBWP_CustomFields class

