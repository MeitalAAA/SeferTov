<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class Current_URL extends \Elementor\Core\DynamicTags\Tag {

  /**
  * Get Name
  *
  * Returns the Name of the tag
  *
  * @since 2.0.0
  * @access public
  *
  * @return string
  */
  public function get_name() {
    return 'gloo-current-url-tag';
  }

  /**
  * Get Title
  *
  * Returns the title of the Tag
  *
  * @since 2.0.0
  * @access public
  *
  * @return string
  */
  public function get_title() {
    return __( 'Current Page URL', 'gloo_for_elementor' );
  }
  
  /**
  * Get Group
  *
  * Returns the Group of the tag
  *
  * @since 2.0.0
  * @access public
  *
  * @return string
  */
  public function get_group() {
    return 'gloo-dynamic-tags';
    //return 'general';
  }

  /**
  * Get Categories
  *
  * Returns an array of tag categories
  *
  * @since 2.0.0
  * @access public
  *
  * @return array
  */
  public function get_categories() {
    return [ 
      \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
      \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
    ];
    
  }

  /**
  * Register Controls
  *
  * Registers the Dynamic tag controls
  *
  * @since 2.0.0
  * @access protected
  *
  * @return void
  */
  protected function _register_controls() {

	  $this->add_control(
			'gloo_is_form_submission',
			[
				'label' => __( 'Is form submission', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
	  
    /*$variables = [];

    foreach ( array_keys( $_SERVER ) as $variable ) {

      $variables[ $variable ] = ucwords( str_replace( '_', ' ', $variable ) );
    }

    $this->add_control(
      'param_name',
      [
        'label' => __( 'Param Name', 'elementor-pro' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $variables,
      ]
    );*/
  }

  /**
  * Render
  *
  * Prints out the value of the Dynamic tag
  *
  * @since 2.0.0
  * @access public
  *
  * @return void
  */
  public function render() {
	  $gloo_is_form_submission = $this->get_settings( 'gloo_is_form_submission' );
	  if($gloo_is_form_submission && $gloo_is_form_submission == 'yes' && isset($_POST['referrer']) && function_exists('esc_url_raw')){
		  echo esc_url_raw( $_POST['referrer'] );
	  }else{
		  $current_page_url = $this->get_page_url();
      echo  $current_page_url['url'];
	  }
	  
    
  }

  public function get_page_url() {
		global $wp;
		$current_url = array();
		$current_url['arg'] = add_query_arg(array($_GET), '');
		if('' === get_option('permalink_structure')){
			$current_url['url'] = home_url($wp->request);
			//$current_url = home_url(add_query_arg(array($_GET), $wp->request));
		}
		else{
			$current_url['url'] = home_url($wp->request);
			$current_url_array = explode('/page', $current_url['url']);
			$current_url['url'] = trailingslashit($current_url_array[0]);
		}
		
		return ($current_url);
	}


}
