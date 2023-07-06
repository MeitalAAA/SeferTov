<?php 
namespace Gloo\Modules\Powerlink_Form_Action;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PowerlinkFormAfterSubmit extends \ElementorPro\Modules\Forms\Classes\Action_Base{
	
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

  /**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'powerlinkleads';
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
		return __( 'Powerlink Form Submit Action', 'gloo_for_elementor' );
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
	
		if ( isset($settings['powerlink_list']) && is_array($settings['powerlink_list']) && count($settings['powerlink_list']) >= 1 ) {
      		$i = 0;
      		$body = array();
			
			foreach (  $settings['powerlink_list'] as $item ) {
				
				if (! empty( $fields[ $item['powerlink_var_name'] ] ) ) {
					$item_value = sanitize_text_field( $fields[ $item['powerlink_var_name'] ] );
					
					if($item_value){
						$body[$item['powerlink_var_value']] = $fields[ $item['powerlink_var_name'] ];			
						$i++;
					}
				} elseif($item['powerlink_var_name'] && mb_substr($item['powerlink_var_name'], 0, 1) === '{' && mb_substr($item['powerlink_var_name'], -1, 1) === '}'){
					$body[$item['powerlink_var_value']] = str_replace(array("{","}"), array("",""), $item['powerlink_var_name']);	
				}
			  }	
			  
			 // $this->powerlink_remote_post( $body , 'POST');
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
			'section_powerlinkleads',
			[
				'label' => __( 'Powerlink Form Submit Action', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'powerlink_uid_description',
			[
				'label' => __( 'Powerlink UID', 'gloo_for_elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'raw' => __( 'Create a hidden field under form fields for Powerlink UID and fill the default value of the field with UID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
       			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'powerlink_var_name', [
				'label' => __( 'Form field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				//'default' => __( 'List Title' , 'gloo' ),
				'label_block' => true,
				'classes' => "gloo_powerlink_var_name",
			]
		);
		
		$repeater->add_control(
			'powerlink_var_value', [
				'label' => __( 'Powerlink field name', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'powerlink_list',
			[
				'label' => __( 'Items', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'powerlink_var_name' => __( 'Form field ID', 'gloo_for_elementor' ),
						'powerlink_var_value' => __( 'Powerlink name', 'gloo_for_elementor' ),
					],					
				],
				'title_field' => '{{{ powerlink_var_name }}}',
			]
		);

		$possible_values = 'Form should have a hidden fields UID and most possible fields are firstname, statuscode, telephone1, emailaddress1';
		
		$widget->add_control(
			'powerlink_list_description',
			[
				'raw' => __( 'First Name and UID are required. <br />You can also put static values in "Form Field ID" input for example {static name}<br />'.$possible_values, 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
       			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
 

  /******************************************/
  /***** powerlink_remote_post function start from here *********/
  /******************************************/
  	public function powerlink_remote_post($body = array(), $method = false){
		
		$url = 'https://api.powerlink.co.il/web/webtoaccount.aspx';
		
		$response = wp_remote_post( $url, array(
			'method'      => $method,
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $body,
			)
		);
		 
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			echo 'Response:<pre>';
			print_r( $response );
			echo '</pre>';
		}

		die();
	}
}