<?php 
namespace Gloo\Modules\ZohoCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoCampaignsAfterSubmit extends \ElementorPro\Modules\Forms\Classes\Action_Base{

	public $name;
	public $namePrefix;

	public function __construct($name = 'zohoformsubmitaction', $namePrefix = ''){
		$this->name = $name;
		$this->namePrefix = $namePrefix;
	}
  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return $this->name.$this->namePrefix;
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
		if($this->namePrefix)
			return __( 'Zoho CRM Form Submit Action - '.$this->namePrefix, 'gloo' );
		else
			return __( 'Zoho CRM Form Submit Action', 'gloo' );
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
		
		
		$update = $this->insert_zoho_lead_record($settings, $fields);
		if(!$update){
			zoho_crm_dynamic_form_action()->get_access_token_from_refresh_token();
			$this->insert_zoho_lead_record($settings, $fields);
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

		$zoho_modules = zoho_crm_dynamic_form_action()->get_option('zoho_modules');
		//$zoho_modules = SerializeStringToArray($zoho_modules);

		$widget->start_controls_section(
			'section_'.$this->get_name(),
			[
				'label' => __( $this->get_label(), 'gloo' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		
		
		if($zoho_modules && is_array($zoho_modules) && count($zoho_modules) >= 1){
			$widget->add_control(
				'zoho_module'.$this->namePrefix,
				[
					'label' => __( 'Zoho Module', 'gloo' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					//'type' => \Elementor\Controls_Manager::TEXT,
					//'default' => __( 'List Content' , 'gloo' ),
					//'show_label' => false,
					'default' => '',
					'options' => $zoho_modules,
					'label_block' => true,
					'classes' => "gloo_zoho_module_dropdown",
				]
			);
		}else{
			$widget->add_control(
				'zoho_module'.$this->namePrefix,
				[
					'label' => __( 'Zoho Module', 'gloo' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic' => [
						'active' => true,
					],
				]
			);
		}

		$widget->add_control(
      'zoho_module_is_acceptance'.$this->namePrefix,
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Use Acceptance', 'gloo' ),
      ]
    );

		$widget->add_control(
			'zoho_module_acceptance_id'.$this->namePrefix,
			[
				'label' => __( 'Acceptance Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'after',
				'condition' => ['zoho_module_is_acceptance'.$this->namePrefix => 'yes']
			]
		);


		/*$widget->add_control(
      'zoho_module_send_uploaded_files'.$this->namePrefix,
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __( 'Send Uploaded Files', 'gloo' ),
				'default' => 'yes',
      ]
		);

		$widget->add_control(
      'zoho_module_require_approval'.$this->namePrefix,
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Require Approval', 'gloo' ),
      ]
		);

		$widget->add_control(
      'zoho_module_work_flow_trigger'.$this->namePrefix,
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Enable Work Flow Trigger', 'gloo' ),
      ]
		);

		$widget->add_control(
      'zoho_module_email_opt_out'.$this->namePrefix,
      [
				'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Email Opt Out', 'gloo' ),
      ]
		);*/

		/*$widget->add_control(
      'zoho_module_email_opt_out',
      [
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
        'label' => __( 'Email Opt Out', 'gloo' ),
      ]
		);
		$widget->add_control(
			'zoho_module_email_opt_out_desc',
			[
				'raw' => __( 'Possible Values: yes/no, on/off, 1/0, true/false', 'gloo' ),
        'type' => \Elementor\Controls_Manager::RAW_HTML,
			]
    );*/
		
		
		$list_variable_name = array(
			'label' => __( 'Form field ID', 'gloo' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			//'default' => __( 'List Title' , 'gloo' ),
			'label_block' => true,
			'classes' => "gloo_variable_name",
		);
		if($zoho_modules && is_array($zoho_modules) && count($zoho_modules) >= 1){
			foreach($zoho_modules as $key=>$zoho_module){

				$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'list_variable_name', $list_variable_name
				);
				
				$zoho_leads_fields_string = get_option('zoho_'.$key.'_fields');
				$zoho_leads_fields = SerializeStringToArray($zoho_leads_fields_string);
				//$zoho_leads_fields = array(); //testing
				
				if($zoho_leads_fields && is_array($zoho_leads_fields) && count($zoho_leads_fields) >= 1){
					$repeater_zoho_args = array(	
						'label' => __( 'Zoho field name', 'gloo' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						//'type' => \Elementor\Controls_Manager::TEXT,
						//'default' => __( 'List Content' , 'gloo' ),
						//'show_label' => false,
						'default' => '',
						'options' => $zoho_leads_fields,
						'label_block' => true,
						'classes' => "gloo_zoho_field_name",
						//'condition' => ['zoho_module[value]' => $key],
					);
				}else{
					$repeater_zoho_args = array(			
						'label' => __( 'Zoho field name', 'gloo' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => true,
						'dynamic' => [
							'active' => true,
						],
						//'condition' => ['zoho_module[value]' => $key],
					);
				}
				
				
				$repeater->add_control(
					'list_variable_value', $repeater_zoho_args
				);
				$widget->add_control(
					'list_'.$key.$this->namePrefix,
					[
						'label' => __( 'Items', 'gloo' ),
						'type' => \Elementor\Controls_Manager::REPEATER,
						'fields' => $repeater->get_controls(),
						'default' => [
							[
								'list_variable_name' => __( 'Form field ID', 'gloo' ),
								'list_variable_value' => __( 'Zoho field name', 'gloo' ),
							],					
						],
						'title_field' => '{{{ list_variable_name }}}',
						'condition' => ['zoho_module'.$this->namePrefix.'[value]' => $key],
					]
				);
			}
		}
		/*$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'list_variable_name', $list_variable_name
		);

		// $zoho_leads_fields = array(''=> '--Select--');
		// $selected_zoho_module = $widget->get_settings('zoho_module');
		// if($selected_zoho_module)
		// 	$zoho_leads_fields = zoho_crm_dynamic_form_action()->get_module_fields($selected_zoho_module);
		
		$zoho_leads_fields_string = zoho_crm_dynamic_form_action()->get_option('zoho_leads_fields');
		$zoho_leads_fields = SerializeStringToArray($zoho_leads_fields_string);
		//$zoho_leads_fields = array(); //testing
		
		if($zoho_leads_fields && is_array($zoho_leads_fields) && count($zoho_leads_fields) >= 1){
			$repeater_zoho_args = array(		
				'label' => __( 'Zoho field name', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				//'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Content' , 'gloo' ),
				//'show_label' => false,
				'default' => '',
				'options' => $zoho_leads_fields,
				'label_block' => true,
				'classes' => "gloo_zoho_field_name",
				//'condition' => ['zoho_module[value]' => $key],
			);
		}else{
			$repeater_zoho_args = array(			
				'label' => __( 'Zoho field name', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				//'condition' => ['zoho_module[value]' => $key],
			);
		}

		$repeater->add_control(
			'list_variable_value', $repeater_zoho_args
		);
		

		$widget->add_control(
			'list',
			[
				'label' => __( 'Items', 'gloo' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_variable_name' => __( 'Form field ID', 'gloo' ),
						'list_variable_value' => __( 'Zoho field name', 'gloo' ),
					],					
				],
				'title_field' => '{{{ list_variable_name }}}',
				'condition' => ['zoho_module[value]' => 'Leads'],
			]
		);*/

		
		
		
		
		
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
	 *update_zoho_lead_record
	 */
	public function insert_zoho_lead_record($settings, $fields){
		
		$zoho_modules = zoho_crm_dynamic_form_action()->get_option('zoho_modules');
		if($zoho_modules && is_array($zoho_modules) && count($zoho_modules) >= 1 && $settings['zoho_module'.$this->namePrefix] && array_key_exists($settings['zoho_module'.$this->namePrefix], $zoho_modules)){
			foreach($zoho_modules as $key=>$zoho_module){
				
				if (isset($settings['list_'.$key.$this->namePrefix]) && is_array($settings['list_'.$key.$this->namePrefix]) && count($settings['list_'.$key.$this->namePrefix]) >= 1 ) {

					$endpoint = 'https://www.zohoapis.'.zoho_crm_dynamic_form_action()->get_option('zoho_data_center').'/crm/v2/'.$settings['zoho_module'.$this->namePrefix];
		
					$body = array();
					$body['data'][0] = array();
					$i = 0;
					foreach (  $settings['list_'.$key.$this->namePrefix] as $item ) {
						
						if (! empty( $fields[ $item['list_variable_name'] ] ) ) {
							$item_value = sanitize_text_field( $fields[ $item['list_variable_name'] ] );
							if($item_value){
								$body['data'][0][$item['list_variable_value']] = $item_value;				
								$i++;
							}
						}elseif($item['list_variable_name'] && mb_substr($item['list_variable_name'], 0, 1) === '{' && mb_substr($item['list_variable_name'], -1, 1) === '}'){
							$item_value = sanitize_text_field( str_replace(array("{","}"), array("",""), $item['list_variable_name']) );
							if($item_value){
								$body['data'][0][$item['list_variable_value']] = $item_value;				
								$i++;
							}
						}
		
					}
		
					$insert = true;
					if (!empty($settings['zoho_module_is_acceptance'.$this->namePrefix]) && !empty($settings['zoho_module_acceptance_id'.$this->namePrefix])){
						if(!(sanitize_text_field( $fields[ $settings['zoho_module_acceptance_id'.$this->namePrefix] ] )))
							$insert = false;
					}
					
					if($i && $insert){
						$body = wp_json_encode( $body );
						$options = [
							'body'        => $body,
							'headers'     => [
								'Authorization' => 'Zoho-oauthtoken '.zoho_crm_dynamic_form_action()->get_option('zoho_access_token'),
								'Content-Type' => 'application/json',
							],
							'timeout'     => 60,
							'redirection' => 5,
							'blocking'    => true,
							'httpversion' => '1.0',
							'sslverify'   => true,
							'data_format' => 'body',
						];
						$response = wp_remote_post( $endpoint, $options );
						//update_option('zoho_campaign_last_inserted', $response);
						if(isset($response['response']) && isset($response['response']['message']) && $response['response']['message'] == 'Created')
							return true;
						else 
							return false;					
					}
					//if $i
					return true;
				}//if list settings.
			}
		}

		
		
	}//public function


	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'ajax_get_zoho_module_fields', [ $this, 'ajax_get_zoho_module_fields' ] );
		//$ajax_manager->register_ajax_action( 'editor_get_wp_widget_form', [ $this, 'ajax_get_wp_widget_form' ] );
	}
	
	public function ajax_get_zoho_module_fields()
  {
    return 'test-ajax';
  }
}