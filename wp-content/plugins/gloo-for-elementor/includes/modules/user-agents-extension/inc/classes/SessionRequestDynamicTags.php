<?php
namespace Gloo\Modules\UserAgentsExtension;

Class SessionRequestDynamicTags extends \Elementor\Core\DynamicTags\Tag {

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
    return 'current-session-user-agent';
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
    return __( 'Current Session User Agent', 'gloo_for_elementor' );
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

    $this->add_control(
      'param_name',
      [
        'label' => __( 'Param Name', 'elementor-pro' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $variables,
      ]
    );*/
    $this->add_control(
      'gloo_session_agent_ip',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'IP Address', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_browser',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Browser', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_os',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Operating System', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_device_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Device', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_brand_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Brand Name', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_string_separator', [
        'label' => __( 'String Separator', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => ', ',
        'label_block' => true,
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_search_string',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Search String', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_search_string_value', [
        'label' => __( 'Value', 'gloo_for_elementor' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        // 'default' => ', ',
        // 'label_block' => true,

        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '==',
							'value' => 'yes'
            ],
            [
							'name' => 'gloo_session_full_agent_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
      ]
    );

    $this->add_control(
      'gloo_session_full_agent_string',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Full User Agent String', 'gloo_for_elementor' ),
        'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'gloo_session_search_string',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
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
    
    if(isset($_SERVER['HTTP_USER_AGENT'])){
      
      $settings = $this->get_settings();
      //db($settings);exit();

      \DeviceDetector\Parser\Device\AbstractDeviceParser::setVersionTruncation(\DeviceDetector\Parser\Device\AbstractDeviceParser::VERSION_TRUNCATION_NONE);
      $userAgent = $_SERVER['HTTP_USER_AGENT'];
      $dd = new \DeviceDetector\DeviceDetector($userAgent);
      $dd->parse();
      if (!$dd->isBot()) { 
        
        $ip_address = getIPAddress();
        $clientInfo = $dd->getClient();
        $osInfo = $dd->getOs();
        $device = $dd->getDeviceName();
        $brand = $dd->getBrandName();
        //$model = $dd->getModel();
        
        if($settings['gloo_session_agent_ip'] && $ip_address){
          $output_array[] = $ip_address;
        }
        
        if($settings['gloo_session_browser'] && $clientInfo && isset($clientInfo['name'])){
          $output_array[] = $clientInfo['name'];
        }

        if($settings['gloo_session_os'] && $osInfo && isset($osInfo['name'])){
          $output_array[] = $osInfo['name'];
        }

        if($settings['gloo_session_device_name'] && $device){
          $output_array[] = $device;
        }

        if($settings['gloo_session_brand_name'] && $brand){
          $output_array[] = $brand;
        }
        
      }
    }
    
    $string_separator = ', ';
    if($settings['gloo_session_string_separator'])
      $string_separator = $settings['gloo_session_string_separator'];

      if(isset($_SERVER['HTTP_USER_AGENT']) && $settings['gloo_session_search_string'] && $settings['gloo_session_search_string'] == 'yes' && $settings['gloo_session_search_string_value']){
        if ( strpos( $_SERVER['HTTP_USER_AGENT'], $settings['gloo_session_search_string_value'] ) ) {
          echo 'Yes';
        }else
          echo 'No';
      }elseif(isset($_SERVER['HTTP_USER_AGENT']) && $settings['gloo_session_full_agent_string'] && $settings['gloo_session_full_agent_string'] == 'yes'){
        echo $_SERVER['HTTP_USER_AGENT'];
      }
      else{
        echo wp_kses_post(implode($string_separator, $output_array));
      }
    
  }



}
