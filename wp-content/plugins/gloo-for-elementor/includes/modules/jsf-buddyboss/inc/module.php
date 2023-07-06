<?php

namespace Gloo\Modules\JSF_Buddyboss;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	public $control_overrides = [];

	public $slug = 'jsf_buddyboss';

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

		add_filter( 'jet-engine/listing/grid/posts-query-args', [ $this, 'je_grid_post_query_args' ], 90, 2 );

	}

	public function je_grid_post_query_args( $args = array(), $widget ) {

		// bail early
		if ( ! isset( $args['meta_query'] ) || ! $args['meta_query'] ) {
			return $args;
		}


		$type                = '';
		$xprofile_query_args = [];


		foreach ( $args['meta_query'] as $meta_query ) {

			if(!isset($meta_query['key'])){
				continue;
			}
			
			// check if it contains our bp_field_ prefix
			if ( strpos( $meta_query['key'], 'bp_field_' ) === false ) {
				continue;
			}

			$field = str_replace( 'bp_field_', '', $meta_query['key'] );
			if ( strpos( $field, 'range_field_' ) !== false ) {
				$type  = 'range';
				$field = str_replace( 'range_field_', '', $field );
			}
			if ( strpos( $field, 'serialized_field_' ) !== false ) {
				$type  = 'serialized';
				$field = str_replace( 'serialized_field_', '', $field );
			}
			if ( strpos( $field, 'search_field_' ) !== false ) {
				$type  = 'search';
				$field = str_replace( 'search_field_', '', $field );
			}

			switch ( $type ) {
				case 'serialized' :
					if ( is_array( $meta_query['value'] ) ) {
						foreach ( $meta_query['value'] as $value ) {
							$xprofile_query_args[] = [
								'field'   => intval( $field ),
								'compare' => 'LIKE',
								'value'   => serialize( $value ),
							];
						}
					} else {
						$xprofile_query_args[] = [
							'field'   => intval( $field ),
							'compare' => 'LIKE',
							'value'   => serialize( $meta_query['value'] ),
						];
					}
					break;
				case 'search' :
					$xprofile_query_args[] = [
						'field'   => intval( $field ),
						'compare' => 'LIKE',
						'value'   => $meta_query['value'],
					];
					break;
				case 'range' :
					if ( isset( $meta_query['value'][0] ) && isset( $meta_query['value'][1] ) ) {
						$xprofile_query_args[] = [
							'field'   => intval( $field ),
							'compare' => '>=',
							'value'   => $meta_query['value'][0],
						];
						$xprofile_query_args[] = [
							'field'   => intval( $field ),
							'compare' => '<=',
							'value'   => $meta_query['value'][1],
						];
					}
					break;
				default:
					if ( is_array( $meta_query['value'] ) ) {
						foreach ( $meta_query['value'] as $value ) {
							$xprofile_query_args[] = [
								'field'   => intval( $field ),
								'compare' => $meta_query['compare'],
								'value'   => $value,
							];
						}
					} else {
						$xprofile_query_args[] = [
							'field'   => intval( $field ),
							'compare' => $meta_query['compare'],
							'value'   => $meta_query['value'],
						];
					}
					break;
			}
		}

		if ( ! $xprofile_query_args ) {
			return $args;
		}

		// relation
		$xprofile_query_args['relation'] = 'AND';
		$user_query                      = new \BP_User_Query( [
			'per_page'       => 0, // get all
			'xprofile_query' => $xprofile_query_args
		] );


		if ( $user_query->results ) {
			$user_ids = $user_query->user_ids;
			if ( $user_ids ) {
				$include = $user_ids;
				if ( isset( $args['include'] ) && $args['include'] ) {
					$include = array_intersect( $args['include'], $user_ids ) ? array_intersect( $args['include'], $user_ids ) : PHP_INT_MAX;
				}
				$args['include'] = $include;
				unset( $args['meta_query'] );
			}
		}


		return $args;
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