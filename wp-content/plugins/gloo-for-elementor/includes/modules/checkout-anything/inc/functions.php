<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/******************************************/
/***** Debug functions start from here **********/
/******************************************/
if(!function_exists("alert")){

  function alert($alertText){
  	echo '<script type="text/javascript">';
  	echo "alert(\"$alertText\");";
  	echo "</script>";
  } // function alert

}// if end


if(!function_exists("js_log")){
  function js_log($alertText){
  	echo '<script type="text/javascript">';
    echo "console.log(\"$alertText\")";
  	echo "</script>";
  } // function alert

}// if end


if(!function_exists('db')){
	function db($array1)
	{
		echo "<pre>";
		var_dump($array1);
		echo "</pre>";
	}
}

if(!function_exists('dbt')){
	function dbt($array1, $ip = '', $exit = true)
	{
		if(in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', $ip])){
			echo "<pre>";
			var_dump($array1);
			echo "</pre>";
			if($exit)
				exit();
		}
		
	}
}



if(!function_exists('dbh')){
  function dbh($debug_data){
    echo '<div style="display:none">';
    db($debug_data);
    echo '</div>';
  }
}


if(!function_exists('get_file_time')){
  function get_file_time($file){
      return date("ymd-Gis", filemtime( $file ));
  }
}




/******************************************/
/***** arrayToSerializeString **********/
/******************************************/
if(!function_exists("ArrayToSerializeString")){
  function ArrayToSerializeString($array){
    if(isset($array) && is_array($array) && count($array) >= 1)
      return serialize($array);
    else
      return serialize(array());
  }
}


/******************************************/
/***** SerializeStringToArray **********/
/******************************************/
if(!function_exists("SerializeStringToArray")){
  function SerializeStringToArray($string){
    if(isset($string) && is_array($string) && count($string) >= 1)
      return $string;
    elseif(isset($string) && $string && @unserialize($string)){
      return unserialize($string);
    }else
      return array();
  }
}

/******************************************/
/***** ArraytoSelectList **********/
/******************************************/
if(!function_exists("ArraytoSelectList")){
  function ArraytoSelectList($array, $sValue = ""){
    $output = '';
    foreach($array as $key=>$value){
      if($key == $sValue)
        $output .= '<option value="'.esc_attr($key).'" selected="selected">'.esc_html($value).'</option>';
      else
        $output .= '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
    }
    return $output;
	}
}

if(!function_exists("otw_textarea_sanitization")){
  function otw_textarea_sanitization($text, $bballowedtags = false){
    if(isset($text) && $text && $text != " " && is_string($text)){
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
        return '';
    }else
      return '';
  }
}

if(!function_exists("otw_textfield_sanitization")){
  function otw_textfield_sanitization($text = ''){
    $output = '';
    if(is_string($text))
      $output = sanitize_text_field(stripslashes(trim($text)));    
    return $output;
  }
}

function create_woo_order() {

  global $woocommerce;

  $address = array(
      'first_name' => '111Joe',
      'last_name'  => 'Conlin',
      'company'    => 'Speed Society',
      'email'      => 'joe@testing.com',
      'phone'      => '760-555-1212',
      'address_1'  => '123 Main st.',
      'address_2'  => '104',
      'city'       => 'San Diego',
      'state'      => 'Ca',
      'postcode'   => '92121',
      'country'    => 'US'
  );

  // Now we create the order
  $order = wc_create_order();

  // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
  $order->add_product( get_product('275962'), 1); // This is an existing SIMPLE product
  $order->set_address( $address, 'billing' );
  //
  $order->calculate_totals();
  $order->update_status("Completed", 'Imported order', TRUE);  
}