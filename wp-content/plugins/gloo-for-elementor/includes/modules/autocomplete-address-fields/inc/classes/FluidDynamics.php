<?php
namespace ByteBunch\FluidDynamics;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FluidDynamics{

  private static $instance = null;

  public $prefix = 'gloo_autocomplete_address';
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
    self::$options = SerializeStringToArray(get_option($this->prefix.'_options'));
    
    
    if(!(isset(self::$options['input_element_class']) && self::$options['input_element_class'])){
      $this->set_option('input_element_class', 'autocomplete_address');
    }
      
    add_action('wp_head', [ $this, 'wp_head' ]);

    if ( $this->is_compatible() ) {
      add_filter( 'elementor_pro/forms/render/item', [ $this, 'addPattern' ], 10, 3 );
      
      add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'addAutocompleteAddressFieldControl' ], 100, 2 );
    }
    
    if(is_admin()){

      $PageSettings = new PageSettings();

      //localization hook
      //add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
      add_action( 'init', array($this, 'plugins_loaded') );


      // add javascript and css to wp-admin dashboard.
      add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );


		}else{
      // add javascript and css to front end.
      add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
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

		load_plugin_textdomain( 'gloo_for_elementor', false, gloo()->modules_path( 'autocomplete-address-fields/languages/') );

    if ( $this->is_compatible() ) {
      //add_action( 'elementor/init', [ $this, 'init' ] );
      
    }
    

	}// plugin_loaded


	/******************************************/
	/***** add settings page link in plugin activation screen.**********/
	/******************************************/
  public function plugin_action_links( $links ) {

     $links[] = '<a href="'. esc_url(get_admin_url(null, 'options-general.php?page='.$this->prefix)) .'">'.__('Settings', 'gloo_for_elementor').'</a>';
     return $links;

  }// localization function


	/******************************************/
  /***** Plugin activation function **********/
  /******************************************/
  public function PluginActivation() {

		global $wpdb;
		
    $ver = "1.0";
    if(!(isset(self::$options['ver']) && self::$options['ver'] == $ver))
      $this->set_option('ver', $ver);

    

  }// plugin activation


	/******************************************/
  /***** plugin deactivation function **********/
  /******************************************/
  public function PluginDeactivation(){
    
  }// plugin deactivation
  

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
  /***** Admin notices. **********/
  /******************************************/
  public function adminNotices() {

    $message = 'save';
    $divClasses = "";
    $output = '';

    if($message == 'save'){
      $output = __('Your setting have been saved.', 'gloo_for_elementor');
      $divClasses = " notice-success";
    }
      echo '<div class="notice is-dismissible'.$divClasses.'"><p>'.$output.'</p></div>';

  }

  /******************************************/
  /***** Admin notices. **********/
  /******************************************/
  public function wp_head() {
    ?>
        <style>
            div.pac-container {
              z-index: 99999999999 !important;
            }
            div.pac-container div.pac-item:hover {
              display:block!important;
            }
        </style>
    <?php
}


	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(is_admin()){
      wp_enqueue_script( 'postbox' );

      wp_register_style( 'gloo_select2_css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
      wp_enqueue_style('gloo_select2_css');

      wp_register_script( 'gloo_select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array('jquery'));
      wp_enqueue_script('gloo_select2_js');

      wp_register_script( $this->prefix('script'),  gloo()->plugin_url( 'includes/modules/autocomplete-address-fields/js/script.js'), array('gloo_select2_js'), '1.0');
    }
    else{
      $script_abs_path = gloo()->plugin_path( 'includes/modules/autocomplete-address-fields/js/script.js');
      wp_register_script( $this->prefix('script'),  gloo()->plugin_url( 'includes/modules/autocomplete-address-fields/js/script.js'), array('jquery'), get_file_time($script_abs_path));
    }

    $load = false;
    if($this->get_option('load_on_pages')){
      $pages = $this->get_option('load_on_pages');
      $pages = explode(",", $pages);
      foreach($pages as $page){
        if(is_page(trim($page)) || is_single(trim($page))){
          $load = true;
          break;
        }
      }
    }
    else
      $load = true;


    if($this->get_option('disable_google_maps_js'))
      $load = false;
      

    $supported_countries = SerializeStringToArray($this->get_option('supported_countries'));
    $regions = '';
    if($this->get_option('enable_regions_only') && !empty($supported_countries) && is_array($supported_countries) && count($supported_countries) >= 1){
      $regions = '&region='.reset($supported_countries);
      // $regions = '&region='.implode(',', $supported_countries);
    }
    $api_lang = '';
    if(($api_lib_lang = $this->get_option('api_lib_lang')) && !empty($api_lib_lang)) {
      $api_lang = '&language='.$api_lib_lang;
    }

    if($load){
      $google_api_key = $this->get_option('google_api_key');
      wp_register_script( 'googlemapsapi', 'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&libraries=places&v=weekly'.$regions.$api_lang);
      wp_enqueue_script( 'googlemapsapi' );
    }
  
      
      wp_enqueue_script( $this->prefix('script') );
      
      $js_variables = array(
        'input_element_class' => $this->get_option('input_element_class'),
        //'supported_countries' => SerializeStringToArray($this->get_option('supported_countries')),
      );
      if(!empty($supported_countries))
        $js_variables['supported_countries'] = $supported_countries;

      wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );
    
    


  }// wp_admin_style_scripts


  /******************************************/
  /***** add javascript and css to front end. **********/
  /******************************************/
  public function wp_style_scripts() {

    $google_api_key = $this->get_option('google_api_key');
    wp_register_script( 'googlemapsapi', 'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&libraries=places&v=weekly');
    wp_enqueue_script( 'googlemapsapi');


    wp_register_script( $this->prefix('script'), gloo()->plugin_url('includes/modules/').'autocomplete-address-fields/js/script.js', array('googlemapsapi'), '1.0');
    wp_enqueue_script( $this->prefix('script') );


    $js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );

  }
  


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
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'OTW Invitation System', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'gloo_for_elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

  }
  


  /******************************************/
  /***** Intialize the elementor and other plugins extended classes and functions. **********/
  /******************************************/
  public function init() {
    
		// Add Plugin actions
		//add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
    //add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
  
    // Add pattern attribute to form field render
		
    //add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'addAutocompleteAddressFieldControl' ], 100, 2 );
    
    
  }
  

  /******************************************/
  /***** Load Elementor custom widgets. **********/
  /******************************************/
  public function init_widgets() {

  
    //add_action('elementor/element/before_section_end', array($this, 'before_section_end') );
    





    /*add_action( 'elementor/element/form/section_form_fields/before_section_end', function( $element, $args ) {
      //db($element);exit();
      $element->start_injection( [
        'at' => 'before',
        'of' => 'input_size',
      ] );
      
      $element->add_control(
        'custom_control',
        [
          'type' => \Elementor\Controls_Manager::NUMBER,
          'label' => __( 'Custom Control', 'plugin-name' ),
        ]
      );
    }, 10, 2 );*/



    // This example will add a render 'class' attribute to the testimonial content
  /*add_action( 'elementor/widget/before_render_content', function ( $widget ) {
    //Check if we are on a testimonial
    if( 'form' === $widget->get_name() ) {
      // Get the settings
      $settings = $widget->get_settings();
      $settings['form_fields'];
      if($settings['form_fields'] && is_array($settings['form_fields']) && count($settings['form_fields']) >= 1){
        foreach($settings['form_fields'] as $form_field){
          if($form_field['field_type'] == 'google_address'){
            $form_field['field_type'] = 'text';
          }
        }
      }
      
      // Adding our type as a class to the testimonial
      if( $settings['testimonial_content_border_bottom'] ) {
        $testimonial->add_render_attribute( 'testimonial_content', 'class', $settings['testimonial_content_border_bottom'], true );
      }
    }
  } );*/
  


    // Register widget 
    //field_type
    //db(\Elementor\Plugin::instance()->widgets_manager->get_widget_types());exit();
    //db(\Elementor\Plugin::instance()->widgets_manager->_widget_types);exit();
		//\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ElementorWidget() );

	}

  /******************************************/
  /***** Load Elementor custom controls. **********/
  /******************************************/
	public function init_controls() {

		// Register control
		\Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new ElementorControl() );

  }

  /**
	 * add_pattern_field_control
	 * @param $element
	 * @param $args
	 */
	public function addAutocompleteAddressFieldControl( $element, $args ) {
		$elementor = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}
		// create a new pattern control as a repeater field
		$tmp = new \Elementor\Repeater();
		$tmp->add_control(
			'autocomplete_address',
			[
				'label' => __('Autocomplete Address', 'gloo_for_elementor'),
				'inner_tab' => 'form_fields_advanced_tab',
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => array('text'),
						],
					],
				],
			]
		);

		$autocomplete_address = $tmp->get_controls();
		$autocomplete_address = $autocomplete_address['autocomplete_address'];

		// insert new autocomplete_address field in advanced tab before field ID control
		$new_order = [];
		foreach ( $control_data['fields'] as $field_key => $field ) {
			if (isset($field['name']) &&  'custom_id' === $field['name'] ) {
				$new_order['autocomplete_address'] = $autocomplete_address;
			}
			$new_order[ $field_key ] = $field;
		}
		$control_data['fields'] = $new_order;

		$element->update_control( 'form_fields', $control_data );
	}

  /******************************************/
  /***** addPattern. **********/
  /******************************************/
	public function addPattern($field, $field_index, $form_widget) {

    if ( ! empty( $field['autocomplete_address'] ) && in_array( $field['field_type'], array('text') ) ) {
      
      $form_widget->add_render_attribute( 'input' . $field_index, 'class', $this->get_option('input_element_class'));
    
    }
    return $field;

  }
  

  /******************************************/
  /***** before_section_end. **********/
  /******************************************/
  public function before_section_end($section){
      
    if($section->get_name() == 'form'){

      //$form_control = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( 'form', 'section_form_fields' );
      //db(\Elementor\Plugin::instance()->controls_manager);exit();
      
      $form_control = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $section->get_unique_name(), 'form_fields' );
      //db($form_control);exit();
      //db($form_control);exit();
      $form_control['fields']['field_type']['options']['google_address'] = 'Google Address';
      $section->update_control(
        'form_fields',
        $form_control
      );
    }
    /*if( $section->get_name() == 'testimonial' && $section_id == 'section_testimonial' ){
      // we are at the end of the "section_testimonial" area of the "testimonial"
      $section->add_control(
        'testimonial_name_title_pos' ,
        [
          'label'        => 'Name and title position',
          'type'         => Elementor\Controls_Manager::SELECT,
          'default'      => 'vertical',
          'options'      => array(
            'vertical' => 'Vertical',
            'horizontal' => 'Horizontal'
          ),
          'prefix_class' => 'dgm-testimonial-name-title-',
          'label_block'  => true,
          'condition'  => [
            'testimonial_image_position' => 'aside',
          ]
        ]
      );
    }*/
    /*if( $section->get_name() == 'testimonial' && $section_id == 'section_style_testimonial_content' ){
      // we are at the end of the "section_testimonial" area of the "testimonial"
      $section->add_control(
        'testimonial_content_border_bottom' ,
        [
          'label'        => 'Border Bottom',
          'type'         => Elementor\Controls_Manager::SWITCHER,
          'label_on' => __( 'Show', 'your-plugin' ),
          'label_off' => __( 'Hide', 'your-plugin' ),
          'return_value' => 'border_bottom',
          'default' => 'yes',
        ]
      );
      $section->add_control(
        'testimonial_content_border_color' ,
        [
          'label'        => 'Border Color',
          'type'         => Elementor\Controls_Manager::COLOR,
          'label_block'  => true,
          'default'  => '#ECF0F8',
          'selectors' => [
            // Stronger selector to avoid section style from overwriting
            '{{WRAPPER}} .elementor-testimonial-content.border_bottom' => 'border-bottom-color: {{VALUE}};',
          ],
          'condition'  => [
            'testimonial_content_border_bottom' => 'border_bottom',
          ]
        ]
      );
    }*/
  }
}

