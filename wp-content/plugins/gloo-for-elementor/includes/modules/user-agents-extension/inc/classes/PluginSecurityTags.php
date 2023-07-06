<?php
class PluginSecurityTags extends \Elementor\Core\DynamicTags\Tag
{

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
  public function get_name()
  {
    return 'user-agents';
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
  public function get_title()
  {
    return __('Current user agent', 'gloo');
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
  public function get_group()
  {
    return 'gloo-dynamic-tags';
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
  public function get_categories()
  {
    return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
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
  protected function _register_controls()
  {

    /*$variables = [];

    foreach ( array_keys( $_SERVER ) as $variable ) {

      $variables[ $variable ] = ucwords( str_replace( '_', ' ', $variable ) );
    }
*/
    $this->add_control(
      'gloo_allow_all_devices',
      [
        'label' => __('Approved Devices', 'gloo_for_elementor'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => ['all' => 'All', 'desktop' => 'Desktop', 'mobile' => 'Mobile'],
        'default' => 'desktop',
      ]
    );

    $variables = [];
    for ($i = 1; $i <= 10; $i++) {
      $variables[$i] = $i;
    }
    $this->add_control(
      'gloo_aproved_device_number',
      [
        'label' => __( 'Approved Device Number', 'gloo' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $variables,
        'default' => 1,
        'condition' => ['gloo_allow_all_devices!' => 'all'],
      ]
    );


    // $variables = array('browser' => 'Browser', 'os' => 'Operating System', 'device' => 'Device', 'brand' => 'Brand');
    // $this->add_control(
    //   'gloo_allow_all_devices_filter',
    //   [
    //     'label' => __('Visibility Type', 'gloo_for_elementor'),
    //     'type' => \Elementor\Controls_Manager::SELECT,
    //     'options' => $variables,
    //     'default' => 1,
    //     //'condition' => ['gloo_allow_all_devices' => 'yes'],
    //   ]
    // );


    $this->add_control(
      'gloo_stored_browser',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __('Browser', 'gloo_for_elementor'),
        //'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_os',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __('Operating System', 'gloo_for_elementor'),
        //'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_device_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __('Device', 'gloo_for_elementor'),
        //'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_brand_name',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __('Brand Name', 'gloo_for_elementor'),
        //'condition' => ['gloo_allow_all_devices!' => 'yes'],
      ]
    );

    $this->add_control(
      'gloo_stored_string_separator',
      [
        'label' => __('String Separator', 'gloo_for_elementor'),
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
  public function render()
  {

    $output_array = array();

    if (is_user_logged_in()) {
      $current_user_id = get_current_user_id();
      $settings = $this->get_settings();
      $mobile_devices = [];
      $desktop_devices = [];

      if (isset($settings['gloo_allow_all_devices']) && $settings['gloo_allow_all_devices'] == 'desktop') {

        $desktop_devices = get_user_meta($current_user_id, 'otw_user_info');
        if($desktop_devices && is_array($desktop_devices) && count($desktop_devices) >= 1 && isset($settings['gloo_aproved_device_number']) && $settings['gloo_aproved_device_number']){
          $desktop_devices = array($desktop_devices[((int)$settings['gloo_aproved_device_number'])-1]);
        }
      } else if (isset($settings['gloo_allow_all_devices']) && $settings['gloo_allow_all_devices'] == 'mobile') {

        $mobile_devices = get_user_meta($current_user_id, 'otw_user_info_mobile');
        if($mobile_devices && is_array($mobile_devices) && count($mobile_devices) >= 1 && isset($settings['gloo_aproved_device_number']) && $settings['gloo_aproved_device_number']){
          $mobile_devices = array($mobile_devices[((int)$settings['gloo_aproved_device_number'])-1]);
        }
      } else {

        $mobile_devices = get_user_meta($current_user_id, 'otw_user_info_mobile');
        $desktop_devices = get_user_meta($current_user_id, 'otw_user_info');
      }



      $gloo_stored_devices_arr = array_merge($mobile_devices, $desktop_devices);
      foreach ($gloo_stored_devices_arr as $gloo_stored_devices_meta) {
        $gloo_stored_devices = json_decode($gloo_stored_devices_meta, true);
        $gloo_stored_devices = json_decode($gloo_stored_devices_meta, true);
        if ($gloo_stored_devices && is_array($gloo_stored_devices) && count($gloo_stored_devices) >= 1) {

          foreach ($gloo_stored_devices as $key => $stored_device) {
            $stored_device = json_decode($gloo_stored_devices_meta, true);

            $controls_array = array('browser' => 'gloo_stored_browser', 'os' => 'gloo_stored_os', 'device' => 'gloo_stored_device_name', 'brand' => 'gloo_stored_brand_name');
            foreach ($controls_array as $stored_key => $key_name) {
              if ($settings[$key_name] && isset($stored_device[$stored_key]) && $stored_device[$stored_key]) {
                $output_array[] = $stored_device[$stored_key];
              }
            }
            break;
          }
        }
      }
    }

    $string_separator = ', ';
    if ($settings['gloo_stored_string_separator'])
      $string_separator = $settings['gloo_stored_string_separator'];

    echo wp_kses_post(implode($string_separator, $output_array));
  }
}
