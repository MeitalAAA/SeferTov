<?php 
namespace Gloo\Modules\CookiesFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CookieFormAction extends \ElementorPro\Modules\Forms\Classes\Action_Base{

  public $name;
	public $namePrefix;
	// public $prefix = 'gloo_salesforce_crm_form_submit_action';

  public function __construct($name = 'gloo_cookie_form_action', $namePrefix = ''){
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
		return __( 'Save Cookies Form Action', 'gloo_for_elementor' );
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
			$this->get_name().'_section',
			[
				'label' => __( 'Save Cookies Form Action', 'gloo_for_elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
    $output_option = [
      'local_storage'      => 'Local Storage',
			'cookie'      => 'Cookies',
 			'session' => 'Session',
		];
    $widget->add_control(
			$this->get_name().'_cookie_type',
			[
				'label' => __( 'Cookie Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'cookie',
				'options' => $output_option,
				// 'label_block' => true,
				// 'separator' => 'before',
			]
		);
		$widget->add_control(
			$this->get_name().'_individual_fields',
			[
				'label' => __( 'Individual Form Fields?', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'form_field_id', [
				'label' => __( 'Form field ID', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'classes' => "gloo_activetrail_var_name",
			]
		);

		$widget->add_control(
			$this->get_name().'_form_field_list',
			[
				'label' => __( 'Items', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ form_field_id }}}',
				'prevent_empty' => false,
				'condition' => [
          $this->get_name().'_individual_fields' => 'yes',
				],
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