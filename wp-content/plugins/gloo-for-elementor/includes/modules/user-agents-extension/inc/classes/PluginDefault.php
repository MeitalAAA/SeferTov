<?php
namespace Gloo\Modules\UserAgentsExtension;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    
		add_action( 'user_register', [ $this, 'register_new_user' ] );
    add_action( 'wp_login', [$this, 'wp_login'], 10, 2 );

    add_shortcode( 'gloo_allowed_devices',  [$this, 'gloo_allowed_devices_shortcode'] );
    add_shortcode( 'gloo_approved_devices',  [$this, 'gloo_approved_devices_shortcode'] );
    
		if($this->is_compatible()){
			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_tags' ] );
		}

    if(is_admin()){
      add_action( 'edit_user_profile', array($this, 'edit_user_profile'));
      add_action( 'show_user_profile', array($this, 'edit_user_profile'));
      add_action( 'profile_update', array($this, 'update_user_profile'));
    }
    
    //add_shortcode( 'otw_is_same_device',  'otw_qa_shortcode' );
  }// construct function end here



  /******************************************/
  /***** register_new_user  **********/
  /******************************************/
  public function register_new_user($user_id){
    
		$this->register_new_device($user_id, array());
		
    //$user = get_user_by( 'id', $user_id);

		//global $wpdb;
    //$wpdb->update($wpdb->prefix.'bbwp_invitation_system', array('invite_accepted' => 1), array('email' => $user->user_email, 'code' => $code), array('%d'), array("%s", "%s"));
    
    
  }

	/******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function register_tags($dynamic_tags){
    
		/*\Elementor\Plugin::$instance->dynamic_tags->register_group( 'user-agent-request-variables', [
			'title' => 'User Agents Variables' 
		] );*/

		// Include the Dynamic tag class file
		//include_once( 'path/to/dynamic/tag/class/file' );

		// Finally register the tag
		//$RequestDynamicTags = new RequestDynamicTags();
		//$RequestDynamicTagsTwo = new RequestDynamicTags('current-session-user-agent', 'Current Session User Agent');
		//$dynamic_tags->register_tag( $RequestDynamicTags );
		//$dynamic_tags->register_tag( $RequestDynamicTagsTwo );
		$dynamic_tags->register_tag( '\Gloo\Modules\UserAgentsExtension\RequestDynamicTags' );
		//$dynamic_tags->register_tag( '\Gloo\Modules\UserAgentsExtension\SessionRequestDynamicTags' );
		
  }
	
  /******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function wp_login($user_login, $user){

    
    $gloo_stored_devices = SerializeStringToArray(get_user_meta( $user->ID, 'gloo_allowed_devices', true ));
    $allowed_devices = $this->get_option('allowed_devices');
    $stored_code = get_user_meta($user->ID, 'gloo_verification_code', true);
    /*if($gloo_stored_devices && is_array($gloo_stored_devices) && count($gloo_stored_devices) >= 1){
      
    }*/
    if($allowed_devices > count($gloo_stored_devices) && isset($_SESSION['register_new_device']) && $_SESSION['register_new_device'] && isset($_SESSION['register_new_device']['gloo_code']) && $_SESSION['register_new_device']['gloo_code'] == $stored_code){
      $this->register_new_device($user->ID, $gloo_stored_devices);
    }

  }
  
  /******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function register_new_device($user_id, $gloo_stored_devices){
    if(isset($_SERVER['HTTP_USER_AGENT'])){
      
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
      $array_key = count($gloo_stored_devices)+1;
			//update_user_meta($user_id, 'gloo_user_agents', $userAgent);
      //$gloo_stored_devices[$array_key]['user_agent'] = $userAgent;
      $new_device = array('user_agent' => $userAgent);
      \DeviceDetector\Parser\Device\AbstractDeviceParser::setVersionTruncation(\DeviceDetector\Parser\Device\AbstractDeviceParser::VERSION_TRUNCATION_NONE);
      
      $dd = new \DeviceDetector\DeviceDetector($userAgent);
      $dd->parse();
      //if (!$dd->isBot()) { 
        
        $ip_address = getIPAddress();
        $clientInfo = $dd->getClient();
        $osInfo = $dd->getOs();
        $device = $dd->getDeviceName();
        $brand = $dd->getBrandName();
        //$model = $dd->getModel();
        
        if($ip_address){
          $new_device['ip'] = $ip_address;
          //$gloo_stored_devices[$array_key]['ip'] = $ip_address;
          //update_user_meta($user_id, 'gloo_stored_agent_ip', $ip_address);
        }
        
        if($clientInfo && isset($clientInfo['name'])){
          $new_device['browser'] = $clientInfo['name'];
          //$gloo_stored_devices[$array_key]['browser'] = $clientInfo['name'];
          //update_user_meta($user_id, 'gloo_stored_browser', $clientInfo['name']);
        }

        if($osInfo && isset($osInfo['name'])){
          $new_device['os'] = $osInfo['name'];
          //$gloo_stored_devices[$array_key]['os'] = $osInfo['name'];
          //update_user_meta($user_id, 'gloo_stored_os', $osInfo['name']);
        }

        if($device){
          $new_device['device'] = $device;
          //$gloo_stored_devices[$array_key]['device'] = $device;
          //update_user_meta($user_id, 'gloo_stored_device_name', $device);
        }

        if($brand){
          $new_device['brand'] = $brand;
          //$gloo_stored_devices[$array_key]['brand'] = $brand;
          //update_user_meta($user_id, 'gloo_stored_brand_name', $brand);
        }
        if($new_device && is_array($new_device) && count($new_device) >= 1){
          $gloo_stored_devices[] = $new_device;
          //$gloo_stored_devices[$array_key] = $new_device;
        }

        update_user_meta($user_id, 'gloo_allowed_devices', ArrayToSerializeString($gloo_stored_devices));
        update_user_meta($user_id, 'gloo_verification_code', '');
        if(isset($_SESSION['register_new_device']))
          unset($_SESSION['register_new_device']);

        $allowed_devices = $this->get_option('allowed_devices');

        if($allowed_devices > count($gloo_stored_devices)){
          $this->send_new_device_email($user_id, $gloo_stored_devices);
        }
        
      //}
    }
  }

  /******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function gloo_allowed_devices_shortcode($atts){
    /*extract(shortcode_atts(array(
          'redirect_url' => false,
    ), $atts));*/
    $output = '';
    $allowed_devices = $this->get_option('allowed_devices');
    if($allowed_devices)
      $output = $allowed_devices;
    return $output;
  }
  
  /******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function gloo_approved_devices_shortcode($atts){
    
    extract(shortcode_atts(array(
          'user_id' => false,
    ), $atts));

    $output = 0;

    if($user_id == false && isset($_SESSION['register_new_device']) && $_SESSION['register_new_device'] && isset($_SESSION['register_new_device']['gloo_code']) && isset($_SESSION['register_new_device']['gloo_uid']) ){
      $user_id = $_SESSION['register_new_device']['gloo_uid'];
    }

    if($user_id){
      $user = get_user_by('id', $user_id);
      if($user){
        $gloo_stored_devices = SerializeStringToArray(get_user_meta( $user->ID, 'gloo_allowed_devices', true ));
        $output = count($gloo_stored_devices);
      }
    }
    
   
    return $output;
  }


  /******************************************/
  /***** function to edit user profile start from here **********/
  /******************************************/
  function edit_user_profile($user){
    $approved_devices = (int) do_shortcode('[gloo_approved_devices user_id="'.$user->ID.'"]');
    $allowed_devices = (int) do_shortcode('[gloo_allowed_devices]');
    $send_email = '';
    if($allowed_devices > $approved_devices){
      $send_email .= '<a href="#" class="button wp-generate-pw hide-if-no-js '.$this->prefix('send_new_device_email_button').'" id="'.$this->prefix('send_new_device_email_button').'">Send email for new device registration.</a>';
    }
    //$current_url = home_url(add_query_arg(array($_GET), $wp->request));
    //echo '<h3>'.__('OTW Invitation System', 'td-bbwp-invitation-system').'</h3>';
    echo '<table class="form-table" role="presentation">'; 
    echo '<tr class="user-role-wrap">'; 
    echo '<th><label for="gloo_approved_devices">'.__('Approved Devices', 'gloo').'</label></th>';
    echo '<td><span style="line-height:30px; margin-right:10px;">'.$approved_devices.__(' out of ', 'gloo').$allowed_devices.'</span>'.$send_email.'
    <input type="hidden" value="no" name="'.$this->prefix('send_new_device_email').'" id="'.$this->prefix('send_new_device_email').'" />
    </td></tr>';
    echo '</table>';
  }

  
  /******************************************/
  /***** function to update user profile start from here **********/
  /******************************************/
  public function update_user_profile($user_id/*, $old_user_data*/){
    
    if(isset($_REQUEST[$this->prefix('send_new_device_email')]) && $_REQUEST[$this->prefix('send_new_device_email')] == 'yes'){
      unset($_REQUEST[$this->prefix('send_new_device_email')]);
      $gloo_stored_devices = SerializeStringToArray(get_user_meta( $user_id, 'gloo_allowed_devices', true ));
      $this->send_new_device_email($user_id, $gloo_stored_devices);
    }
  }

  /******************************************/
  /***** function to edit user profile start from here **********/
  /******************************************/
  public function send_new_device_email($user_id, $gloo_stored_devices){
    $userData = get_user_by('id', $user_id);
    $userFullName = $userData->user_nicename;
    $email = $userData->user_email;
    $subject = $this->get_option('email_subject');
    $pageURL = $this->get_option('login_page_url');
    $verify_code = generate_random_int(8);
    $pageURL = esc_url(add_query_arg(array('uid' => $user_id,'gloo_code' => $verify_code), $pageURL));
    $find_array = array("{login_page_url}", "{approved_devices}", "{allowed_devices}");
    $replace_array = array($pageURL, count($gloo_stored_devices), $this->get_option('allowed_devices'));
    $user_message = str_replace($find_array, $replace_array, get_option($this->prefix("email_message")));
    $user_message = wp_kses_post($user_message);
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: '.$this->get_option('from_name').' <'.$this->get_option('from_email').'>'. "\r\n";
    //db(wp_mail($email, $subject, $user_message, $headers));exit();
    //$email = 'tahirg.shahid@gmail.com';
    if(wp_mail($email, $subject, $user_message, $headers))
      update_user_meta($user_id, 'gloo_verification_code',$verify_code);
  }
} // BBWP_CustomFields class
