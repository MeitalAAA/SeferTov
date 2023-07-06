<?php

namespace Gloo\Modules\Interactor;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'interactor';

	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

	/**
	 * @var Settings
	 */
	public $settings = null;

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

//		require gloo()->plugin_path( 'includes/modules/interactor/inc/conditions/settings.php' );
//		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/conditions/manager.php' );
		require gloo()->modules_path( 'interactor/inc/settings.php' );
		require gloo()->modules_path( 'interactor/inc/conditions/manager.php' );

		$this->conditions = new Conditions\Manager();
		$this->settings = new Settings();

//		$el_types = array(
//			'section',
//			'column',
//			'widget',
//		);
//
//		foreach ( $el_types as $el ) {
//			add_filter( 'elementor/frontend/' . $el . '/should_render', array( $this, 'check_cond' ), 10, 2 );
//		}

	}

	/**
	 * Check render conditions
	 *
	 * @param  [type] $result  [description]
	 * @param  [type] $element [description]
	 *
	 * @return [type]          [description]
	 */
	public function triggers_to_ignore( $element ) {

		$settings   = $element->get_settings();
		$is_enabled = ! empty( $settings['gloo_interactor_enable_conditions'] ) ? $settings['gloo_interactor_enable_conditions'] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );


		if ( ! $is_enabled ) {
			return false;
		}

		$dynamic_settings = $element->get_settings_for_display();
		$conditions       = $dynamic_settings['gloo_interactor_conditions'];
//		$relation         = ! empty( $settings['jedv_relation'] ) ? $settings['jedv_relation'] : 'AND';
		$is_or_relation = false;
//		$type             = ! empty( $settings['gloo_interactor_condition_type'] ) ? $settings['gloo_interactor_condition_type'] : 'show';
		$has_conditions = false;

		$args = array(
			'condition_type'      => null,
			'condition'           => null,
			'condition_user_role' => null,
			'condition_user_id'   => null,
			'condition_field'     => null,
			'condition_value'     => null,
			'condition_data_type' => null,
			'condition_triggers'  => null,
		);

		$triggers_to_ignore = [];
		foreach ( $conditions as $index => $condition ) {

			if ( ! isset( $condition['gloo_interactor_condition_triggers'] ) || ! $condition['gloo_interactor_condition_triggers'] ) {
				continue;
			}

			foreach ( $args as $arg => $default ) {
				$key          = 'gloo_interactor_' . $arg;
				$args[ $arg ] = ! empty( $condition[ $key ] ) ? $condition[ $key ] : $default;
			}

			$is_dynamic_field = isset( $settings['gloo_interactor_conditions'][ $index ]['__dynamic__']['gloo_interactor_condition_field'] );
			$is_empty_field   = empty( $settings['gloo_interactor_conditions'][ $index ]['gloo_interactor_condition_field'] );

			$args['field_raw'] = ( ! $is_dynamic_field && ! $is_empty_field ) ? $settings['gloo_interactor_conditions'][ $index ]['gloo_interactor_condition_field'] : null;

			if ( empty( $args['condition'] ) ) {
				continue;
			}

			$condition          = $args['condition'];
			$type               = $args['condition_type'];
			$triggers_linked    = $args['condition_triggers'];
			$condition_instance = $this->conditions->get_condition( $condition );

			if ( ! $condition_instance ) {
				continue;
			}

			if ( ! $has_conditions ) {
				$has_conditions = true;
			}

			$check = $condition_instance->check( $args );

			if ( ( 'show' === $type && ! $check ) || ( 'show' !== $type && $check ) ) {
				foreach ( $triggers_linked as $trigger ) {
					if ( ! in_array( $trigger, $triggers_to_ignore, true ) ) {
						array_push( $triggers_to_ignore, $trigger );
					}
				}
			}

//			if ( 'show' === $type ) {
//				if ( ! $check ) {
//					foreach ( $triggers_linked as $trigger ) {
//						if ( ! in_array( $trigger, $triggers_linked, true ) ) {
//							array_push( $triggers_linked, $trigger );
//						}
//					}
//				}
//			} else {
//				if ( $check ) {
//					foreach ( $triggers_linked as $trigger ) {
//						if ( ! in_array( $trigger, $triggers_linked, true ) ) {
//							array_push( $triggers_linked, $trigger );
//						}
//					}
//				}
////				if ( $is_or_relation ) {
////					if ( ! $check ) {
////						return false;
////					}
////				} elseif ( $check ) {
////					return true;
////				}
//			}
		}

		if ( empty( $triggers_to_ignore ) ) {
			return false;
		}

		return $triggers_to_ignore;
	}

	/**
	 * Check render conditions
	 *
	 * @param  [type] $result  [description]
	 * @param  [type] $element [description]
	 *
	 * @return [type]          [description]
	 */
	public function check_condition( $element ) {
		$result     = '';
		$settings   = $element->get_settings();
		$is_enabled = ! empty( $settings['gloo_interactor_enable_conditions'] ) ? $settings['gloo_interactor_enable_conditions'] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_enabled ) {
			return $result;
		}

		$dynamic_settings = $element->get_settings_for_display();
		$conditions       = $dynamic_settings['gloo_interactor_conditions'];
		$is_or_relation   = false;
		$type             = ! empty( $settings['gloo_interactor_condition_type'] ) ? $settings['gloo_interactor_condition_type'] : 'show';
		$has_conditions   = false;

		$args = array(
			'type'      => $type,
			'condition' => null,
			'user_role' => null,
			'user_id'   => null,
			'field'     => null,
			'value'     => null,
			'data_type' => null,
		);

		foreach ( $conditions as $index => $condition ) {
			foreach ( $args as $arg => $default ) {
				$key          = 'gloo_interactor_' . $arg;
				$args[ $arg ] = ! empty( $condition[ $key ] ) ? $condition[ $key ] : $default;
			}

			$is_dynamic_field = isset( $settings['gloo_interactor_conditions'][ $index ]['__dynamic__']['gloo_interactor_condition_field'] );
			$is_empty_field   = empty( $settings['gloo_interactor_conditions'][ $index ]['gloo_interactor_condition_field'] );

			$args['field_raw'] = ( ! $is_dynamic_field && ! $is_empty_field ) ? $settings['gloo_interactor_conditions'][ $index ]['gloo_interactor_condition_field'] : null;

			if ( empty( $args['condition'] ) ) {
				continue;
			}

			$condition          = $args['condition'];
			$condition_instance = $this->conditions->get_condition( $condition );

			if ( ! $condition_instance ) {
				continue;
			}

			if ( ! $has_conditions ) {
				$has_conditions = true;
			}

			$check = $condition_instance->check( $args );

			if ( 'show' === $type ) {
				if ( $is_or_relation ) {
					if ( $check ) {
						return true;
					}
				} elseif ( ! $check ) {
					return false;
				}
			} else {
				if ( $is_or_relation ) {
					if ( ! $check ) {
						return false;
					}
				} elseif ( $check ) {
					return true;
				}
			}
		}

		if ( ! $has_conditions ) {
			return $result;
		}

		$result = ( 'show' === $type ) ? ! $is_or_relation : $is_or_relation;

		return $result;
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
