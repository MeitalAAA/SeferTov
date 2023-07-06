<?php
namespace Gloo\ElementorSelect2Fields;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin{

  private static $instance = null;

  public $prefix = 'gloo_elementor_select2_fields';
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
    // self::$options = SerializeStringToArray(get_option($this->prefix.'_options'));
    
    add_action('wp_head', [ $this, 'wp_head' ]);

    if ( $this->is_compatible() ) {
      add_filter( 'elementor_pro/forms/render/item', [ $this, 'elementor_pro_forms_render_item' ], 10, 3 );
      
      add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'addFormFieldControl' ], 100, 2 );

      add_action( 'elementor/element/form/section_form_style/after_section_end', [
        $this,
        'add_control_section_to_form'
      ], 10, 2 );
    }
    
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
      add_action( 'wp_enqueue_scripts', array($this, 'wp_style_scripts') );
    }

  }// construct function end here


	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.




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
    <script>
			if (typeof generateRandomString != 'function') {
			  function generateRandomString(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
              result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
          return result;
        }
			}
			if (typeof inArray != 'function') {
        function inArray(needle, haystack) {
            var length = haystack.length;
            for(var i = 0; i < length; i++) {
                if(haystack[i] == needle) return true;
            }
            return false;
        }
      }
          jQuery(document).ready(function($){
			  
            if($(".gloo_elementor_select2").length >= 1){
              select_two_ids = [];
              $(".gloo_elementor_select2").each(function(i, v){
                current_select_id = $(this).attr('id');
                if(inArray($(this).attr('id'), select_two_ids)){
                  current_select_id = generateRandomString(12);
                  $(this).attr('id', current_select_id);
                }
                select_two_ids.push(current_select_id);

                // var options_object = {};
                // if($(this).attr('data-select-placeholder')){
                //   options_object.placeholder = $(this).attr('data-select-placeholder');
                // }
                // console.log(options_object);
                // $(this).select2(options_object);

              });
              $(".gloo_elementor_select2").select2();
            }
          });
        </script>
        <style>
          ul.select2-selection__rendered{
            white-space:unset!important;
          }
          .select2-selection__choice{
            display: inline-block!important;
            float: none!important;
          }
          .select2-selection__rendered .select2-search{
            float: none!important;
          }
          span.select2-selection__arrow {
              display: none;
          }
        </style>
    <?php
}


	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_style_scripts() {

    wp_register_style( 'gloo_select2_css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
    wp_enqueue_style('gloo_select2_css');

    wp_register_style( $this->prefix('style'), $this->plugin_url() . 'css/style.css', array('gloo_select2_css'), get_file_time($this->plugin_path() . 'css/style.css'));
    wp_enqueue_style($this->prefix('style'));


    wp_register_script( 'gloo_select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array('jquery'));
    wp_enqueue_script('gloo_select2_js');

    // wp_register_script( $this->prefix('script'),  gloo()->plugin_url( 'includes/modules/elementor-select2-fields/js/script.js'), array(), '1.0');
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
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'BBWP Invitation System', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'gloo_for_elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

  }
  


  /**
	 * add_pattern_field_control
	 * @param $element
	 * @param $args
	 */
	public function addFormFieldControl( $element, $args ) {
		$elementor = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}
    // db($element->get_name());
    // db(get_class_methods($element));exit();
    // $element->start_controls_section(
		// 	'content_section',
		// 	[
		// 		'label' => esc_html__( 'Button', '' ),
		// 		'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		// 	]
		// );

    // $element->end_controls_section();
		// create a new pattern control as a repeater field
    // $element->add_group_control(
    //   \Elementor\Group_Control_Border::get_type(),
    //   [
    //     'name'      => $this->prefix.'_border_test',
    //     'condition'    => [
    //       'gloo_is_select2' => 'yes',
    //     ],
    //     'tab'          => 'advanced',
    //     'inner_tab'    => 'form_fields_advanced_tab',
    //     'tabs_wrapper' => 'form_fields_tabs',
    //   ]
    // );
		// $tmp = new \Elementor\Repeater();

    // $tmp->add_group_control(
    //   \Elementor\Group_Control_Border::get_type(),
    //   [
    //     'name'      => $this->prefix.'_border',
    //     'condition'    => [
    //       'gloo_is_select2' => 'yes',
    //     ],
    //     'tab'          => 'advanced',
    //     'inner_tab'    => 'form_fields_advanced_tab',
    //     'tabs_wrapper' => 'form_fields_tabs',
    //   ]
    // );
    // $tmp->add_control(
		// 	$this->prefix.'_border',			
		// );
    
		// $all_controls = $tmp->get_controls();
    // unset($all_controls['_id']);
    // db($all_controls);exit();
    // $gloo_is_select2 = $all_controls['gloo_is_select2'];		
    
		// insert new autocomplete_address field in advanced tab before field ID control
		// $new_order = [];
		// foreach ( $control_data['fields'] as $field_key => $field ) {
			// if ( 'custom_id' === $field['name'] ) {
			// 	$new_order['gloo_is_select2'] = $gloo_is_select2;
			// }
			// $new_order[ $field_key ] = $field;
		// }
    // $new_order['gloo_is_select2'] = $all_controls['gloo_is_select2'];
    
    $control_data['fields']['gloo_is_select2'] = [
      'name' => 'gloo_is_select2',
      'label' => __('Enable Select2', 'gloo_for_elementor'),
      'inner_tab' => 'form_fields_advanced_tab',
      'tab' => 'content',
      'tabs_wrapper' => 'form_fields_tabs',
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'conditions' => [
        'terms' => [
          [
            'name' => 'field_type',
            'operator' => 'in',
            'value' => array('select', 'gloo_terms_field', 'gloo_cpt_field', 'gloo_user_field'),
          ],
        ],
      ],
    ];
    
    
    

    
    
    
    

    
    // $control_data['fields'][$this->prefix.'_border'] = $all_controls[$this->prefix.'_border'];
		// $control_data['fields'] = $new_order;

    // $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );
    // $element->update_control( 'form_fields', $control_data );

    $element->update_control( 'form_fields', $control_data );
    // $all_controls = array();
    
    // $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $all_controls );

		// $element->update_control( 'form_fields', $control_data );
	}

  public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			'gloo_select2_fields_style',
			[
				'label' => __( 'Select2', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

    /*$control_data['fields']*/$element->add_control(
      $this->prefix.'_label_text_color', 
      [
        'name'         => $this->prefix.'_label_text_color',
        'label'        => __('Field Text Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .select2-container .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}};',
        ],
      /*'condition'    => [
        'gloo_is_select2' => 'yes',
      ],
      'tab'          => 'advanced',
      'inner_tab'    => 'form_fields_advanced_tab',
      'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control(
      $this->prefix.'_label_height', 
      [
        'name'         => $this->prefix.'_label_height',
        'label'        => __('Field Height', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::SLIDER,
        'default' 		=> [
          'unit' 		=> 'px',
          'size' 		=> 50
        ],
        'range' 		=> [
          '%' => [
            'min' 	=> 0,
            'max' 	=> 100,
          ],
          'px' => [
            'min' 	=> 0,
            'max' 	=> 1000,
          ],
        ],
        'size_units' 	=> [ 'px', '%','em','rem','vh' ],
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'height: {{SIZE}}{{UNIT}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control( 
      $this->prefix.'_label_background_color', 
      [
        'name'         => $this->prefix.'_label_background_color',
        'label'        => __('Field Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'background-color: {{VALUE}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control(
      $this->prefix.'_label_padding', 
      [
        'name'         => $this->prefix.'_label_padding',
        'label'        => __('Field Padding', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control(
      $this->prefix.'_label_border_width', 
      [
        'name'         => $this->prefix.'_label_border_width',
        'label'        => __('Field Border Width', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'border-top: {{TOP}}{{UNIT}}; border-right: {{RIGHT}}{{UNIT}}; border-bottom: {{BOTTOM}}{{UNIT}}; border-left: {{LEFT}}{{UNIT}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control( 
      $this->prefix.'_label_border_style', 
      [
        'name'         => $this->prefix.'_label_border_style',
        'label'        => __('Field Border Style', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::SELECT,
        'options' => array(
          'none' => 'None',
          'solid' => 'Solid',
          'double' => 'Double',
          'dotted' => 'Dotted',
          'dashed' => 'Dashed',
          'groove' => 'groove',
        ),
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'border-style: {{VALUE}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    $element->add_control( 
      $this->prefix.'_label_border_color', 
      [
        'name'         => $this->prefix.'_label_border_color',
        'label'        => __('Field Border Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} span.select2-selection.select2-selection--single' => 'border-color: {{VALUE}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );

    

    $element->add_control( 
      $this->prefix.'_background_color', 
      [
        'name'         => $this->prefix.'_background_color',
        'label'        => __('Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.select2-container--open .select2-results__option--selectable' => 'background-color: {{VALUE}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );
    $element->add_control( 
      $this->prefix.'_text_color', 
      [
        'name'         => $this->prefix.'_text_color',
        'label'        => __('Text Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.select2-container--open .select2-results__option--selectable' => 'color: {{VALUE}};',
        ],
        /*'condition'    => [
          'gloo_is_select2' => 'yes',
        ],
        'tab'          => 'advanced',
        'inner_tab'    => 'form_fields_advanced_tab',
        'tabs_wrapper' => 'form_fields_tabs',*/
      ]
    );


    $element->end_controls_section();
  }
  /******************************************/
  /***** elementor_pro_forms_render_item. **********/
  /******************************************/
	public function elementor_pro_forms_render_item($field, $field_index, $form_widget) {

    if ( ! empty( $field['gloo_is_select2'] ) && in_array( $field['field_type'], array('select', 'gloo_terms_field', 'gloo_cpt_field', 'gloo_user_field') ) ) {
      $form_widget->add_render_attribute( 'select' . $field_index, 'class', 'gloo_elementor_select2');
      // $form_widget->add_render_attribute( 'select' . $field_index, 'data-placeholder', 'choose field');
    }
    return $field;

  }

  /******************************************/
  /***** plugin_url functions **********/
  /******************************************/
  public function plugin_url() {    
    return trailingslashit(gloo()->plugin_url( 'includes/modules/elementor-select2-fields/'));
  }

  public function plugin_path() {
    return trailingslashit(gloo()->plugin_path('includes/modules/elementor-select2-fields'));
  }

  public function modules_path( $path = null ) {
    return trailingslashit(gloo()->plugin_path('includes/modules'));
  }
  
  public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'required', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
	}
} // BBWP_CustomFields class

