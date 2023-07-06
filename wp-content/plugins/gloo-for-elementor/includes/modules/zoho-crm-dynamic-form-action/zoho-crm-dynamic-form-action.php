<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Zoho_Crm_Dynamic_Form_Action' ) ) {

	/**
	 * Define Gloo_Module_Zoho_Crm_Dynamic_Form_Action class
	 */
	class Gloo_Module_Zoho_Crm_Dynamic_Form_Action extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'zoho_crm_dynamic_form_action';
		}

		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
				'ElementorPro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];

		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Zoho Crm Dynamic Form Action', 'gloo' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}

		/**
		 * is_parent_module_active
		 *
		 * @return true/false
		 */
		public function is_parent_module_active() {
			$output = false;
			$active_modules = $this->get_active_modules_from_db();
			if (in_array('gloo_zoho', $active_modules) || in_array('little_engine', $active_modules))
				$output = true;
			return $output;
		}

		
		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'zoho-crm-dynamic-form-action/inc/module.php' );
			$this->instance = \Gloo\Modules\ZohoCrmDynamicFormAction\Module::instance();
		}

	}

}