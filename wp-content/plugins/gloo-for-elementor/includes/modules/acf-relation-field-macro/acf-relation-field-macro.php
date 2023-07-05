<?php
/**
 * Acf Relation Field Macro
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Acf_Relation_Field_Macro' ) ) {

	/**
	 * Define Gloo_Module_Acf_Relation_Field_Macro class
	 */
	class Gloo_Module_Acf_Relation_Field_Macro extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_acf_relation_field_macro';
		}

		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
				'jet_engine'      => function_exists( 'jet_engine' ),
			];

		}
		
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Acf Relation Field Macro', 'gloo_for_elementor' );
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
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'acf-relation-field-macro/inc/module.php' );
			$this->instance = \Gloo\Modules\Acf_Relation_Field_Macro\Module::instance();
		}

	}

}
