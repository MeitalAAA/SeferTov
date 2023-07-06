<?php
/**
 * Repeater Dynamic Tag module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Repeater_Dynamic_Tag' ) ) {

	/**
	 * Define Gloo_Module_Repeater_Dynamic_Tag class
	 */
	class Gloo_Module_Repeater_Dynamic_Tag extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'repeater_dynamic_tag';
		}

		public function module_dependencies() {
			return [
        		'Elementor Pro'      =>  defined( 'ELEMENTOR_PRO_VERSION' ),
			];
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Repeater Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'repeater-dynamic-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Repeater_Dynamic_Tag\Module::instance();
		}

	}

}
