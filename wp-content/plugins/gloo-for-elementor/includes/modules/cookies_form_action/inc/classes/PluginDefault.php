<?php
namespace Gloo\Modules\CookiesFormAction;

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
		add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );
		
  }// construct function end here


	/******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
    $quantity = 1;
    for($i = 1; $i <= $quantity; $i++){
      $CookieFormAction = new CookieFormAction('gloo_cookie_form_action', $i);
      \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $CookieFormAction->get_name(), $CookieFormAction );
    }


    // add_action( 'elementor-pro/forms/pre_render', [ $this, 'forms_pre_render' ], 10, 2 );

    add_action( 'elementor/frontend/before_render', [ $this, 'before_render_content' ], 10, 1);

  }

  public function forms_pre_render( $instance, $form ) {
    $form->add_render_attribute( 'field-group' . $item_index, 'data-field_type', $field['field_type'] );
  }

  public function before_render_content($widget){
		if($widget->get_name() == 'form') {
      //form_name
      $submit_actions = $widget->get_settings( 'submit_actions' );
      if ($submit_actions && in_array( $this->prefix.'1', $submit_actions ) ) {
        
        $data_settings = array(
          'cookie_type' => $widget->get_settings($this->prefix.'1_cookie_type'),
          'individual_fields' => '',
        );
        $form_field_list_values = array();
        $individual_fields = $widget->get_settings($this->prefix.'1_individual_fields');
        if($individual_fields == 'yes'){
          $form_field_list = $widget->get_settings($this->prefix.'1_form_field_list');
          if (is_array($form_field_list) && count($form_field_list) >= 1 ) {
            foreach (  $form_field_list as $item ) {
              if(isset($item['form_field_id']) && $item['form_field_id'])
                $form_field_list_values[] = $item['form_field_id'];
            }
          }
        }
        $data_settings['individual_fields'] = $individual_fields;
        $data_settings['form_field_list'] = $form_field_list_values;
      
        $widget->add_render_attribute( '_wrapper', 'data-gloo_cookies_form_action', json_encode($data_settings) );
        $widget->add_render_attribute( '_wrapper', 'class', $this->prefix );

        wp_enqueue_script( $this->prefix );
      }
    }
  }

	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(!is_admin()){
      $script_abs_path = gloo()->plugin_path( 'includes/modules/cookies_form_action/assets/frontend/js/script.js');
      wp_register_script( $this->prefix, gloo()->plugin_url().'includes/modules/cookies_form_action/assets/frontend/js/script.js', array('jquery'), get_file_time($script_abs_path));
      // wp_enqueue_script( 'gloo_otp_action' );
    }
  }// wp_admin_style_scripts


	
} // BBWP_CustomFields class

