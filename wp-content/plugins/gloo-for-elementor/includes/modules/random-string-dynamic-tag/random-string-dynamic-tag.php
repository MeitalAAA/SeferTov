<?php
/**
 * Random String Dynamic Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Random_String_Dynamic_Tag' ) ) {

	/**
	 * Define Gloo_Random_String_Dynamic_Tag class
	 */
	class Gloo_Random_String_Dynamic_Tag extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'random_string_dynamic_tag';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Random String Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'random-string-dynamic-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Random_String_Dynamic_Tag\Module::instance();
		}

	}

}
