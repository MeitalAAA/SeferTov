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
/***** SerializeStringToArray **********/
/******************************************/
if(!function_exists("JsonStringToArray")){
  function JsonStringToArray($string){
    $output = array();
    if(isset($string) && is_array($string) && count($string) >= 1)
      $output = $string;
    elseif(isset($string) && $string){
      $string = json_decode($string, true);
      if($string && is_array($string) && count($string) >= 1)
        $output = $string;
    }
    return $output;
  }
}

/******************************************/
/***** arrayToSerializeString **********/
/******************************************/
if(!function_exists("ArrayToJsonString")){
  function ArrayToJsonString($array){
    if(isset($array) && is_array($array) && count($array) >= 1)
      return json_encode($array);
    else
      return json_encode(array());
  }
}
