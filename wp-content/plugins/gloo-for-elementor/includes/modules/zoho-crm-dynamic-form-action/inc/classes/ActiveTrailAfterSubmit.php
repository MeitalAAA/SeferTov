<?php 
namespace Gloo\Modules\ZohoCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActiveTrailAfterSubmit extends \ElementorPro\Modules\Forms\Classes\Action_Base{
  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'otwactivetrail';
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
		return __( 'OTW Active Trail', 'gloo' );
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

		//  Make sure that there is a Sendy installation url
		if ( empty( $settings['activetrail_accesstoken'] ) ) {
			return;
		}
		
		// Make sure that there is a Sendy Email field ID
		// which is required by Sendy's API to subsribe a user
		if ( empty( $settings['activetrail_email_field'] ) ) {
			return;
		}

		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}
		
		// Make sure that the user emtered an email
		// which is required by Sendy's API to subsribe a user
		if ( empty( $fields[ $settings['activetrail_email_field'] ] ) ) {
			return;
		}
		
		// If we got this far we can start building our request data
		// Based on the param list at https://sendy.co/api
		$body = [
			//"email" => "Test@gmail.com",
			'email' => sanitize_email($fields[ $settings['activetrail_email_field'] ]),			
			//'subscribe_ip' => \ElementorPro\Classes\Utils::get_client_ip(),
			//'referrer' => isset( $_POST['referrer'] ) ? $_POST['referrer'] : '',
		];
		
		// add name if field is mapped
		if ( !empty( $fields[ $settings['activetrail_first_name_field'] ] ) ) {
			$body['first_name'] = sanitize_text_field($fields[ $settings['activetrail_first_name_field'] ]);
		}

		if (! empty( $fields[ $settings['activetrail_last_name_field'] ] ) ) {
			$body['last_name'] = sanitize_text_field($fields[ $settings['activetrail_last_name_field'] ]);
		}

		


		if (! empty( $settings['activetrail_list'] ) ) {
			$endpoint = 'http://webapi.mymarketing.co.il/api/groups/'.$settings['activetrail_list'].'/members';
			//$endpoint = 'https://webapi.mymarketing.co.il/api/mailinglist/'.$settings['activetrail_list'].'/members';
		}else{
			$endpoint = 'https://webapi.mymarketing.co.il/api/contacts';
		}
		//db($endpoint);
		
		$body = wp_json_encode( $body );
		
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
		
		//if ( !empty( $fields[ $settings['activetrail_approval_field'] ] ) ) {
			$response = wp_remote_post( $endpoint, $options );
			update_option('activetrail_campaign_last_inserted', $response);
			//db($response);exit();
		//}
		

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
			'section_otwactivetrail',
			[
				'label' => __( 'OTW Active Trail', 'gloo' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'activetrail_accesstoken',
			[
				'label' => __( 'Access Token', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your access token.', 'gloo' ),
			]
		);

		$widget->add_control(
			'activetrail_list',
			[
				'label' => __( 'Active Trail List ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'before',
				'description' => __( 'the list id you want to subscribe a user to.', 'gloo' ),
			]
		);

		$widget->add_control(
			'activetrail_email_field',
			[
				'label' => __( 'Email Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'activetrail_first_name_field',
			[
				'label' => __( 'First Name Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'activetrail_last_name_field',
			[
				'label' => __( 'Last Name Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'activetrail_approval_field',
			[
				'label' => __( 'Approval Checkbox ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
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
		unset(
			$element['activetrail_accesstoken'],
			$element['activetrail_list'],
			$element['activetrail_email_field'],
			$element['activetrail_first_name_field'],
			$element['activetrail_last_name_field']
		);
  }
  

}