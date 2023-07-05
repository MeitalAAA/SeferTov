<?php 
namespace Gloo\Modules\LoginFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LoginFormAction extends \ElementorPro\Modules\Forms\Classes\Action_Base{

  public $name;
	public $namePrefix;
	// public $prefix = 'gloo_salesforce_crm_form_submit_action';

  public function __construct($name = 'gloo_login_form_action', $namePrefix = ''){
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

	public function prefix() {
		return $this->get_name().'_';
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
		return __( 'Gloo Login', 'gloo_for_elementor' );
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

		// Don't proceed if user already logged in
		if (is_user_logged_in()) {
			$ajax_handler->add_error_message(__('You are already logged-in', 'gloo_for_elementor'));
			$ajax_handler->is_success = false;
			return;
		}

		$settings = $record->get( 'form_settings' );
		//  Make sure that there is a username/email/username or email field
		if (empty($settings[$this->prefix().'email_or_username_field_id'])) {
			return;
		}

		if (empty($settings[$this->prefix().'login_type'])) {
			return;
		}

		$login_type = $settings[$this->prefix().'login_type'];
		$email_or_username_field_id = $settings[$this->prefix().'email_or_username_field_id'];
		$error_field_id = $email_or_username_field_id;

		$message = __('Username or password is incorrect.', 'gloo_for_elementor');
		$current_user_id = false;
		

		

		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$form_data = [];
		$raw_fields = $record->get('fields');
		foreach ($raw_fields as $id => $field) {
			$form_data[$id] = $field['value'];
		}

		// Remember
		$remember = false;
		if(!empty($form_data[$settings[$this->prefix().'remember_me_field_id']]) ){
			$remember = true;
		}

		// user_login
		if ( empty( $form_data[$email_or_username_field_id] ) ) {
			$ajax_handler->add_error($error_field_id, __('Enter your login id', 'gloo_for_elementor'));
			return;
		}else
			$user_login = sanitize_text_field($form_data[$email_or_username_field_id]);
		

		if($login_type == 'standard'){
			if ( empty( $form_data[$settings[$this->prefix().'password_field_id']] ) ) {
				$error_field_id = $settings[$this->prefix().'password_field_id'];
				$ajax_handler->add_error($error_field_id, __('Enter your password', 'gloo_for_elementor'));
				return;
			}
			$user = wp_signon([
				'user_login' => $user_login,
				'remember' => $remember,
				'user_password' => $form_data[$settings[$this->prefix().'password_field_id']]
			]);
			if (is_wp_error($user)){
				$ajax_handler->add_error_message($user->get_error_message());
				return;
			}
			$current_user_id = $user->ID;
			wp_set_current_user($user->ID);

			
		}else if($login_type == 'id_and_phone' || $login_type == 'id_and_phone_and_password'){

			if ( empty( $settings[$this->prefix().'phone_field_id'] ) || empty( $form_data[$settings[$this->prefix().'phone_field_id']] ) || empty( $settings[$this->prefix().'phone_meta_key'] )) {
				$error_field_id = $settings[$this->prefix().'phone_field_id'];
				$ajax_handler->add_error($error_field_id, __('Enter your phone number.', 'gloo_for_elementor'));
				return;
			}
			$phone_field_id = $settings[$this->prefix().'phone_field_id'];

			if( is_email( $user_login ) )
				$user = get_user_by('email', $user_login);
			else
				$user = get_user_by('login', $user_login );
			
			if(!$user){
				$error_field_id = $email_or_username_field_id;
				$ajax_handler->add_error($error_field_id, __('Enter your correct login id', 'gloo_for_elementor'));
				return;
			}

			$user_provided_phone = sanitize_text_field(trim($form_data[$phone_field_id]));
			$user_provided_phone = substr($user_provided_phone, -7);
			$db_phone = get_user_meta($user->ID,  $settings[$this->prefix().'phone_meta_key'], true);
			$db_phone = substr($db_phone, -7);
			if($user_provided_phone != $db_phone){
				$error_field_id = $phone_field_id;
				$ajax_handler->add_error($error_field_id, __('Enter your correct phone number.', 'gloo_for_elementor'));
				return;
			}

			if($login_type == 'id_and_phone_and_password'){
				if ( empty( $form_data[$settings[$this->prefix().'password_field_id']] ) || !wp_check_password( $form_data[$settings[$this->prefix().'password_field_id']], $user->data->user_pass, $user->ID )) {
					$error_field_id = $settings[$this->prefix().'password_field_id'];
					$ajax_handler->add_error($error_field_id, __('Enter your correct password', 'gloo_for_elementor'));
					return;
				}
			}

			$logged_in_user = wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID, true );
			do_action( 'wp_login', $user->user_login, $logged_in_user );
			$current_user_id = $user->ID;
			
		}
		
		if($current_user_id){
			// Redirect
			$redirect_to = $settings[$this->prefix().'redirect_to'];
			if(!empty($settings[$this->prefix().'redirect_to']) && filter_var( $redirect_to, FILTER_VALIDATE_URL )){
				$ajax_handler->add_response_data('redirect_url', $redirect_to );
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
			$this->prefix().'section',
			[
				'label' => __( 'Gloo Login', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
    	$output_option = [
      		'standard'      => 'WP Standard',
			'id_and_phone'      => 'ID and Phone',
			// 'email_and_phone'      => 'Email and Phone Number',
			'id_and_phone_and_password'      => 'ID, Password and Phone',
		];
    	$widget->add_control(
			$this->prefix().'login_type',
			[
				'label' => __( 'Login Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => $output_option,
				// 'label_block' => true,
				// 'separator' => 'before',
			]
		);
		$widget->add_control(
			$this->prefix().'email_or_username_field_id', [
				'label' => __( 'Email or Username Field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$widget->add_control(
			$this->prefix().'password_field_id', [
				'label' => __( 'Password Field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'standard'
						],
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'id_and_phone_and_password'
						]
					]
				]
			]
		);

		$widget->add_control(
			$this->prefix().'phone_field_id', [
				'label' => __( 'Phone Field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'id_and_phone'
						],
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'id_and_phone_and_password'
						]
					]
				]
			]
		);


		$widget->add_control(
			$this->prefix().'phone_meta_key', [
				'label' => __( 'Phone Meta Key', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'id_and_phone'
						],
						[
							'name' => $this->prefix().'login_type',
							'operator' => '==',
							'value' => 'id_and_phone_and_password'
						]
					]
				]
			]
		);


		
		
		$widget->add_control(
			$this->prefix().'remember_me_field_id', [
				'label' => __( 'Remember Field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$widget->add_control(
			$this->prefix().'redirect_to', [
				'label' => __( 'Redirect URL', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
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

}