<?php
namespace OTW\DynamicComposer;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;
use Elementor\Element_Base;
use ElementorPro\Base\Module_Base;
use ElementorPro\Plugin;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DynamicComposerCSS{
  
  public function __construct(){

    update_option('otw_elementor_composer_css_update', 'yes');
    
    //add_action( 'elementor/element/before_section_end', [ $this, 'otw_elementor_widgets_modifier'], 10, 2);
    add_action( 'elementor/element/after_section_end', [ $this, 'after_section_end'], 11, 2);
    //add_filter( 'elementor/widget/render_content', [ $this, 'otw_render_widget_output' ], 10, 2);
  
    // add_action( 'elementor/element/parse_css', [ $this, 'add_post_css' ], 10, 2 );
    // add_action( 'elementor/css-file/post/parse', [ $this, 'add_page_settings_css' ] );
    

    add_filter( 'elementor_pro/editor/localize_settings', [ $this, 'localize_settings' ] );

    add_action( 'elementor/editor/before_enqueue_scripts', array($this, 'elementor_after_enqueue_scripts') );

    // add_action( 'elementor/frontend/section/after_render', array( $this, 'elementor_frontend_before_render' ), 10 );
    add_action( 'elementor/frontend/after_render', array( $this, 'elementor_frontend_after_render' ), 10 );

    
    
  }// construct function end here


  public function otw_elementor_widgets_modifier($element, $section_id) {

    if ( 'section_custom_css' !== $section_id ) {
			return;
    }
    
    //echo $element->get_name();
  //	$controls = $element->get_frontend_settings_keys();
  
  //db(\Elementor\Plugin::instance()->widgets_manager->get_widget_types());exit();
  //print_r($controls);
    //if ( array_key_exists($element->get_name(), $this->allowedWidgets)) {
    //if ('heading' == $element->get_name() ) {
      //db($element);exit();
      $element->start_injection( [
        'type' => 'control',
        'at' => 'before',
        'of' => 'custom_css_title',
      ] );

      $repeater = new \Elementor\Repeater();

      $repeater->add_control(
        'list_variable_name', [
          'label' => __( 'Variable Name', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::TEXT,
          //'default' => __( 'List Title' , 'gloo' ),
          'label_block' => true,
          'classes' => "gloo_dynamic_css_composer_variable_name",
        ]
      );
  
      $repeater->add_control(
        'list_variable_value', [
          'label' => __( 'Variable Value', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::TEXT,
          //'default' => __( 'List Content' , 'gloo' ),
          //'show_label' => false,
          'label_block' => true,
          'dynamic' => [
            'active' => true,
          ],
        ]
      );

      $element->add_control(
        'css_var_list',
        [
          'label' => __( 'Items', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::REPEATER,
          'fields' => $repeater->get_controls(),
          'default' => [
            [
              'list_variable_name' => __( 'Variable Name', 'gloo_for_elementor' ),
              'list_variable_value' => __( 'Variable Value.', 'gloo_for_elementor' ),
            ]
          ],
          'title_field' => '{{{ list_variable_name }}}',
          //'title_field' => preg_replace('/[^a-zA-Z0-9]+/g', '', '{{{ list_variable_name }}}'),
        ]
      );

      $element->end_injection();
 

    //}
  }


  /**
	 * @param Controls_Stack $controls_stack
	 */
	public function after_section_end( $controls_stack, $section_id ) {

    if ( 'section_custom_css' !== $section_id ) {
			return;
    }

		$old_section = \ElementorPro\Plugin::elementor()->controls_manager->get_control_from_stack( $controls_stack->get_unique_name(), 'section_custom_css' );

		//\ElementorPro\Plugin::elementor()->controls_manager->remove_control_from_stack( $controls_stack->get_unique_name(), [ 'section_custom_css_pro', 'custom_css_pro', 'section_custom_css' ] );

		$controls_stack->start_controls_section(
			'gloo_custom_css_section',
			[
				'label' => __( 'Dynamic CSS Composer', 'gloo_for_elementor' ),
				'tab' => $old_section['tab'],
			]
    );
    
    $controls_stack->add_control(
      'gloo_custom_css_check',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Enable Dynamic CSS', 'gloo_for_elementor' ),
      ]
    );

    if(!(($_SERVER['HTTP_HOST'] == 'www.templeoflove.co.il' || $_SERVER['HTTP_HOST'] == 'templeoflove.co.il' || $_SERVER['HTTP_HOST'] == 'wordpress-506307-1675784.cloudwaysapps.com') && is_page(3788))){

      $repeater = new \Elementor\Repeater();

      $repeater->add_control(
        'list_variable_name', [
          'label' => __( 'Variable Name', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::TEXT,
          //'default' => __( 'List Title' , 'gloo' ),
          'label_block' => true,
          'classes' => "gloo_list_variable_name",
        ]
      );
  
      $repeater->add_control(
        'list_variable_value', [
          'label' => __( 'Variable Value', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::TEXT,
          //'default' => __( 'List Content' , 'gloo' ),
          //'show_label' => false,
          'label_block' => true,
          'dynamic' => [
            'active' => true,
          ],
        ]
      );

      $controls_stack->add_control(
        'gloo_css_variables',
        [
          'label' => __( 'Items', 'gloo_for_elementor' ),
          'type' => \Elementor\Controls_Manager::REPEATER,
          'fields' => $repeater->get_controls(),
          'default' => [
            [
              'list_variable_name' => __( 'Variable Name', 'gloo_for_elementor' ),
              'list_variable_value' => __( 'Variable Value.', 'gloo_for_elementor' ),
            ]
          ],
          'title_field' => '{{{ list_variable_name }}}',
          'condition' => ['gloo_custom_css_check' => 'yes']
        ]
      );
      
    }
    

		$controls_stack->add_control(
			'gloo_custom_css_title',
			[
				'raw' => __( 'Add your own custom CSS here', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::RAW_HTML,
        'condition' => ['gloo_custom_css_check' => 'yes']
			]
    );

    $controls_stack->add_control(
			'gloo_custom_css',
			[
				'type' => \Elementor\Controls_Manager::CODE,
				'label' => __( 'Custom CSS', 'gloo_for_elementor' ),
				'language' => 'css',
				'render_type' => 'ui',
				'show_label' => false,
        'separator' => 'none',
        'condition' => ['gloo_custom_css_check' => 'yes']
			]
		);

    $controls_stack->add_control(
			'gloo_custom_css_description',
			[
				'raw' => __( 'Use "selector" to target wrapper element. Examples:<br>selector {color: red;} // For main element<br>selector .child-element {margin: 10px;} // For child element<br>.my-class {text-align: center;} // Or use any custom selector', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-descriptor',
        'condition' => ['gloo_custom_css_check' => 'yes']
			]
		);
    
		$controls_stack->end_controls_section();
	}





  
	/**
	 * @param $post_css Post
	 * @param $element  Element_Base
	 */
	public function add_post_css( $post_css, $element ) {
		/*if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}*/

    $element_settings = $element->get_settings();
    $display_settings = $element->get_settings_for_display();


		if ( empty( $element_settings['gloo_custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['gloo_custom_css'] );

		if ( empty( $css ) ) {
			return;
		}
		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );

    if(isset($display_settings['gloo_css_variables']) && is_array($display_settings['gloo_css_variables']) && count($display_settings['gloo_css_variables']) >= 1){
      foreach($display_settings['gloo_css_variables'] as $key=>$value){
        //if($value['list_variable_name'] && $value['list_variable_value'])
          $css = str_replace($value['list_variable_name'], $value['list_variable_value'], $css);
      }
    }
		// Add a css comment
		$css = sprintf( '/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector() ) . $css . '/* End custom CSS */';

    
    if ($element_settings['gloo_custom_css_check'] != 'yes' ) {
      $css = sprintf( '/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector() ) . "" . '/* End custom CSS */';
    }

    //Clear cache file
    $post_css_meta = get_post_meta($post_css->get_post_id(), $post_css::META_KEY, true);
		if(get_option("otw_elementor_composer_css_update") == 'yes' && $post_css_meta['status'] == 'file'){
			update_option("otw_elementor_composer_css_update", 'no');
			update_post_meta( $post_css->get_post_id(), $post_css::META_KEY, array('time' => '0') );
			$post_css->update();
	  }

    $post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * @param $post_css Post
	 */
	public function add_page_settings_css( $post_css ) {
		$document = Plugin::elementor()->documents->get( $post_css->get_post_id() );
    $custom_css = $document->get_settings( 'gloo_custom_css' );
    $display_settings = $document->get_settings_for_display();

		$custom_css = trim( $custom_css );

		if ( empty( $custom_css ) ) {
			return;
		}

		$custom_css = str_replace( 'selector', $document->get_css_wrapper_selector(), $custom_css );
    if(isset($display_settings['gloo_css_variables']) && is_array($display_settings['gloo_css_variables']) && count($display_settings['gloo_css_variables']) >= 1){
      foreach($display_settings['gloo_css_variables'] as $key=>$value){
        //if($value['list_variable_name'] && $value['list_variable_value'])
          $custom_css = str_replace($value['list_variable_name'], $value['list_variable_value'], $custom_css);
      }
    }
		// Add a css comment
		$custom_css = '/* Start custom CSS */' . $custom_css . '/* End custom CSS */';

    if ($element_settings['gloo_custom_css_check'] != 'yes' ) {
      $css = sprintf( '/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector() ) . "" . '/* End custom CSS */';
    }
    $post_css->get_stylesheet()->add_raw_css( $custom_css );
  }
  

  /******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function elementor_after_enqueue_scripts() {

    
    wp_register_script( 'gloo-dynamic-composer-css-script', gloo()->plugin_url('includes/modules/').'dynamic-composer/js/css-composer-script.js', array('jquery'), '1.1', true);
    wp_enqueue_script( 'gloo-dynamic-composer-css-script' );
    
    //$js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    //wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );
  

  }// wp_admin_style_scripts


  public function localize_settings( array $settings ) {
		$settings['i18n']['gloo_custom_css'] = __( 'Gloo Custom CSS', 'gloo_for_elementor' );

		return $settings;
  }
  

  public function elementor_frontend_after_render($element){

    $display_settings = $element->get_settings_for_display();

    // if ( 'section_custom_css' !== $section_id ) {
		// 	return;
    // }
    //if(isset($_GET['test'])){

      if (isset($display_settings['gloo_custom_css_check']) && $display_settings['gloo_custom_css_check'] == 'yes' ){
        
        $custom_css = $element->get_settings( 'gloo_custom_css' );
        $custom_css = trim( $custom_css );

        if ( empty( $custom_css ) )
          return;
        //db(get_class_methods($element));
        
        $custom_css = str_replace( 'selector', $element->get_unique_selector(), $custom_css );

        if(isset($display_settings['gloo_css_variables']) && is_array($display_settings['gloo_css_variables']) && count($display_settings['gloo_css_variables']) >= 1){
          
          foreach($display_settings['gloo_css_variables'] as $key=>$value){
            //if($value['list_variable_name'] && $value['list_variable_value'])
              $custom_css = str_replace($value['list_variable_name'], $value['list_variable_value'], $custom_css);
          }
          //db($custom_css);exit();
          // db($element->get_type());
          // db($element->get_unique_name());
          // db($element->get_current_section());
        }

        echo '<style type="text/css" data-gloo="css">'; 
        echo $custom_css;
        echo '</style>';

      }
      // else return;
      
      
      // db($element);
      // db(get_class_methods($element));
      // exit();
      
    //}
  }

}// class end here
