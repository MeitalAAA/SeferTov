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


if(!function_exists("db")){

  function db($array1){
  	echo "<pre>";
  	var_dump($array1);
  	echo "</pre>";
	}// function db

}// if



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
/***** Debug shutdown function **********/
/******************************************/
if(!function_exists("bb_shutdown")){
  function bb_shutdown()
  {
    if(!(defined('DOING_AJAX') && DOING_AJAX)){
      echo '<div class="bbwp_shutdown_time" style="color:#fff;position:fixed;bottom:20px;left:0px; background-color:#000;z-index:999999999;">'.$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"].' sec</div>';
    }
  }
}
//register_shutdown_function('bb_shutdown');


function choose_ashtopbar() {
  return "testing test";
}

function register_ashcodes(){
  add_shortcode('test-testing', 'choose_ashtopbar');
}