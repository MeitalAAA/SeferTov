<?php
namespace Gloo\Modules\FormOTP;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	
	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_prefix($glue_string = '_'){
		return 'gloo_otp_submit_action'.$glue_string;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    
		if(!is_admin()){
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			// add javascript and css to wp-admin dashboard.
			// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}
		
		

		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

		add_action( 'elementor_pro/init', [ $this, 'elementor_pro_init' ] );
		add_action('elementor-pro/forms/pre_render', [$this, 'elementor_pro_form_pre_render'], 10, 2);


		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2 );

  }// construct function end here


	public function elementor_pro_init() {

		add_action('elementor_pro/forms/validation', [$this, 'elementor_pro_forms_validation'], (PHP_INT_MAX)-100, 2);
		// if(isset($_GET['test'])){
		// 	db($_SESSION);exit();
		// }

		$form_action = new \Gloo\Modules\FormOTP\OTPSubmitAction();
		\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );

		// $quantity = get_option('gloo_otp_submit_action_quantity', 1);
		// if(!(!empty($quantity) && $quantity && is_numeric($quantity) && $quantity >= 1))
		// 	$quantity = 1;
		
		// for($i = 1; $i <= $quantity; $i++){
			
		// 	$form_action = new \Gloo\Modules\Form_Post_Submission\Frontend_Post_Submission('gloo_frontend_post_creation', $i);
		// 	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );
		// }

	}

	public function elementor_pro_forms_validation($record, $ajax_handler){

		$submit_actions = $record->get_form_settings( 'submit_actions' );
		$prefix = 'gloo_otp_submit_action_';
		
		if ($submit_actions && in_array( $prefix, $submit_actions ) ) {

			if(empty($ajax_handler->errors)){

				if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE)
						session_start();

				$settings = $record->get( 'form_settings' );
				$otp_to   = $settings[ $prefix . 'otp_to' ];
				$message   = $settings[ $prefix . 'message' ];
				$otp_frontend_wrong_label   = $settings[ $prefix . 'otp_frontend_wrong_label' ];
				$otp_frontend_form_submission_label = $settings[ $prefix . 'otp_frontend_form_submission_label' ];
				$otp_frontend_error_label = $settings[ $prefix . 'otp_frontend_error_label' ];
				// Get sumitetd Form data
				$raw_fields = $record->get( 'fields' );

				// Normalize the Form Data
				$fields = [];
				foreach ( $raw_fields as $id => $field ) {
					$fields[ $id ] = $field['value'];
				}
				
				if($otp_to && isset($fields[$otp_to]) && $fields[$otp_to] && $message){

					$otp_length = absint( $settings[ $prefix . 'otp_length' ] )?:4;
					
					if(isset($_POST['form_fields']) && isset($_POST['form_fields']['gloo_otp']) && is_array($_POST['form_fields']['gloo_otp'])){
						
						$otp_error = true;
						if(count($_POST['form_fields']['gloo_otp']) >= 1 && isset($_SESSION['gloo_otp'])){
							$otp_value = '';
							foreach($_POST['form_fields']['gloo_otp'] as $single_otp){
								$otp_value .= absint($single_otp);
							}
							if($otp_value == $_SESSION['gloo_otp']){
								if($_SESSION['gloo_otp_to'] != $fields[$otp_to]){
									$this->send_new_otp($record, $ajax_handler, $fields, true);
								}else{
									$otp_error = false;
									unset($_SESSION['gloo_otp_data']);
									unset($_SESSION['gloo_otp_failed']);
								}
								
							}
// 							db($otp_value);db( $_SESSION['gloo_otp']);exit();
						}
						if($otp_error){
							
							if(isset($_SESSION['gloo_otp_failed']))
								$_SESSION['gloo_otp_failed'] += 1;
							else
								$_SESSION['gloo_otp_failed'] = 1;

								
							$failed_otp_attemps = (int)$settings[ $prefix . 'otp_send_new_token' ];
							// db($failed_otp_attemps);db($_SESSION['gloo_otp_failed']);
							if(($failed_otp_attemps && $_SESSION['gloo_otp_failed'] >= $failed_otp_attemps)){
								$this->send_new_otp($record, $ajax_handler, $fields, true);
							}else{
								wp_send_json_error( [
									// 'message' => __('Please provide correct OTP.', 'gloo_for_elementor'),
								  //   'message' => $otp_frontend_wrong_label.' otp tries = '.$_SESSION['gloo_otp_failed'],	
									'message' => $otp_frontend_wrong_label,
									'data' => $ajax_handler->data,
								] );die();
							}
							
						}
							
					}else{

						$this->send_new_otp($record, $ajax_handler, $fields);
						
						
					}
				}
				
			}

		}
	}

  /******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    
      wp_register_style( 'gloo_otp_action_css', gloo()->plugin_url().'includes/modules/form-otp/assets/frontend/css/style.css', array(), '1.0.0' );
      wp_enqueue_style('gloo_otp_action_css');
			
      wp_register_script( 'gloo_otp_action', gloo()->plugin_url().'includes/modules/form-otp/assets/frontend/js/script.js', array('jquery'), rand(0, 99));
      wp_enqueue_script( 'gloo_otp_action' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );

		
  }// wp_admin_style_scripts


	public function send_otp_email($record, $ajax_handler, $prefix, $to){
		$settings = $record->get( 'form_settings' );
		// $otp_to   = $settings[ $prefix . 'otp_to' ];
		$email_from_name   = $settings[ $prefix . 'email_from_name' ]?: get_bloginfo( 'name' );
		$email_from   = $settings[ $prefix . 'email_from' ]?: get_bloginfo('admin_email');
		$email_subject   = $settings[ $prefix . 'email_subject' ]?: 'Your One Time Passcode';
		$email_content_type   = $settings[ $prefix . 'email_content_type' ]?: 'html';
		$message   = $settings[ $prefix . 'message' ];
		if($message){
			// return $message;	
			// $message = '<html><head><title></title></head><body>';
			$message = str_replace(array('[gloo-form-otp]'), array($_SESSION['gloo_otp']), $message);
			// $message .= '</body></html>';
			
			$headers = array(
				"MIME-Version: 1.0",
				// "Content-Type: text/html; charset=UTF-8",
				"From: ".$email_from_name." <".$email_from.">",
				//"Cc: John Q Codex <jqc@wordpress.org>",
				//"Reply-To: Person Name <person.name@example.com>",
			);
			if($email_content_type == 'html')
				$headers[] = "Content-Type: text/html; charset=UTF-8";

			if(!wp_mail($to, $email_subject, $message, $headers))
				return false;
			else
				return $message;
			// return $message;
		}
		return false;
	}

	public function send_otp_sms($record, $ajax_handler, $prefix, $to){
		$to = apply_filters('gloo_form_otp_phone_to_validation', $to);
		
		$settings = $record->get( 'form_settings' );
		// $otp_to   = $settings[ $prefix . 'otp_to' ];
		$phone_credentials_source   = $settings[ $prefix . 'phone_credentials_source' ];
		
		$from = $settings[ $prefix . 'sms_from_number' ];
		$message   = $settings[ $prefix . 'message' ];
		if($message && !empty($phone_credentials_source) && !empty($from)){
			$message = str_replace(array('[gloo-form-otp]'), array($_SESSION['gloo_otp']), $message);
			if($phone_credentials_source == 'twilio'){
				return $this->send_twilio_sms($record, $ajax_handler, $prefix, $to, $from, $message);
			}
		}

		return false;
	}

	public function send_twilio_sms($record, $ajax_handler, $prefix, $to, $from, $message){
		$settings = $record->get( 'form_settings' );
		$sid   = $settings[ $prefix . 'sms_account_sid' ];
		$token = $settings[ $prefix . 'sms_auth_token' ];
		if($sid && $token){

			$request = [
					'body' => [
							'From' => $from,
							'To' => $to,
							'Body' => $message
					],
					'headers' => array(
							'Content-type' => 'application/x-www-form-urlencoded',
							'Authorization' => 'Basic ' . base64_encode($sid . ':' . $token)
					),
			];

			$url = "https://api.twilio.com/2010-04-01/Accounts/".$sid."/Messages.json";
			// $url = "https://api.twilio.com/2010-04-01/Accounts/".$sid."/SMS/Messages.json";
			$response = wp_remote_post($url, $request);			
			// return wp_remote_retrieve_body($response);
			if(!is_wp_error($response)){
				$body = wp_remote_retrieve_body($response);
				$body = @json_decode($body, true);
				if($body && is_array($body) && isset($body['status'])){
					if(($body['status'] == 400 || $body['status'] == '20410') && isset($body['message'])){
						return array('error' => true, 'message' => $body['message']);
					}else
						return $message;
				}
			}
		}
		return false;
	}



	public function send_new_otp($record, $ajax_handler, $fields, $renew_otp = false){
		// unset($_SESSION['gloo_otp_data']);
		$settings = $record->get( 'form_settings' );
		$prefix = 'gloo_otp_submit_action_';
		$otp_length = absint( $settings[ $prefix . 'otp_length' ] )?:4;
		$otp_frontend_form_submission_label = $settings[ $prefix . 'otp_frontend_form_submission_label' ];
		$otp_frontend_error_label = $settings[ $prefix . 'otp_frontend_error_label' ];
		$otp_to   = $settings[ $prefix . 'otp_to' ];
		$otp_type   = $settings[ $prefix . 'otp_type' ];

		$otp_total_attemps_allowed =  (int)$settings[ $prefix . 'otp_total_attemps_allowed' ];
		if(!empty($otp_total_attemps_allowed) && isset($_SESSION['gloo_otp_data']) && isset($_SESSION['gloo_otp_data']['start_time'])){
			$start_time = ((int)$_SESSION['gloo_otp_data']['start_time']) + 3600;
			$current_time = wp_date('U');
			// db(wp_date('Y-m-d H:i:s', $start_time));
			// db(wp_date('Y-m-d H:i:s', $current_time));
			// db($_SESSION['gloo_otp_data']['total_attempts']);
			// db($otp_total_attemps_allowed);
			if($current_time >= $start_time){
				unset($_SESSION['gloo_otp_data']);
			}elseif(isset($_SESSION['gloo_otp_data']['total_attempts']) && $_SESSION['gloo_otp_data']['total_attempts'] >= $otp_total_attemps_allowed){
				unset($_SESSION['gloo_otp']);
				wp_send_json_error( [
					'message' => __('Your account has been blocked, you can try again after 1 hour.'),
					'data' => $ajax_handler->data,
				] );die();
			}

		}

		
		$_SESSION['gloo_otp'] = otw_generate_int($otp_length);
		$_SESSION['gloo_otp_to'] = $fields[$otp_to];

		if($otp_type == 'sms'){
			// $frontend_heading = $settings[ $prefix . 'otp_frontend_heading_sms' ];
			$message_sent = $this->send_otp_sms($record, $ajax_handler, $prefix, $fields[$otp_to]);
		}
		else{
			// $frontend_heading = $settings[ $prefix . 'otp_frontend_heading' ];
			$message_sent = $this->send_otp_email($record, $ajax_handler, $prefix, $fields[$otp_to]);
		}

		$send_output = [
			// 'message' => __('Your form has been submitted.', 'gloo_for_elementor'),
			'message' => $otp_frontend_form_submission_label,
			'data' => $ajax_handler->data,
			'form_id' => $record->get_form_settings( 'id' ),
			// 'message_sent' => $message_sent,
			'otp_length' => $otp_length,
			// 'frontend_heading' => $frontend_heading,
		];
		// if($renew_otp && isset($settings[ $prefix . 'otp_frontend_new_otp_label' ]) && $settings[ $prefix . 'otp_frontend_new_otp_label' ])
		// 	$send_output['renew_message'] = '<p>'.$settings[ $prefix . 'otp_frontend_new_otp_label' ].'</p>';


		if($message_sent){
							
			if(is_array($message_sent) && isset($message_sent['error']) && isset($message_sent['message'])){
				wp_send_json_error( [
					'message' => $message_sent['message'],
					'data' => $ajax_handler->data,
				] );die();
			}else{

				$this->update_gloo_otp_session();

				unset($_SESSION['gloo_otp_failed']);
				if($renew_otp && isset($settings[ $prefix . 'otp_frontend_new_otp_label' ]) && $settings[ $prefix . 'otp_frontend_new_otp_label' ]){
					$send_output['message'] = '<p>'.$settings[ $prefix . 'otp_frontend_new_otp_label' ].'</p>';
					unset($send_output['otp_length']);
					unset($send_output['form_id']);
				}
				wp_send_json_error( $send_output );die();
			}
			

		}else{

			wp_send_json_error( [
				// 'message' => __('We couldn\'t send OTP notification.', 'gloo_for_elementor'),
				'message' => $otp_frontend_error_label,
				'data' => $ajax_handler->data,
			] );die();
		
		}


	}

	public function update_gloo_otp_session(){
		$otp_data = array();
		if(isset($_SESSION['gloo_otp_data']) && is_array($_SESSION['gloo_otp_data']) && count($_SESSION['gloo_otp_data']) >= 1)
			$otp_data = $_SESSION['gloo_otp_data'];

		if(!isset($otp_data['start_time']))
			$otp_data['start_time'] = wp_date('U');

		if(isset($otp_data['total_attempts']) && $otp_data['total_attempts'])
			$otp_data['total_attempts'] += $otp_data['total_attempts'];
		else
			$otp_data['total_attempts'] = 1;

		$_SESSION['gloo_otp_data'] = $otp_data;
		
	}


	public function elementor_pro_form_pre_render($settings, $widget){

		$settings = $widget->get_settings_for_display();
		if(isset($settings['submit_actions'])){
			
			$submit_actions = $settings['submit_actions'];
			$prefix = 'gloo_otp_submit_action_';
		
			if ($submit_actions && in_array( $prefix, $submit_actions ) ) {

				$otp_frontend_heading_sms   = $settings[ $prefix . 'otp_frontend_heading_sms' ];
				$otp_frontend_heading   = $settings[ $prefix . 'otp_frontend_heading' ];
				$otp_button_label   = $settings[ $prefix . 'otp_button_label' ];
				$otp_type   = $settings[ $prefix . 'otp_type' ];
				$otp_length   = $settings[ $prefix . 'otp_length' ];
				$form_id = $widget->get_id();
				
				// wp_enqueue_script( 'gloo_otp_action' );

				$js_variables = array(
					// 'otp_frontend_heading_sms' => $otp_frontend_heading_sms,
					// 'otp_frontend_heading' => $otp_frontend_heading,
					'otp_button_label' => $otp_button_label,
					'otp_type' => $otp_type,
					'otp_length' => $otp_length,
					'form_id' => $form_id
				);

				$widget->add_render_attribute( 'form', 'data-gloo-otp', json_encode($js_variables));

      			// wp_localize_script( 'gloo_otp_action', 'gloo_otp_action', $js_variables );
				echo '<div class="gloo_otp_heading_container" style="display:none;">';
				if($otp_type == 'sms')
					echo $otp_frontend_heading_sms;
				else
					echo $otp_frontend_heading;				
				echo '</div>';
				?>
				<style type="text/css">
					<?php 
					// db($settings[$this->get_prefix() .'alignment']);exit();
					if(isset($settings[$this->get_prefix().'direction']) && $settings[$this->get_prefix().'direction'] == 'yes'){
						?>
						.gloo-otp-inputs{
							flex-direction: row-reverse!important;
						}
						<?php
					} ?>
				</style>
				<?php
			}
		}
	}

	public function add_control_section_to_form( $element, $args ) {

		// $elementor = \Elementor\Plugin::instance();
		// $control_data = $elementor->controls_manager->get_control_from_stack( $element->get_unique_name(), 'submit_actions' );
		// db($control_data);
		// db($element);exit();
		$element->start_controls_section(
			$this->get_prefix().'style',
			[
				'label' => __( 'OTP Form Action', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->get_prefix() . 'container_width',
			[
				'label' 		=> __( 'Container Width', 'gloo_for_elementor' ),
				'type' 			=> \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'.gloo-otp-container' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$element->add_control(
			$this->get_prefix() .'alignment',
			[
				'label' => esc_html__( 'Alignment', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'gloo_for_elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				// 'default' => 'end',
				'toggle' => true,
				'selectors' 	=> [
					'.gloo-otp-channel' => 'align-items: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			$this->get_prefix() . 'input_width',
			[
				'label' 		=> __( 'Input Width', 'gloo_for_elementor' ),
				'type' 			=> \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'.gloo-otp-container .gloo-otp-input' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$element->add_control(
			$this->get_prefix() . 'input_height',
			[
				'label' 		=> __( 'Input Height', 'gloo_for_elementor' ),
				'type' 			=> \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'.gloo-otp-container .gloo-otp-input' => 'height: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$element->add_control(
			$this->get_prefix().'direction',
			[
				'label' => __( 'Right to Left', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'         => $this->get_prefix().'input_border',
				'label' => esc_html__( 'Border', 'plugin-name' ),
				'selector' => '.gloo-otp-container .gloo-otp-input',
			]
		);

		
		/*$element->add_control(
      $this->prefix.'_list_style_type_test', 
      [
        'name'         => $this->prefix.'_list_style_type_test',
        'label'        => __('List Style Type', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
					'disc' => 'Disc',
					'decimal' => 'Decimal',
					// 'georgian' => 'georgian',
					// 'space-counter' => 'space-counter',
					'circle' => 'Circle',
					'square' => 'Square',
					'upper-roman' => 'Upper Roman',
					'lower-alpha' => 'Lower Alpha',
				],
				// 'default' => 'disc',
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item' => 'list-style-type: {{VALUE}};',
        ],
      ]
    );


		$element->add_control(
      $this->prefix.'_close_button_color', 
      [
        'name'         => $this->prefix.'_close_button_color',
        'label'        => __('Close Button Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove' => 'color: {{VALUE}};',
        ],
      ]
    );

    $element->add_control(
      $this->prefix.'_close_button_hover_color', 
      [
        'name'         => $this->prefix.'_close_button_hover_color',
        'label'        => __('Close Button Hover Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

		$element->add_control(
      $this->prefix.'_close_button_hover_bg_color', 
      [
        'name'         => $this->prefix.'_close_button_hover_bg_color',
        'label'        => __('Close Button Hover Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item a.remove:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

		$element->add_control(
      $this->prefix.'_repeater_wrapper_bg_color', 
      [
        'name'         => $this->prefix.'_repeater_wrapper_bg_color',
        'label'        => __('Wrapper Background Color', 'gloo_for_elementor'),
        'type'         => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '.gloo_repeater_field_wrapper li.gloo_repeater_li_item' => 'background-color: {{VALUE}};',
        ],
      ]
    );
		
		
		


		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'         => $this->prefix.'_repeater_wrapper_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'plugin-name' ),
				'selector' => '.gloo_repeater_field_wrapper li.gloo_repeater_li_item',
			]
		);*/

    $element->end_controls_section();
  }

	public function add_form_field_switcher($name, $label, $context = 'content'){
		$output = [
				'name'         => $name,
				'label'        => $label,
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'    => [
						'field_type' => $this->get_type(),
				],
				'tab'          => $context,
				'inner_tab'    => 'form_fields_'.$context.'_tab',
				'tabs_wrapper' => 'form_fields_tabs',
		];
		
		return $output;
	}
	
} // BBWP_CustomFields class

