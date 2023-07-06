<?php
/**
 * Acf Dynamic Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Acf_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Acf_Dynamic_Tags class
	 */
	class Gloo_Module_Acf_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'acf_dynamic_tag';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Acf Dynamic Tags', 'gloo_for_elementor' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}

		public function module_dependencies() {
			// or return boolean value
			return [
				'acf'      => function_exists( 'acf' ),
			];
		}

		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'acf-dynamic-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Acf_Dynamic_Tags\Module::instance();
		}

	}

}
