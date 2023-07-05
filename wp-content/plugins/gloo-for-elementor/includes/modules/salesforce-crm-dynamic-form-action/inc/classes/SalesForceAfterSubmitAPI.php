<?php 
namespace Gloo\Modules\SalesForceCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SalesForceAfterSubmitAPI extends \ElementorPro\Modules\Forms\Classes\Action_Base{

  public $name;
	public $namePrefix;
	public $prefix = 'gloo_salesforce_crm_form_submit_action';

	public function __construct($name = 'salesforceapi', $namePrefix = ''){
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
		return __( 'SalesForce CRM API Submit Action', 'gloo' );
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
		

    if ( isset($settings['list_Lead'.$this->namePrefix]) && is_array($settings['list_Lead'.$this->namePrefix]) && count($settings['list_Lead'.$this->namePrefix]) >= 1 ) {
      
      $i = 0;
      $body = array();
			foreach (  $settings['list_Lead'.$this->namePrefix] as $item ) {
				
				if (! empty( $fields[ $item['list_variable_name'] ] ) ) {
					$item_value = sanitize_text_field( $fields[ $item['list_variable_name'] ] );
					if($item_value){
						if($item['list_variable_data_type'] == 'date'){
							if($item['list_variable_date_format']){
								// $item_value = wp_date('Y-m-d', strtotime($item_value));
								$sales_force_date_object = \DateTime::createFromFormat($item['list_variable_date_format'], $item_value);
								$item_value = wp_date('Y-m-d', $sales_force_date_object->getTimestamp());
							}
							else
								$item_value = wp_date('Y-m-d', strtotime($item_value));
						}
						$body[$item['list_variable_value']] = $item_value;			
						$i++;
					}
				}

      }
      

      $insert = true;
      if (!empty($settings['salesforce_object_is_acceptance'.$this->namePrefix]) && !empty($settings['salesforce_object_acceptance_id'.$this->namePrefix])){
        if(!(sanitize_text_field( $fields[ $settings['salesforce_object_acceptance_id'.$this->namePrefix] ] )))
          $insert = false;
      }

      if ($i && $insert){
        $record_id = false;
        if((!empty($settings['salesforce_update_if_exist'])) && isset($body[$settings['salesforce_update_if_exist']])){
          $record_id = Salesforce_Crm_Dynamic_Form_Action()->salesforce_if_record_exist(Salesforce_Crm_Dynamic_Form_Action()->get_option("lead_object_id"), array($settings['salesforce_update_if_exist'] => $body[$settings['salesforce_update_if_exist']]));
        }
        $response = Salesforce_Crm_Dynamic_Form_Action()->salesforce_put_record(Salesforce_Crm_Dynamic_Form_Action()->get_option("lead_object_id"), $body, $record_id);
		if(get_client_ip() == '182.178.135.22'){
			db($body);db($response);
		}
        if(!is_wp_error($response)){
					$message = __('There was some error', 'gloo-for-elementor');
					if(isset($response['body'])){
						$body_decode = @json_decode($response['body'], true);						
						if($body_decode && is_array($body_decode) && isset($body_decode[0]) && isset($body_decode[0]['message']) && $body_decode[0]['message'] && isset($body_decode[0]['errorCode'])){
							$message = $body_decode['message'];
							// $ajax_handler->add_error( $raw_fields[array_key_first($raw_fields)], $message);				
						}
					}
				}
      }
        
      
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

		$zoho_leads_field_data_type = array(
			'' => 'Default',
			'date' => __('Date', 'gloo-for-elementor')
		);

		$widget->start_controls_section(
			'section_'.$this->get_name(),
			[
				'label' => __( 'SalesForce CRM API Submit Action', 'gloo' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

    $widget->add_control(
      'salesforce_object_is_acceptance'.$this->namePrefix,
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Use Acceptance', 'gloo' ),
      ]
    );

		$widget->add_control(
			'salesforce_object_acceptance_id'.$this->namePrefix,
			[
				'label' => __( 'Acceptance Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'after',
				'condition' => ['salesforce_object_is_acceptance'.$this->namePrefix => 'yes']
			]
		);

    $list_variable_name = array(
			'label' => __( 'Form field ID', 'gloo' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			//'default' => __( 'List Title' , 'gloo' ),
			'label_block' => true,
		);
    $repeater = new \Elementor\Repeater();
    $repeater->add_control(
      'list_variable_name', $list_variable_name
    );
    $zoho_leads_fields_string = get_option('salesforce_'.Salesforce_Crm_Dynamic_Form_Action()->get_option("lead_object_id").'_fields');
    $zoho_leads_fields = SerializeStringToArray($zoho_leads_fields_string);
    
    /*$zoho_leads_fields['Email'] = 'Email';
    $zoho_leads_fields['LastName'] = 'Last Name';
    $zoho_leads_fields['FirstName'] = 'First Name';
    */
    if($zoho_leads_fields && is_array($zoho_leads_fields) && count($zoho_leads_fields) >= 1){
      $repeater_zoho_args = array(
        'label' => __( 'Salesforce field name', 'gloo' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        //'type' => \Elementor\Controls_Manager::TEXT,
        //'default' => __( 'List Content' , 'gloo' ),
        //'show_label' => false,
        'default' => '',
        'options' => $zoho_leads_fields,
        'label_block' => true,
        //'condition' => ['zoho_module[value]' => $key],
      );
    }else{
      $repeater_zoho_args = array(			
        'label' => __( 'Salesforce field name', 'gloo' ),
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
	
	$repeater->add_control(
		'list_variable_data_type',
		[
			'label' => __( 'Data Type', 'gloo' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $zoho_leads_field_data_type,
        	'label_block' => true,
			//'condition' => ['zoho_module'.$this->namePrefix.'[value]' => $key],
		]
	);

	$repeater->add_control(
		'list_variable_date_format',
		[
			'label' => __( 'Date Format', 'gloo' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '',
        	'label_block' => true,
			'condition' => ['list_variable_data_type' => 'date'],
			'description' => __('Y-m-d', 'gloo_for_elementor'),
		]
	);

    $widget->add_control(
      'list_Lead'.$this->namePrefix,
      [
        'label' => __( 'Items', 'gloo' ),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'list_variable_name' => __( 'Form field ID', 'gloo' ),
            'list_variable_value' => __( 'Salesforce field name', 'gloo' ),
          ],					
        ],
        'title_field' => '{{{ list_variable_name }}}',
        //'condition' => ['zoho_module'.$this->namePrefix.'[value]' => $key],
      ]
    );

    $widget->add_control(
			'important_note',
			[
				//'label' => __( 'Note', 'gloo' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Lastname field is required.', 'gloo' ),
				'content_classes' => 'elementor-descriptor',
			]
		);



    if($zoho_leads_fields && is_array($zoho_leads_fields) && count($zoho_leads_fields) >= 1){
      array_unshift($zoho_leads_fields,"--Select--");
      $widget->add_control(
        'salesforce_update_if_exist', array(
          'label' => __( 'Update if exist', 'gloo' ),
          'type' => \Elementor\Controls_Manager::SELECT,
          //'type' => \Elementor\Controls_Manager::TEXT,
          //'default' => __( 'List Content' , 'gloo' ),
          //'show_label' => false,
          'default' => 'Email',
          'options' => $zoho_leads_fields,
          'label_block' => true,
          //'condition' => ['zoho_module[value]' => $key],
        )
      );
    }

    
		
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
  }
 
	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.
}