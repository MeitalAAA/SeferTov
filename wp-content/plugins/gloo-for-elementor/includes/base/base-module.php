<?php
/**
 * Base class for module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Base' ) ) {

	/**
	 * Define Gloo_Module_Base class
	 */
	abstract class Gloo_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		abstract public function module_id();

		/**
		 * Module name
		 *
		 * @return string
		 */
		abstract public function module_name();

		/**
		 * Module dependencies
		 *
		 * @return mixed
		 */
		public function module_dependencies() {
			return true;
		}

		/**
		 * Module dependencies
		 *
		 * @return mixed
		 */
		public function get_active_modules_from_db() {
			return get_option( 'gloo_modules', array() );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		abstract public function module_init();

	}

}
