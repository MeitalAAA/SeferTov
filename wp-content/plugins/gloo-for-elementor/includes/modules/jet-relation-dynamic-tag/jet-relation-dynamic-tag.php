<?php
/**
 * Jet Relation Dynamic Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Jet_Relation_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Jet_Relation_Dynamic_Tags class
	 */
	class Gloo_Module_Jet_Relation_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'jet_relation_dynamic_tag';
		}

		
		public function module_dependencies() {
			return [
        		'jet_engine'   => function_exists( 'jet_engine' ),
			];
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Jet Relation Field Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'jet-relation-dynamic-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Jet_Relation_Dynamic_Tags\Module::instance();
		}

	}

}
