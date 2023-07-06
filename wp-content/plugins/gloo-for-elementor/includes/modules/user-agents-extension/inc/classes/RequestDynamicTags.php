<?php
namespace Gloo\Modules\UserAgentsExtension;

Class RequestDynamicTags extends \Elementor\Core\DynamicTags\Tag {

  /*public $name;
	public $title;

	public function __construct($name = 'current-stored-user-agent', $title = 'Current Stored User Agent'){
		$this->name = $name;
		$this->title = $title;
	}*/

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
    return 'current-stored-user-agent';
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
    return __( 'Current user agent Multiple', 'gloo_for_elementor' );
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
    //return 'user-agent-request-variables';
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
    return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
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

    /*$variables = [];

    foreach ( array_keys( $_SERVER ) as $variable ) {

      $variables[ $variable ] = ucwords( str_replace( '_', ' ', $variable ) );
    }
*/
    $this->add_control(
      'gloo_allow_all_devices',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'All Approved Devices', 'gloo_for_elementor' ),
      ]
    );


    $variables = [];
    for($i = 1; $i <= gloo_user_agents_extension()->get_option('allowed_devices'); $i++){
      $variables[$i] = $i;
    }
    $this->add_control(
      'gloo_aproved_device',
      [
        'label' => __( 'Approved Device', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $variables,
        'default' => 1,
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );


    $variables = array('ip' => 'IP Address', 'browser' => 'Browser', 'os' => 'Operating System', 'device' => 'Device', 'brand' => 'Brand');
    $this->add_control(
      'gloo_allow_all_devices_filter',
      [
        'label' => __( 'Visibility Type', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $variables,
        'default' => 1,
        'condition' => ['gloo_allow_all_devices' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_agent_ip',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'IP Address', 'gloo_for_elementor' ),
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_browser',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Browser', 'gloo_for_elementor' ),
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_os',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Operating System', 'gloo_for_elementor' ),
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_device_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Device', 'gloo_for_elementor' ),
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_brand_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Brand Name', 'gloo_for_elementor' ),
        'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_string_separator', [
        'label' => __( 'String Separator', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => ', ',
        'label_block' => true,
      ]
    );
    

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

    $output_array = array();
    
    if(is_user_logged_in()){
      //$value =  get_user_meta($current_user_id, 'gloo_user_agents_extension', true);
      $current_user_id = get_current_user_id();
      $settings = $this->get_settings();
      $gloo_stored_devices = SerializeStringToArray(get_user_meta( $current_user_id, 'gloo_allowed_devices', true ));
      if($gloo_stored_devices && is_array($gloo_stored_devices) && count($gloo_stored_devices) >= 1){
        $i = 1;
        foreach($gloo_stored_devices as $key=>$stored_device){
          if($settings['gloo_allow_all_devices'] == 'yes'){
            if($settings['gloo_allow_all_devices_filter']){
              $visibility_type = $settings['gloo_allow_all_devices_filter'];
              if(isset($stored_device[$visibility_type]) && $stored_device[$visibility_type]){
                $output_array[] = $stored_device[$visibility_type];
              }              
            }
            else
              break;
          }
          else if($settings['gloo_aproved_device'] == $i){
            $controls_array = array('ip' => 'gloo_stored_agent_ip', 'browser' => 'gloo_stored_browser', 'os' => 'gloo_stored_os', 'device' => 'gloo_stored_device_name', 'brand' => 'gloo_stored_brand_name');
            foreach($controls_array as $stored_key=>$key_name){
              //$meta_value = get_user_meta($current_user_id, $key_name, true);
              if($settings[$key_name] && isset($stored_device[$stored_key]) && $stored_device[$stored_key]){
                $output_array[] = $stored_device[$stored_key];
              }
            }
            break;      
          }
          $i++;
        }
      }
      
      /*$controls_array = array('gloo_stored_agent_ip', 'gloo_stored_browser', 'gloo_stored_os', 'gloo_stored_device_name', 'gloo_stored_brand_name');
      foreach($controls_array as $key_name){
        $meta_value = get_user_meta($current_user_id, $key_name, true);
        if($settings[$key_name] && $meta_value){
          $output_array[] = $meta_value;
        }
      }*/
    }
    
    
    //db($settings);exit();
    /*$param_name = $this->get_settings( 'param_name' );

      if ( ! $param_name ) {
      return;
    }

    if ( ! isset( $_SERVER[ $param_name ] ) ) {
      return;
    }

    $value = $_SERVER[ $param_name ];
    echo wp_kses_post( $value );*/
    
    
    $string_separator = ', ';
    if($settings['gloo_stored_string_separator'])
      $string_separator = $settings['gloo_stored_string_separator'];

    echo wp_kses_post(implode($string_separator, $output_array));
  }



}
