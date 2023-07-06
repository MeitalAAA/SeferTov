<?php
namespace Gloo\Modules\ZohoCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoCrmDynamicFormAction{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_zoho_crm_form_submit_action';
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
    if(!isset(self::$options['zoho_data_center']))
      $this->set_option('zoho_data_center', 'com');
      
    if(!isset(self::$options['zoho_form_actions_quantity']))
      $this->set_option('zoho_form_actions_quantity', '1');

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

    $this->save_zoho_access_and_refresh_token();
    $this->get_access_token_from_refresh_token();
    
    //db($this->get_module_fields());exit();

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
      $this->message = __('OTW Elementor Form CRM require Elementor Pro to be installed and active.', 'gloo');
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

    $quantity = $this->get_option('zoho_form_actions_quantity');
    for($i = 1; $i <= $quantity; $i++){
      $ZohoCampaignsAfterSubmit = new ZohoCampaignsAfterSubmit('zohoformsubmitaction', $i);
      \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ZohoCampaignsAfterSubmit->get_name(), $ZohoCampaignsAfterSubmit );
    }
    
    // Instantiate the action class
    //$ActiveTrailAfterSubmit = new ActiveTrailAfterSubmit();
    //$SalesForceAfterSubmit = new SalesForceAfterSubmit();
    //db($ActiveTrailAfterSubmit);exit();
    // Register the action with form widget
    //\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ActiveTrailAfterSubmit->get_name(), $ActiveTrailAfterSubmit );
    
    
    $ZohoCampaignsAfterSubmitOld = new ZohoCampaignsAfterSubmitOld();
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ZohoCampaignsAfterSubmitOld->get_name(), $ZohoCampaignsAfterSubmitOld );
    

    //\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $SalesForceAfterSubmit->get_name(), $SalesForceAfterSubmit );
  
  }


  /******************************************/
  /***** save_zoho_access_and_refresh_token **********/
  /******************************************/
  public function save_zoho_access_and_refresh_token(){
    
    if(isset($_GET['code']) && isset($_GET['location']) && isset($_GET['accounts-server'])){
      $endpoint = 'https://accounts.zoho.'.$this->get_option('zoho_data_center').'/oauth/v2/token';

      //$this->set_option('zoho_code', $_GET['code']);

      $body = array(        
        //'client_id' => '1000.D17N9WIZFYPHS7W21DCXTVNGFJS06C',
        //'client_secret' => '1598015e29c340194f537ed21260ab35288fe8ba62',
        //'redirect_uri' => 'https://www.salamdubai.co.il/',
        'grant_type' => 'authorization_code',
        'client_id' => $this->get_option('zoho_client_id'),
        'client_secret' => $this->get_option('zoho_client_secret'),
        'redirect_uri' => trailingslashit(get_bloginfo('url')),
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

      if($response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){
        
        $response_body = @json_decode($response['body'], true);
		  
        if($response_body && isset($response_body['access_token']) && isset($response_body['refresh_token'])){
          update_option('zoho_access_token', $response_body['access_token']);
          $this->set_option('zoho_access_token', $response_body['access_token']);
          $this->set_option('zoho_access_token_time', time());
          
          update_option('zoho_refresh_token', $response_body['refresh_token']);
          $this->set_option('zoho_refresh_token', $response_body['refresh_token']);

          $this->get_module_fields();
        }

      }
      
    }
    
  }


  /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  /*public function get_module_fields($module = 'Leads'){

    $endpoint = 'https://www.zohoapis.'.$this->get_option('zoho_data_center').'/crm/v2/settings/fields?module='.$module;      
    $options = [
      'headers'     => [
        //'Authorization' => 'Zoho-oauthtoken '.get_option('zoho_access_token'),
        'Authorization' => 'Zoho-oauthtoken '.$this->get_option('zoho_access_token'),
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
    

    if($response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){        
      $response_body = @json_decode($response['body'], true);

      $zoho_leads_fields = array();
      if(isset($response_body['fields']) && is_array($response_body['fields']) && count($response_body['fields']) >= 1){
        foreach($response_body['fields'] as $field){
          if(isset($field['field_label']) && isset($field['api_name']))
            $zoho_leads_fields[$field['api_name']] = $field['field_label'];
        }          
      }

      if($zoho_leads_fields && count($zoho_leads_fields) >= 1){
        $this->set_option('zoho_leads_fields', ArrayToSerializeString($zoho_leads_fields));
      }
      //db($zoho_leads_fields);exit();
      //$this->get_access_token_from_refresh_token();
    }

  }*/

    /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  public function get_module_fields($module = 'Leads'){
    $zoho_leads_fields = array();
    $endpoint = 'https://www.zohoapis.'.$this->get_option('zoho_data_center').'/crm/v2/settings/fields?module='.$module;      
    $options = [
      'headers'     => [
        //'Authorization' => 'Zoho-oauthtoken '.get_option('zoho_access_token'),
        'Authorization' => 'Zoho-oauthtoken '.$this->get_option('zoho_access_token'),
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
    
    if((!is_wp_error($response)) && $response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){        
      $response_body = @json_decode($response['body'], true);

      if(isset($response_body['fields']) && is_array($response_body['fields']) && count($response_body['fields']) >= 1){
        foreach($response_body['fields'] as $field){
          if(isset($field['field_label']) && isset($field['api_name']))
            $zoho_leads_fields[$field['api_name']] = $field['field_label'];
        }          
      }

      if($zoho_leads_fields && count($zoho_leads_fields) >= 1){
        //$this->set_option('zoho_'.$module.'_fields', ArrayToSerializeString($zoho_leads_fields));
        update_option('zoho_'.$module.'_fields', ArrayToSerializeString($zoho_leads_fields));
      }
      //db($zoho_leads_fields);exit();
      //$this->get_access_token_from_refresh_token();
    }
    
    return $zoho_leads_fields;
  }


  	/**
	 *get_access_token_from_refresh_token
	 */
	public function get_access_token_from_refresh_token(){
    
    $last_access_token_time = $this->get_option('zoho_access_token_time');
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
    if($get_access_token){
      $endpoint = 'https://accounts.zoho.'.$this->get_option('zoho_data_center').'/oauth/v2/token?refresh_token='.get_option('zoho_refresh_token').'&grant_type=refresh_token&client_id='.$this->get_option('zoho_client_id').'&client_secret='.$this->get_option('zoho_client_secret');
      $options = [
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => true,
        'data_format' => 'body',
      ];
      $response = wp_remote_post( $endpoint, $options );
      
        if(!is_wp_error($response) && isset($response['body']) && $response['body']){
  
          $response_body = @json_decode($response['body'], true);
        
          if($response_body && isset($response_body['access_token'])){
  
            update_option('zoho_access_token', $response_body['access_token']);
            $this->set_option('zoho_access_token', $response_body['access_token']);
            $this->set_option('zoho_access_token_time', time());
            //zoho_crm_dynamic_form_action()->set_option('zoho_access_token', $response_body['access_token']);          
          }
          
        }
    }
    
	}
  

  
  /******************************************/
  /***** get_leads_fields function start from here *********/
  /******************************************/
  public function get_modules_list(){

    $endpoint = 'https://www.zohoapis.'.$this->get_option('zoho_data_center').'/crm/v2/settings/modules';      
    $options = [
      'headers'     => [
        //'Authorization' => 'Zoho-oauthtoken '.get_option('zoho_access_token'),
        'Authorization' => 'Zoho-oauthtoken '.$this->get_option('zoho_access_token'),
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
    

    if((!is_wp_error($response)) && $response && isset($response['response']['message']) && $response['response']['message'] == 'OK'){        
      $response_body = @json_decode($response['body'], true);
      $zoho_leads_fields = array();
      if(isset($response_body['modules']) && is_array($response_body['modules']) && count($response_body['modules']) >= 1){
        foreach($response_body['modules'] as $field){
          if(isset($field['plural_label']) && isset($field['api_name']))
            $zoho_leads_fields[$field['api_name']] = $field['plural_label'];
            $this->get_module_fields($field['api_name']);
        }          
      }

      if($zoho_leads_fields && count($zoho_leads_fields) >= 1){
        $this->set_option('zoho_modules', $zoho_leads_fields);
      }
      //db($zoho_leads_fields);exit();
      //$this->get_access_token_from_refresh_token();
    }

  }

} // BBWP_CustomFields class

