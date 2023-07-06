<?php 
namespace Gloo\Modules\ActiveTrailFormSubmitAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActiveTrailFormSubmitActionClass extends \ElementorPro\Modules\Forms\Classes\Action_Base{
  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'activetrail_form_submit_action';
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
		return __( 'ActiveTrail Form Submit Action', 'gloo_for_elementor' );
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

		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}
		
		if ( isset($settings['activetrail_list']) && is_array($settings['activetrail_list']) && count($settings['activetrail_list']) >= 1 ) {
      $i = 0;
      $body = array();
			foreach (  $settings['activetrail_list'] as $item ) {
				
				if (! empty( $fields[ $item['activetrail_var_name'] ] ) ) {
					$item_value = sanitize_text_field( $fields[ $item['activetrail_var_name'] ] );
					if(!empty($item_value)){
						$body[$item['activetrail_var_value']] = $fields[ $item['activetrail_var_name'] ];			
						$i++;
					}
				}elseif($item['activetrail_var_name'] && mb_substr($item['activetrail_var_name'], 0, 1) === '{' && mb_substr($item['activetrail_var_name'], -1, 1) === '}'){
					$body[$item['activetrail_var_value']] = str_replace(array("{","}"), array("",""), $item['activetrail_var_name']);	
				}

      }

			$insert = true;
			if (!empty($settings['activetrail_is_acceptance']) && !empty($settings['activetrail_acceptance_id'])){
				if(!(sanitize_text_field( $fields[ $settings['activetrail_acceptance_id'] ] )))
					$insert = false;
			}

      if ($insert && $i && isset($settings['activetrail_accesstoken']) && $settings['activetrail_accesstoken']){
					$update = $this->post_activetrail_lead($body, $settings);
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

		$widget->start_controls_section(
			'section_activetrailleads',
			[
				'label' => __( 'ActiveTrail Form Submit Action', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'activetrail_accesstoken',
			[
				'label' => __( 'Access Token', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '',
				'label_block' => true,				
				//'description' => __( 'Enter your access token.', 'gloo_for_elementor' ),
			]
		);		

		$widget->add_control(
			'activetrail_list_id',
			[
				'label' => __( 'Active Trail List ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'the list id you want to subscribe a user to.', 'gloo_for_elementor' ),
			]
		);

		$widget->add_control(
      'activetrail_is_acceptance',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Use Acceptance', 'gloo_for_elementor' ),
      ]
    );

		$widget->add_control(
			'activetrail_acceptance_id',
			[
				'label' => __( 'Acceptance Field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'after',
				'condition' => ['activetrail_is_acceptance' => 'yes']
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'activetrail_var_name', [
				'label' => __( 'Form field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Title' , 'gloo' ),
				'label_block' => true,
				'classes' => "gloo_activetrail_var_name",
			]
		);
		
		
		$repeater->add_control(
			'activetrail_var_value', [
        'label' => __( 'activetrail field name', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
      ]
		);

		$widget->add_control(
			'activetrail_list',
			[
				'label' => __( 'Items', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'activetrail_var_name' => __( 'Form field ID', 'gloo_for_elementor' ),
						'activetrail_var_value' => __( 'ActiveTrail name', 'gloo_for_elementor' ),
					],					
				],
				'title_field' => '{{{ activetrail_var_name }}}',
			]
		);

		$leads_fields = array('first_name' => 'first_name', 'last_name' => 'last_name', 'email' => 'email');
		$possible_values = 'Possible ActiveTrail Field names are ';
		$possible_values .= implode(', ', $leads_fields);
		/*foreach($leads_fields as $key=>$lead_field){
			$possible_values .= $key.', ';
		}*/
		$possible_values .= ' etc.';
		$widget->add_control(
			'activetrail_list_description',
			[
				'raw' => __( 'Last Name and Email are required. <br />You can also put static values in "Form Field ID" input for example {static name}<br />'.$possible_values, 'gloo' ),
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
  public function post_activetrail_lead($post = array(), $settings){
		
		if (! empty( $settings['activetrail_list_id'] ) ) {
			$endpoint = 'http://webapi.mymarketing.co.il/api/groups/'.$settings['activetrail_list_id'].'/members';
			//$endpoint = 'https://webapi.mymarketing.co.il/api/mailinglist/'.$settings['activetrail_list'].'/members';
		}else{
			$endpoint = 'https://webapi.mymarketing.co.il/api/contacts';
		}

		$body = wp_json_encode( $post );
		
		$options = [
				'body'        => $body,
				'headers'     => [
					'Authorization' => $settings['activetrail_accesstoken'],
					//'Authorization' => '0XDA01DAB0378A730CD8D8D427BA7BC14CEA50CA0E42D2249A85971463CCDF033C09B493000122713FCB3E3ED25C348D80',
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
  }
}