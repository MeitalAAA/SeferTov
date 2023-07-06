<?php 
namespace Gloo\Modules\ZohoCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoCampaignsAfterSubmitOld extends \ElementorPro\Modules\Forms\Classes\Action_Base{
  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'zohocampaign';
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
		return __( 'Zoho Leads Form Action', 'gloo' );
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

		$widget->start_controls_section(
			'section_zohocampaign',
			[
				'label' => __( 'Zoho CRM Form Submit Action', 'gloo' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);



		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_variable_name', [
				'label' => __( 'Form field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Title' , 'gloo' ),
				'label_block' => true,
				'classes' => "gloo_variable_name",
			]
		);

		$zoho_leads_fields_string = get_option('zoho_Leads_fields');
		//$zoho_leads_fields_string = zoho_crm_dynamic_form_action()->get_option('zoho_leads_fields');
		$zoho_leads_fields = SerializeStringToArray($zoho_leads_fields_string);
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
			);
		}else{
			$repeater_zoho_args = array(			
				'label' => __( 'Zoho field name', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
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
	 *update_zoho_lead_record
	 */
	public function insert_zoho_lead_record($settings, $fields){
		
		$endpoint = 'https://www.zohoapis.'.zoho_crm_dynamic_form_action()->get_option('zoho_data_center').'/crm/v2/Leads';
		
		if ( isset($settings['list']) && is_array($settings['list']) && count($settings['list']) >= 1 ) {
			$body = array();
			$body['data'][0] = array();
			$i = 0;
			foreach (  $settings['list'] as $item ) {
				
				if (! empty( $fields[ $item['list_variable_name'] ] ) ) {
					$item_value = sanitize_text_field( $fields[ $item['list_variable_name'] ] );
					if($item_value){
						$body['data'][0][$item['list_variable_value']] = $fields[ $item['list_variable_name'] ];				
						$i++;
					}
					
				}

			}

			
			if($i){
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
			}//if $i

		}//if list settings.
		
	}//public function

}