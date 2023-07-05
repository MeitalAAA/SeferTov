<?php
namespace Gloo\Modules\ActiveTrailFormSubmitAction;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'activetrail_form_submit_action';

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
		//gloo()->modules_path().'activetrail-form-submit-action/

		//Gloo\Modules\ActiveTrailFormSubmitAction
		//Gloo\Modules\ActiveTrailFormSubmitAction
		include_once gloo()->modules_path().'activetrail-form-submit-action/inc/autoload.php';

		activetrail_form_submit_action();

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
