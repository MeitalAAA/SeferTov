<?php

namespace Gloo\Modules\UserAgentsExtension;

// exit if file is called directly
if (!defined('ABSPATH')) {
  exit;
}

class PluginSecurity extends Plugin
{
  public $new_logic;
  /**
   * Class constructor
   */
  public function __construct()
  {
    $this->new_logic = unserialize(get_option('gloo_user_agents_extension_options'));

    if (!is_admin()) {
      add_filter('wp_authenticate_user', [$this, 'validate_allow_logic']);
    } elseif (is_admin() && isset($this->new_logic['limit_admin']) && $this->new_logic['limit_admin'] == 'yes') {
      add_filter('wp_authenticate_user', [$this, 'validate_allow_logic']);
    }

    if ($this->is_compatible()) {
      add_action('elementor/dynamic_tags/register_tags', [$this, 'register_tags']);
    }

    if (is_admin()) {
      add_action('edit_user_profile', array($this, 'user_devices'));
      add_action('show_user_profile', array($this, 'user_devices'));
      add_action('profile_update', array($this, 'user_devices'));
      add_action('edit_user_profile_update', [$this, 'user_devices_update']);
    }
  }

  /**
   * Register tags
   */
  public function register_tags($dynamic_tags)
  {

    // In our Dynamic Tag we use a group named request-variables so we need 
    // To register that group as well before the tag
    /*\Elementor\Plugin::$instance->dynamic_tags->register_group('user-agents-extension', [
      'title' => 'User Agents Extension'
    ]);*/
    
    // Include the Dynamic tag class file
    include_once(dirname(__FILE__) . '/PluginSecurityTags.php');

    // Finally register the tag
    $dynamic_tags->register_tag('PluginSecurityTags');
  }
  /**
   * Validates if user is allowed to login
   */
  public function validate_allow_logic($user)
  {
    // If login validation failed already, return that error.
    if (is_wp_error($user)) {
      return $user;
    }

    /**
     * Get Brower data
     */

    $browser = $this->getUserData();

    /**
     * Get user meta
     */
    $allowed_device_count = 1;
    if (wp_is_mobile()) {
      /* Display and echo mobile specific stuff here */
      $usermeta_key = 'otw_user_info_mobile';
      $allowed_device_count = $this->new_logic['allowed_mobile_devices'];
    } else {
      $usermeta_key = 'otw_user_info';
      $allowed_device_count = $this->new_logic['allowed_desktop_devices'];
    }

    $user_meta = get_user_meta($user->data->ID, $usermeta_key, false);
    if ((int) $allowed_device_count > count($user_meta)) {
      if (!in_array(json_encode($browser, JSON_UNESCAPED_SLASHES), $user_meta)) {
        add_user_meta($user->data->ID, $usermeta_key, json_encode($browser));
        return $user;
      }
    }
    
    if($this->new_logic['disable_wp_login'] == 'no'){
      if (!in_array(json_encode($browser, JSON_UNESCAPED_SLASHES), $user_meta)) {
        $this->send_new_email($user->data);
        return new \WP_Error('loggedin_reached_limit', "Cannot log in using this device");
      }
    }
    
    

    return $user;
  }

  /**
   * Get user browser data
   */

  public function getUserData()
  {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $new_device = array('user_agent' => $userAgent);
    \DeviceDetector\Parser\Device\AbstractDeviceParser::setVersionTruncation(\DeviceDetector\Parser\Device\AbstractDeviceParser::VERSION_TRUNCATION_NONE);
    $dd = new \DeviceDetector\DeviceDetector($userAgent);
    $dd->parse();


    $clientInfo = $dd->getClient();
    $osInfo = $dd->getOs();
    $device = $dd->getDeviceName();
    $brand = $dd->getBrandName();

    /**
     * remving ip condtion as per @Tahir request
     * 
     * $ip_address = getIPAddress();
     * if ($ip_address) { $new_device['ip'] = $ip_address; }
     */

    if ($clientInfo && isset($clientInfo['name'])) {
      $new_device['browser'] = $clientInfo['name'];
    }

    if ($osInfo && isset($osInfo['name'])) {
      $new_device['os'] = $osInfo['name'];
    }

    if ($device) {
      $new_device['device'] = $device;
    }

    if ($brand) {
      $new_device['brand'] = $brand;
    }
    return $new_device;
  }

  public function send_new_email($user)
  {

    $from_name = isset($this->new_logic['from_name']) ? $this->new_logic['from_name'] : get_bloginfo('name');
    $from_email = isset($this->new_logic['from_email']) ? $this->new_logic['from_email'] : get_bloginfo('admin_email');
    $to_email = isset($this->new_logic['on_failure_send_email_to']) ? $this->new_logic['on_failure_send_email_to'] : get_bloginfo('admin_email');
    $email_subject = isset($this->new_logic['email_subject']) ? $this->new_logic['email_subject'] : 'User device lmit reached';

    $user_fullname = $user->user_nicename;
    $user_email = $user->user_email;
    $subject = $email_subject;

    $find_array = array("{username}", "{email}");
    $replace_array = array($user_fullname, $user_email);
    $user_message = str_replace($find_array, $replace_array, $this->new_logic["email_message_new"]);

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";

    wp_mail($to_email, $subject, $user_message, $headers);
  }

  /**
   * The field on the editing screens.
   *
   * @param $user WP_User user object
   */
  public function user_devices($user)
  {
    $mobile_devices = get_user_meta($user->data->ID, 'otw_user_info_mobile');
    $desktop_devices = get_user_meta($user->data->ID, 'otw_user_info'); ?>
    <h3>Approved Devices</h3>
    <table class="form-table">
      <?php foreach ($desktop_devices as $key => $d_device) { ?>
        <tr>
          <th>
            <label for="birthday">Desktop Device <?= ($key + 1); ?></label>
          </th>
          <td>
            Delete <input type="checkbox" value="<?php echo base64_encode($d_device); ?>" name="reset_devices_desktop[]">
            <p class="description">
              <?php echo $d_device; ?>
            </p>
          </td>
        </tr>
      <?php } ?>

      <?php foreach ($mobile_devices as $key => $d_device) { ?>
        <tr>
          <th>
            <label for="birthday">Mobile Device <?= ($key + 1); ?></label>
          </th>
          <td>
            Delete <input type="checkbox" value="<?php echo base64_encode($d_device); ?>" name="reset_devices_mobile[]">
            <p class="description">
              <?php echo $d_device; ?>
            </p>
          </td>
        </tr>
      <?php } ?>
    </table>
<?php
  }

  /**
   * The save action.
   *
   * @param $user_id int the ID of the current user.
   *
   * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
   */
  public function user_devices_update($user_id)
  {
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
      return false;
    }

    if (isset($_POST['reset_devices_desktop'])) {
      $desktop_devices = get_user_meta($user_id, 'otw_user_info');

      foreach ($_POST['reset_devices_desktop'] as $device) {
        if (($key = array_search(base64_decode($device), $desktop_devices)) !== false) {
          delete_user_meta($user_id, 'otw_user_info', $desktop_devices[$key]);
        }
      }
    }

    if (isset($_POST['reset_devices_mobile'])) {
      $mobile_devices = get_user_meta($user_id, 'otw_user_info_mobile');

      foreach ($_POST['reset_devices_mobile'] as $device) {
        if (($key = array_search(base64_decode($device), $mobile_devices)) !== false) {
          delete_user_meta($user_id, 'otw_user_info_mobile', $mobile_devices[$key]);
        }
      }
    }
  }
}
