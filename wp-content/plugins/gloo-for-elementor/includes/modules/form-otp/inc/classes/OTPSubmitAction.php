<?php 
namespace Gloo\Modules\FormOTP;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OTPSubmitAction extends \ElementorPro\Modules\Forms\Classes\Action_Base{

	public $name;
	public $namePrefix;

	public function __construct($name = 'gloo_otp_submit_action', $namePrefix = ''){
		$this->name = $name;
		$this->namePrefix = $namePrefix;
	}

  public function get_sub_prefix($glue_string = '_'){
		$output = '';
		if(!empty($this->namePrefix) && $this->namePrefix && is_numeric($this->namePrefix) && $this->namePrefix >= 2)
			$output = $glue_string.$this->namePrefix;
		return $output;
	}

  public function get_prefix($glue_string = '_'){
		return $this->name.$this->get_sub_prefix('_parent_').$glue_string;
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
		return $this->get_prefix();
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
			return __( 'OTP form action  - '.$this->namePrefix, 'gloo_for_elementor' );
		else
			return __( 'OTP form action ', 'gloo_for_elementor' );
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

    // if(isset($_POST['form_fields']) && isset($_POST['form_fields']['gloo_otp']) && is_array($_POST['form_fields']['gloo_otp'])){
    //   wp_send_json_error( [
    //     'message' => _('Please provide correct OTP.'),
    //     'data' => $ajax_handler->data,
    //   ] );die();
    // }
    

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
			$this->get_prefix(),
			[
				'label' => __( $this->get_label(), 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
    $widget->add_control(
			$this->get_prefix().'otp_type',
			[
				'label'   => __( 'OTP method', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
          'email' => __('Email', 'gloo_for_elementor'),
          'sms' => __('SMS', 'gloo_for_elementor')
        ),
				'default' => 'email',
			]
		);

    $widget->add_control(
			$this->get_prefix().'otp_length',
			[
				'label' => __( 'Code length', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 2,
				'max' => 10,
				'step' => 1,
				'default' => 4,
			]
		);

    $widget->add_control(
			$this->get_prefix().'otp_to',
			[
				'label' => __( 'Phone/Email field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

    $widget->add_control(
			$this->get_prefix().'message',
			[
				'label' => __( 'Content', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __('Your one time password is [gloo-form-otp].', 'gloo_for_elementor'),
				'show_label' => true,
			]
		);

    $widget->add_control(
			$this->get_prefix().'otp_button_label',
			[
				'label' => __( 'Button Label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __('Send OTP', 'gloo_for_elementor'),
			]
		);

	// $widget->add_control(
	// 	$this->get_prefix().'otp_expire_time',
	// 	[
	// 		'label' => __( 'Expiry time (Seconds)', 'gloo_for_elementor' ),
	// 		'type' => \Elementor\Controls_Manager::TEXT,
	// 		'default' => '3600',
	// 	]
	// );

	$widget->add_control(
		$this->get_prefix().'otp_send_new_token',
		[
			'label' => __( 'Resend OTP attemps allowed', 'gloo_for_elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '5',
		]
	);

	$widget->add_control(
		$this->get_prefix().'otp_total_attemps_allowed',
		[
			'label' => __( 'Total Attemps allowed in 1 hour.', 'gloo_for_elementor' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '10',
		]
	);

    

    /******************************************************* Email ************************************************/
		$widget->add_control(
			$this->get_prefix().'email_settings',
			[
				'label' => __( 'Email Settings', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'email_from_name',
			[
				'label' => __( 'From Name', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'placeholder' => get_bloginfo( 'name' ),
				'show_label' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'email_from',
			[
				'label' => __( 'From Email', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo('admin_email'),
				'placeholder' => get_bloginfo('admin_email'),
				'show_label' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'email_subject',
			[
				'label' => __( 'Subject', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Your One Time Passcode', 'gloo_for_elementor'),
				'placeholder' => __('Thank you for registering', 'gloo_for_elementor'),
				'show_label' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'email_content_type',
			[
				'label' => __( 'Send As', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'html',
				'render_type' => 'none',
				'options' => [
					'html' => __( 'HTML', 'gloo_for_elementor' ),
					'plain' => __( 'Plain', 'gloo_for_elementor' )
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);

    $widget->add_control(
			$this->get_prefix().'otp_frontend_heading',
			[
				'label' => __( 'OTP field label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '<p>'.__('Enter the 4 digits code sent to your email.', 'gloo_for_elementor').'</p>',
        'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'email'
						]
					]
				]
			]
		);
		


    //******************************************************* SMS ************************************************
 
		$widget->add_control(
			$this->get_prefix().'sms_settings',
			[
				'label' => __( 'SMS Settings', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'phone_credentials_source',
			[
				'label' => __( 'API Source', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => false,
				'options' =>[
					'twilio' => 'Twilio',
				],
				'default' => 'twilio',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);
		
		$widget->add_control(
			$this->get_prefix().'sms_account_sid',
			[
				'label' => __( 'Account SID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => false,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'sms_auth_token',
			[
				'label' => __( 'Auth Token', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => false,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);
		$widget->add_control(
			$this->get_prefix().'sms_from_number',
			[
				'label' => __( 'From Number', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '+919876543210',
				'show_label' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);
    $widget->add_control(
			$this->get_prefix().'otp_frontend_heading_sms',
			[
				'label' => __( 'OTP field label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '<p>'.__('Enter the 4 digits code sent to your phone.', 'gloo_for_elementor').'</p>',
        'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_prefix().'otp_type',
							'operator' => '==',
							'value' => 'sms'
						]
					]
				]
			]
		);

		$widget->add_control(
			$this->get_prefix().'otp_frontend_wrong_label',
			[
				'label' => __( 'Wrong OTP label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __('Please provide correct OTP.', 'gloo_for_elementor'),        
			]
		);

		$widget->add_control(
			$this->get_prefix().'otp_frontend_form_submission_label',
			[
				'label' => __( 'Form Submission Label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __('Your form has been submitted.', 'gloo_for_elementor'),        
			]
		);

		$widget->add_control(
			$this->get_prefix().'otp_frontend_form_submission_label',
			[
				'label' => __( 'Form Submission Label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __('Your form has been submitted.', 'gloo_for_elementor'),        
			]
		);

		$widget->add_control(
			$this->get_prefix().'otp_frontend_error_label',
			[
				'label' => __( 'Error label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        		'default' => __('We couldn\'t send OTP notification.', 'gloo_for_elementor'),        
			]
		);

		$widget->add_control(
			$this->get_prefix().'otp_frontend_new_otp_label',
			[
				'label' => __( 'Resend OTP label', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
        		'default' => __('Your previous OTP code was wrong, we sent a new code.', 'gloo_for_elementor'),        
			]
		);
		
		// $widget->add_control(
    //   $this->get_prefix().'enable_otp',
    //   [
    //     'type' => \Elementor\Controls_Manager::SWITCHER,
    //     'label' => __( 'Enable OTP', 'gloo' ),
    //   ]
    // );

		

		
		
		
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
 

}