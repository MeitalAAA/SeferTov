<?php 
namespace Gloo\Modules\SalesForceCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SalesForceAfterSubmit extends \ElementorPro\Modules\Forms\Classes\Action_Base{
  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'salesforceleads';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'SalesForce CRM Form Submit Action', 'gloo_for_elementor' );
	}

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );

		$contactinfo = '';

		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}
		
		if ( isset($settings['salesforce_list']) && is_array($settings['salesforce_list']) && count($settings['salesforce_list']) >= 1 ) {
      $i = 0;
      $body = array();
			foreach (  $settings['salesforce_list'] as $item ) {
				
				if (! empty( $fields[ $item['salesforce_var_name'] ] ) ) {
					$item_value = sanitize_text_field( $fields[ $item['salesforce_var_name'] ] );
					if($item_value){
						$body[$item['salesforce_var_value']] = $fields[ $item['salesforce_var_name'] ];			
						$i++;
					}
				}elseif($item['salesforce_var_name'] && mb_substr($item['salesforce_var_name'], 0, 1) === '{' && mb_substr($item['salesforce_var_name'], -1, 1) === '}'){
					$body[$item['salesforce_var_value']] = str_replace(array("{","}"), array("",""), $item['salesforce_var_name']);	
				}

      }

      if ($i && isset($settings['salesforce_orgid']) && $settings['salesforce_orgid'])
        $update = $this->post_sales_force_lead($body, $settings['salesforce_orgid']);
      
    }
		
		
	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {

		$widget->start_controls_section(
			'section_salesforceleads',
			[
				'label' => __( 'SalesForce CRM Form Submit Action', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

    $widget->add_control(
			'salesforce_orgid',
			[
				'label' => __( 'SalesForce Organization ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your Salesforce.com Organization ID.', 'gloo_for_elementor' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'salesforce_var_name', [
				'label' => __( 'Form field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Title' , 'gloo' ),
				'label_block' => true,
				'classes' => "gloo_salesforce_var_name",
			]
		);
		
		$leads_fields = '{"1":{"label":"First Name","max":"40","name":"first_name","type":"text"},"2":{"label":"Last Name","max":"80","name":"last_name","type":"text","req":"true"},"3":{"label":"Email","max":"80","name":"email","type":"text","req":"true"},"4":{"label":"Company","max":"40","name":"company","type":"text"},"5":{"label":"City","max":"40","name":"city","type":"text"},"6":{"label":"State/Province","max":"20","name":"state","type":"text"},"7":{"label":"Salutation","name":"salutation","type":"select"},"8":{"label":"Title","max":"40","name":"title","type":"text"},"9":{"label":"Website","max":"80","name":"URL","type":"text"},"10":{"label":"Phone","max":"40","name":"phone","type":"text"},"11":{"label":"Mobile","max":"40","name":"mobile","type":"text"},"12":{"label":"Fax","max":"40","name":"fax","type":"text"},"13":{"label":"Address","name":"street","type":"select"},"14":{"label":"Zip","max":"20","name":"zip","type":"text"},"15":{"label":"Country","max":"40","name":"country","type":"text"},"16":{"label":"Description","name":"description","type":"select"},"17":{"label":"Lead Source","name":"lead_source","type":"select"},"18":{"label":"Industry","name":"industry","type":"select"},"19":{"label":"Rating","name":"rating","type":"select"},"20":{"label":"Annual Revenue","name":"revenue","type":"text"},"21":{"label":"Employees","name":"employees","type":"text"},"22":{"label":"Email Opt Out","name":"emailOptOut","type":"checkbox"},"23":{"label":"Fax Opt Out","name":"faxOptOut","type":"checkbox"},"24":{"label":"Do Not Call","name":"doNotCall","type":"checkbox"}}';
  	//$web['Case']='{"1":{"label":"Contact Name","max":"80","name":"name","type":"text"},"2":{"label":"Email","max":"80","name":"email","type":"text"},"3":{"label":"Phone","max":"40","name":"phone","type":"text"},"4":{"label":"Subject","max":"80","name":"subject","type":"text"},"5":{"label":"Description","name":"description","type":"select"},"6":{"label":"Company","max":"80","name":"company","type":"text"},"7":{"label":"Type","name":"type","type":"select"},"8":{"label":"Status","name":"status","type":"select"},"9":{"label":"Case Reason","name":"reason","type":"select"},"10":{"label":"Priority","name":"priority","type":"select"}}'; 
		
		$leads_fields = json_decode($leads_fields);
    $fields_array = array();
    foreach($leads_fields as $lead_field){
      $fields_array[$lead_field->name] = $lead_field->label;
		}
		/*$repeater->add_control(
			'list_variable_value', [							
				'label' => __( 'Salesforce field name', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				//'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Content' , 'gloo' ),
				//'show_label' => false,
				'default' => '',
				'options' => $fields_array,
				'label_block' => true,
			]
		);*/
		$repeater->add_control(
			'salesforce_var_value', [
        'label' => __( 'Salesforce field name', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
      ]
		);

		$widget->add_control(
			'salesforce_list',
			[
				'label' => __( 'Items', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'salesforce_var_name' => __( 'Form field ID', 'gloo_for_elementor' ),
						'salesforce_var_value' => __( 'Sales force name', 'gloo_for_elementor' ),
					],					
				],
				'title_field' => '{{{ salesforce_var_name }}}',
			]
		);

		$possible_values = 'Possible SalesForce Field names are ';
		foreach($leads_fields as $key=>$lead_field){
			$possible_values .= $lead_field->name.', ';
		}
		$possible_values .= 'debug and debugEmail etc.';
		$widget->add_control(
			'salesforce_list_description',
			[
				'raw' => __( 'Last Name and Email are required. <br />You can also put static values in "Form Field ID" input for example {static name}<br />'.$possible_values, 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-descriptor',
			]
		);
		
		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		/*unset(
			$element['zohocampaign_accesstoken'],
			$element['zohocampaign_list'],
			$element['zohocampaign_email_field'],
			$element['zohocampaign_first_name_field'],
			$element['zohocampaign_last_name_field']
		);*/
  }
 

	/**
	 *post_sales_force_lead
	 */
  public function post_sales_force_lead($post = array(), $org_id = '', $object='Lead', $test = false){
    
    /*$post = array(
      'first_name' => 'Tahir ',
      'last_name' => 'LName',
      'email' => 'tahir@otw.design',
      'retURL' => get_bloginfo('url'),
    );*/


    global $wp_version;
    
    switch($object) {
      case 'Case':
        $post['orgid'] = $org_id;
        break;
      case 'Lead':
        $post['oid'] =  $org_id;
        break;
    }

    if(empty($post['oid']) && empty($post['orgid'])) {
      return NULL;
    }

    $header=array(
      'user-agent' => 'Gloo Salesforce Plugin - WordPress/'.$wp_version.'; '.get_bloginfo('url')
    );

    if(!empty($post) && is_array($post)){
      if(!array_key_exists('retURL', $post))
        $post['retURL'] =  get_bloginfo('url');

      $files = $body = array();
      foreach($post as $k=>$v){
        if(is_array($v)){
            foreach($v as $vv){
              $body[]=urlencode($k).'='.urlencode($vv);       
            }
        }else{
          $body[]=urlencode($k).'='.urlencode($v);       
        }
      }
      
      $post=implode('&',$body);
    }

    $args = array(
      'body'      => $post,
      'headers'   => $header,
      'timeout'     => 60,
      //'sslverify' => true,
      //'redirection' => 5,
      //'blocking'    => true,
      //'httpversion' => '1.0',
    );

    $sub = $test ? 'test' : 'webto' ;
    
    $url =  sprintf('https://%s.salesforce.com/servlet/servlet.WebTo%s?encoding=UTF-8', $sub, $object);
    
    $result = wp_remote_post($url, $args);

    //db($result);  die();
    // There was an error
    if(is_wp_error( $result )) {
    // return NULL;
    }
    $done = array('entry created'=>'TRUE');
    // Find out what the response code is
    $code = wp_remote_retrieve_response_code( $result );
    // Salesforce should ALWAYS return 200, even if there's an error.
    // Otherwise, their server may be down.
    if( intval( $code ) !== 200) {
      return NULL;
    }
    // If `is-processed` isn't set, then there's no error.
    elseif(!isset($result['headers']['is-processed'])) {
      return $done;
    }
    // If `is-processed` is "true", then there's no error.
    else if ($result['headers']['is-processed'] === "true") {
      return $done;
    }
    // But if there's the word "Exception", there's an error.
    /*  else if(strpos($result['headers']['is-processed'], 'Exception')) {
    return NULL;
    }*/
    return NULL;
  }
}