<?php
namespace Gloo\Modules\SalesForceCrmDynamicFormAction;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'salesforce-crm-dynamic-form-action';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}


	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		// include the generic functions file.
		//plugin_dir_path(OTW_ELEMENTOR_FORM_CRM_PLUGIN_FILE).'
		//gloo()->modules_path().'salesforce-crm-dynamic-form-action/

		//Gloo\Modules\SalesForceCrmDynamicFormAction
		//Gloo\Modules\SalesForceCrmDynamicFormAction
		include_once gloo()->modules_path().'salesforce-crm-dynamic-form-action/inc/autoload.php';

		Salesforce_Crm_Dynamic_Form_Action();

	}

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
