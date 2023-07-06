<?php
namespace Gloo\Modules\SalesForceCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SalesforceCrmDynamicFormAction{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_salesforce_crm_form_submit_action';
  static $options = array();


  /******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){

    // get the plugin options/settings.
    self::$options = SerializeStringToArray(get_option($this->prefix('options')));
    /*if(!isset(self::$options['zoho_data_center']))
      $this->set_option('zoho_data_center', 'com');*/
      
    if(is_admin()){

      $PageSettings = new PageSettings();

      // add javascript and css to wp-admin dashboard.
      //add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

      //add settings page link to plugin activation page.
      //add_filter( 'plugin_action_links_'.plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'plugin_action_links') );

      // Plugin activation hook
      //register_activation_hook(plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(plugin_basename(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE), array($this, 'PluginDeactivation'));

		}else{
      // add javascript and css to front end.
      //add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
    }

    add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );

    $this->get_sales_force_access_refresh_token();
    $this->get_access_token_from_refresh_token();
    if(isset($_GET['test']) && $_GET['test'] == 'admin'){

      //		$new_date_string = wp_date('d/m/Y');
		
// 		$sales_force_date_object = \DateTime::createFromFormat('d/m/Y', $new_date_string);
// 		db($sales_force_date_object->format('Y-m-d'));
// 		db(SerializeStringToArray(get_option('salesforce_Lead_fields')));
//       db(SerializeStringToArray(get_option('salesforce_Lead_fields_raw')));
      
      //db($this->get_object_fields($this->get_option("lead_object_id")));exit();
      
      //$instance = $this->get_option('salesforce_client_instance');
      //$api_version = $this->get_option('salesforce_api_version');

      //$endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/';
      //$endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/Lead/';
      //$query = "SELECT Id FROM Lead WHERE Email = 'tahir@otw.design'";
      //$endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/query?q='.urlencode($query);
      //$response = $this->salesforce_remote_get($endpoint);
      //db($response);exit();
      
      //$this->salesforce_put_record('Lead', array('LastName' => 'mehmood', 'FirstName' => 'Tahir', 'Email' => 'tahir@otw.design'), '00Q4J00000FG43rUAD');
      
      //$this->salesforce_put_record('Lead', array('LastName' => 'mehmood', 'FirstName' => 'Tahir', 'Email' => 'tahir@otw.design'));
    }

  }// construct function end here


	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.


	/******************************************/
	/***** localization function **********/
	/******************************************/
	/*public function plugins_loaded(){

		load_plugin_textdomain( 'gloo', false, plugin_dir_path(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE) . 'languages/' );

    if ( $this->is_compatible() ) {
      add_action( 'elementor/init', [ $this, 'elementor_init' ] );
      //add_action( 'elementor_pro/init', [ $this, 'init_pro' ] );
    }
    

  }*/
  // plugin_loaded


	/******************************************/
	/***** add settings page link in plugin activation screen.**********/
	/******************************************/
  /*public function plugin_action_links( $links ) {

     $links[] = '<a href="'. esc_url(get_admin_url(null, 'tools.php?page='.$this->prefix)) .'">'.__('Settings', 'gloo').'</a>';
     return $links;

  }*/
  // localization function


	/******************************************/
  /***** get option function**********/
  /******************************************/
  public function get_option($key){

    if(isset(self::$options[$key]))
      return self::$options[$key];
    else
      return NULL;

  }// get_option


	/******************************************/
  /***** get option function **********/
  /******************************************/
  public function set_option($key, $value){

      self::$options[$key] = $value;
      update_option($this->prefix.'_options', ArrayToSerializeString(self::$options));

	}// set_option
  



  /******************************************/
  /***** Check if elementor is loaded. **********/
  /******************************************/
  public function is_compatible() {
    
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
      $this->message = __('Elementor Form CRM require Elementor Pro to be installed and active.', 'gloo_for_elementor');
      $this->messageClass = 'warning';
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			return false;
		}
    return true;
    
  }
  


  /******************************************/
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notices() {
    //Value of $class can be error, success, warning and info
    if($this->message && $this->messageClass){ 
      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-'.$this->messageClass.' is-dismissible' ), esc_html( $this->message ) );
    }
  }
  

  
  /******************************************/
  /***** init_pro. **********/
  /******************************************/
  public function init_pro() {
    
    // Instantiate the action class
    $SalesForceAfterSubmit = new SalesForceAfterSubmit();
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $SalesForceAfterSubmit->get_name(), $SalesForceAfterSubmit );
    
    $quantity = 1;
    for($i = 1; $i <= $quantity; $i++){
      $SalesForceAfterSubmitAPI = new SalesForceAfterSubmitAPI('salesforceapi', $i);
      \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $SalesForceAfterSubmitAPI->get_name(), $SalesForceAfterSubmitAPI );
    }

  }


  /******************************************/
  /***** salesforce_remote_get function start from here *********/
  /******************************************/
  public function salesforce_remote_get($endpoint){
    $options = [
      'headers'     => [
        'Authorization' => 'Bearer '.$this->get_option('salesforce_access_token'),
        'Content-Type' => 'application/json',
      ],
      'timeout'     => 60,
      'redirection' => 5,
      'blocking'    => true,
      'httpversion' => '1.0',
      'sslverify'   => true,
      'data_format' => 'body',
    ];
    $response = wp_remote_get( $endpoint, $options );
    return $response;
  }

  /******************************************/
  /***** salesforce_remote_post function start from here *********/
  /******************************************/
  public function salesforce_remote_post($endpoint, $body = array(), $method = false){
    $options = [
      'headers'     => [
        'Authorization' => 'Bearer '.$this->get_option('salesforce_access_token'),
        'Content-Type' => 'application/json',
      ],
      'timeout'     => 60,
      'redirection' => 5,
      'blocking'    => true,
      'httpversion' => '1.0',
      'sslverify'   => true,
      'data_format' => 'body',
    ];
    if($body && is_array($body) && count($body) >= 1){
      $body = wp_json_encode( $body );
      $options['body'] = $body;
    }

    if($method)
      $options['method'] = $method;

    $response = wp_remote_post( $endpoint, $options );
    return $response;
	}

  /******************************************/
  /***** get_sales_force_access_refresh_token. **********/
  /******************************************/
  public function get_sales_force_access_refresh_token(){

    //$client_id = '3MVG9tzQRhEbH_K01OiWqbiZmnr.9qIY9LyjKGVOkNBGeQRAFdNCYLy_H1zlLvozjyMKPgnuo3sTg3aClfXYR';
    //$client_secret = '208135BB5668937D96A6F57A4587606571B7ABF689E2F349DAED69FB0817D262';
    $client_id = $this->get_option('salesforce_client_id');
    $client_secret = $this->get_option('salesforce_client_secret');
    
    $redirect_uri = trailingslashit(get_bloginfo('url'));
    // $redirect_uri = untrailingslashit(get_bloginfo('url'));
    //$redirect_uri = 'https://car-gears.targeta.co.il/';
    
    
    if(isset($_GET['code']) && $client_id && $client_secret){
      $endpoint = 'https://login.salesforce.com/services/oauth2/token';
      $body = array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'code' => $_GET['code']
      );
      
      //$endpoint = add_query_arg($body, $endpoint);
      
      //$body = wp_json_encode( $body );
      $options = [
        'body'        => $body,
        /*'headers'     => [
          //'Content-Type' => 'x-www-form-urlencoded',
          'Content-Type' => 'application/json',
        ],*/
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => true,
        'data_format' => 'body',
      ];
      $response = wp_remote_post( $endpoint, $options );
      if((!is_wp_error($response)) && $response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){
        $response_body = @json_decode($response['body'], true);
        
        if($response_body && isset($response_body['access_token']) && isset($response_body['refresh_token'])){
          //update_option('zoho_access_token', $response_body['access_token']);
          $this->set_option('salesforce_access_token', $response_body['access_token']);
          $this->set_option('salesforce_access_token_time', time());
          $this->set_option('salesforce_signature', $response_body['signature']);
          //update_option('salesforce_refresh_token', $response_body['refresh_token']);
          $this->set_option('salesforce_refresh_token', $response_body['refresh_token']);
          $this->get_object_fields($this->get_option('lead_object_id'));
        }

      }
      
    }
  }


  public function get_access_token_from_refresh_token(){
   
    $client_id = $this->get_option('salesforce_client_id');
    $client_secret = $this->get_option('salesforce_client_secret');

    $last_access_token_time = $this->get_option('salesforce_access_token_time');
    $get_access_token = true;
    if($last_access_token_time){
      $dateTimeObject = new \DateTime();
      $dateTimeObject->setTimestamp($last_access_token_time);
      $dateTimeObject->add(new \DateInterval('PT30M'));
      if(time() < $dateTimeObject->getTimestamp()){
        $get_access_token = false;        
      }
    }
    //$get_access_token = true;
    if($get_access_token && $client_id && $client_secret){
      
      $endpoint = 'https://login.salesforce.com/services/oauth2/token';
      
      $body = array(
        'grant_type' => 'refresh_token',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'refresh_token' => $this->get_option('salesforce_refresh_token'),
      );

      $options = [
        'body'        => $body,
        /*'headers'     => [
          //'Content-Type' => 'x-www-form-urlencoded',
          'Content-Type' => 'application/json',
        ],*/
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => true,
        'data_format' => 'body',
      ];
      $response = wp_remote_post( $endpoint, $options );
      
        if((!is_wp_error($response)) && $response && isset($response['body']) && $response['body']){
  
          $response_body = @json_decode($response['body'], true);
        
          if($response_body && isset($response_body['access_token'])){
  
            //update_option('salesforce_access_token', $response_body['access_token']);
            $this->set_option('salesforce_access_token', $response_body['access_token']);
            $this->set_option('salesforce_access_token_time', time());
            $this->set_option('salesforce_signature', $response_body['signature']);
            //zoho_crm_dynamic_form_action()->set_option('zoho_access_token', $response_body['access_token']);          
          }
          
        }
    }
	}
  

  /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  public function get_object_fields($module = 'Lead'){
    $salesforce_object_fields = array();
    //$instance = 'ybrown.lightning.force.com';
    $instance = $this->get_option('salesforce_client_instance');
    $api_version = $this->get_option('salesforce_api_version');
    $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/'.$module.'/describe/';
    
    $response = $this->salesforce_remote_get($endpoint);
    
    if((!is_wp_error($response)) && $response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){        
      $response_body = @json_decode($response['body'], true);

      if(isset($response_body['fields']) && is_array($response_body['fields']) && count($response_body['fields']) >= 1){
        foreach($response_body['fields'] as $field){
          if(isset($field['label']) && isset($field['name']))
            $salesforce_object_fields[$field['name']] = $field['label'];
        }          
      }

      if($salesforce_object_fields && count($salesforce_object_fields) >= 1){
        //$this->set_option('salesforce_'.$module.'_fields', $salesforce_object_fields);
        update_option('salesforce_'.$module.'_fields', ArrayToSerializeString($salesforce_object_fields));
      }
      //db($salesforce_object_fields);exit();
    }
    return $salesforce_object_fields;
  }


  /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  public function salesforce_put_record($object = 'Lead', $body = array(), $record_id = false){
    
    $instance = $this->get_option('salesforce_client_object_instance');
    //$instance = $this->get_option('salesforce_client_instance');
    $api_version = $this->get_option('salesforce_api_version');
    
    if($record_id){
      $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/'.$object.'/'.$record_id;
      $response = $this->salesforce_remote_post($endpoint, $body, 'PATCH');
    }else{
      $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/'.$object.'/';
      $response = $this->salesforce_remote_post($endpoint, $body);
    }
    
    return $response;
  }


  /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  public function salesforce_if_record_exist($object = 'Lead', $body = array()){
    $output = false;
    $instance = $this->get_option('salesforce_client_instance');
    $api_version = $this->get_option('salesforce_api_version');

    $whereclause = '';
    $i = 1;
    if($body && is_array($body) && count($body) >= 1){
      foreach($body as $key=>$value){
        if($i == 1)
          $whereclause .= ' WHERE ';
        $whereclause .= $key." = '".$value."'";
      }
      $i++;
    }
    $query = "SELECT Id FROM ".$object.$whereclause;
    $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/query?q='.urlencode($query);
    $response = $this->salesforce_remote_get($endpoint);

    if((!is_wp_error($response)) && $response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){
      $response_body = @json_decode($response['body'], true);
      if(isset($response_body['totalSize']) && $response_body['totalSize'] >= 1){
        $output = $response_body['records'][0]['Id'];
      }
    }
    
    return $output;
  }
  /******************************************/
  /***** php_curl_testing function start from here *********/
  /******************************************/
  public function php_curl_testing($object, $body = array(), $record_id = false){
    
    $instance = $this->get_option('salesforce_client_object_instance');
    //$instance = $this->get_option('salesforce_client_instance');
    $api_version = $this->get_option('salesforce_api_version');

    if($record_id)
      $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/'.$object.'/'.$record_id;
    else
      $endpoint = 'https://'.$instance.'/services/data/'.$api_version.'/sobjects/'.$object.'/';
    
    
    $body = wp_json_encode( $body );
    $headers = [
    'Authorization: Bearer '.$this->get_option('salesforce_access_token'),
    'Content-Type: application/json'
    ];
    //$endpoint = 'https://ybrown.my.salesforce.com/services/data/v20.0/sobjects/Lead/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $info = curl_getinfo($ch);
    curl_close($ch);
    db($response);
    db($http_status);
    db($info);
    exit();
  }

} // BBWP_CustomFields class