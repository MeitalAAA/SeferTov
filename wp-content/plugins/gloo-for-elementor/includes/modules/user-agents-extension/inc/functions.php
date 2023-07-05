<?php
// exit if file is called directly
if (!defined('ABSPATH')) {
  exit;
}

@session_start([
  'read_and_close' => true,
]);

if (isset($_REQUEST['uid']) && $_REQUEST['uid'] && isset($_REQUEST['gloo_code']) && $_REQUEST['gloo_code']) {
  $verify_code = sanitize_text_field($_REQUEST['gloo_code']);
  $gloo_uid = sanitize_text_field($_REQUEST['uid']);
  $stored_code = get_user_meta($gloo_uid, 'gloo_verification_code', true);
  if ($verify_code == $stored_code) {
    $_SESSION['register_new_device']['gloo_code'] = $verify_code;
    $_SESSION['register_new_device']['gloo_uid'] = $gloo_uid;
  }
}

/******************************************/
/***** Debug functions start from here **********/
/******************************************/
if (!function_exists("alert")) {

  function alert($alertText)
  {
    echo '<script type="text/javascript">';
    echo "alert(\"$alertText\");";
    echo "</script>";
  } // function alert

} // if end


if (!function_exists("db")) {

  function db($array1)
  {
    echo "<pre>";
    var_dump($array1);
    echo "</pre>";
  } // function db

} // if



/******************************************/
/***** arrayToSerializeString **********/
/******************************************/
if (!function_exists("ArrayToSerializeString")) {
  function ArrayToSerializeString($array)
  {
    if (isset($array) && is_array($array) && count($array) >= 1)
      return serialize($array);
    else
      return serialize(array());
  }
}


/******************************************/
/***** SerializeStringToArray **********/
/******************************************/
if (!function_exists("SerializeStringToArray")) {
  function SerializeStringToArray($string)
  {
    if (isset($string) && is_array($string) && count($string) >= 1)
      return $string;
    elseif (isset($string) && $string && @unserialize($string)) {
      return unserialize($string);
    } else
      return array();
  }
}

/******************************************/
/***** generate random integre value **********/
/******************************************/
if (!function_exists('generate_random_int')) {
  function generate_random_int($number_values)
  {
    $number_values = $number_values - 2;
    $lastid = rand(0, 9);
    for ($i = 0; $i <= $number_values; $i++) {
      $lastid .= rand(0, 9);
    }
    return $lastid;
  }
}

if (!function_exists('getIPAddress')) {

  function getIPAddress()
  {
    $ip = '';
    //whether ip is from the share internet  
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address  
    else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

}


/******************************************/
/***** generate random integre value **********/
/******************************************/
if (!function_exists('otw_sanitize_textarea')) {
  function otw_sanitize_textarea($text, $bballowedtags = false)
  {
    if(isset($text) && $text && $text != " "){
      if($bballowedtags === false){
        global $allowedposttags;
        $text = stripslashes(trim(wp_kses( $text, $allowedposttags)));
      }
			elseif ($bballowedtags === true) {
				$text = stripslashes(trim($text));
			}
      else
        $text = stripslashes(trim(wp_kses( $text, $bballowedtags)));
      if(strlen($text) >= 1){
        return $text;
      }else
        return false;
    }else
      return false;
  }
}


function gloo_user_agents_extension()
{
  return \Gloo\Modules\UserAgentsExtension\Plugin::instance();
}
